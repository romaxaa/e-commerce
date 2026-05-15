<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Подключаем автозапуск PhpAmqpLib
require_once __DIR__ . '/../include/rb.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

R::setup('mysql:host=mysql; dbname=ecommerce','user', 'root'); 
if(!R::testConnection()) die('No DB connection!');

echo " [*] Worker DB is start.\n";

// 1. Подключаемся к RabbitMQ
$connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'root');
$channel = $connection->channel();

// 3. Создаем колбэк (инструкцию), которая выполнится при получении сообщения
$callback = function (AMQPMessage $msg) 
{
    echo " [x] Получены данные для БД...\n";
    
    // Декодируем наш JSON назад в PHP-массив
    $data = json_decode($msg->getBody(), true);
    
    if (!$data) 
    {
        echo " [!] Ошибка: Неверный формат JSON\n";
        // Важно: даже если данные плохие, говорим Кролику, что обработали, чтобы удалить их из очереди
        $msg->ack(); 
        return;
    }
    
    try 
    {
        if(empty($data['adress_id']))
        {
            echo json_encode(['result' => 'empty_adress']);
            return;
        }

        $checkCart = R::getAll('SELECT c.user_id, c.product_id, c.count as product_count, p.price FROM cart c INNER JOIN products p ON c.product_id = p.id WHERE c.user_id = ?', [$data['user_id']]);

        if(!empty($checkCart))
        {
            $count = 0;
            $totalSum = 0;
            foreach($checkCart as $item)
            {
                $count += $item['product_count'];
                $totalSum += $item['price'] * $count;
            }
        }
        else
        {
            echo json_encode(['result' => 'empty_cart']);
            return;
        }

        if(isset($checkCart))
        {
            $order = R::dispense('orders');
            $order->user_id = htmlspecialchars((int)$data['user_id']);
            $order->payment_type = htmlspecialchars($data['payment'], ENT_QUOTES);

            $order->total_price = htmlspecialchars((int)$totalSum);
            $order->status = 'paid';
            $order->delivery_type = htmlspecialchars($data['delivery'], ENT_QUOTES);
            $order->adress_id = htmlspecialchars($data['adress_id'], ENT_QUOTES);
            $order->created_at = date("Y-m-d H:i:s");
            $id = R::store($order);

            if(!empty($id))
            {
                $orderitem = R::dispense('orderitem');
                $orderitem->order_id = htmlspecialchars((int)$id);
                foreach($checkCart as $row)
                {
                    $orderitem->product_id = $row['product_id'];
                }
                $orderitem->quantity = $count;
                $orderitem->price = $totalSum;
                $orderitem->config = null;
                $id_items = R::store($orderitem);

                if(!empty($id_items))
                {
                    R::exec('DELETE FROM `cart` WHERE `user_id` = ?', [$data['user_id']]);
                    echo json_encode(['result' => 'good']);
                    return;

                    $msg->ack();
                }

            }
        }
        
        echo " [v] Заказ успешно сохранен в БД!\n";
        
        // Подтверждаем RabbitMQ, что всё прошло успешно. Теперь Кролик удалит сообщение.
        //$msg->ack();
        
    } 
    catch (Exception $e) 
    {
        echo " [X] Ошибка при записи в БД: " . $e->getMessage() . "\n";
        
        // Если база отвалилась, возвращаем сообщение обратно в очередь (requeue = true), 
        // чтобы попробовать обработать его чуть позже
        $msg->nack(false, false, true);
    }
};

// 4. Настраиваем basic_qos (Важно!)
// Говорим Кролику: "Не пихай в этот скрипт больше 1 сообщения за раз, пока он не пришлет ack"
$channel->basic_qos(null, 1, false);

// 5. Начинаем слушать строго очередь 'worker_db'
// Обрати внимание: no_ack теперь false, потому что мы сами управляем подтверждениями через $msg->ack()
$channel->basic_consume('worker_db', '', false, false, false, false, $callback);

// 6. Запускаем бесконечный цикл ожидания
try {
    $channel->consume();
} catch (\Throwable $exception) 
{
    echo "Воркер остановлен: " . $exception->getMessage() . "\n";
}

$channel->close();
$connection->close();