<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

echo " [*] Воркер ЧЕКОВ запущен и ждет подтверждения заказов...\n";

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'root');
$channel = $connection->channel();

// 1. Указываем имя обменника (ДОЛЖНО БЫТЬ ТАКИМ ЖЕ, как в RabbitService)
$exchange = 'shop.events'; // Поменяй на имя своего exchange!

// 2. Объявляем СВОЮ СОБСТВЕННУЮ очередь для чеков
$queueName = 'worker_receipt_queue';
$channel->queue_declare($queueName, false, true, false, false, false, new AMQPTable(['x-queue-type' => 'quorum']));

// 3. САМЫЙ ВАЖНЫЙ ШАГ: Связываем нашу очередь с обменником по новому ключу!
// Мы говорим Кролику: "Если в $exchange прилетит ключ 'order.confirmed', дай копию в эту очередь"
$channel->queue_bind($queueName, $exchange, 'order.confirmed');


// 4. Пишем логику обработки данных чека
$callback = function (AMQPMessage $msg) 
{
    echo " [x] Начинаю генерацию чека...\n";
    
    // Получаем те самые данные, которые отправил worker_db!
    $data = json_decode($msg->getBody(), true);
    
    print_r($data); // Посмотри в консоли, тут будет order_id, total_sum и т.д.
    
    try {
        $orderId  = $data['order_id'];
        $totalSum = $data['total_sum'];
        
        // --- ТВОЯ ЛОГИКА ГЕНЕРАЦИИ ЧЕКА ---
        // Например: генерируешь PDF, отправляешь данные в ОФД или пишешь лог в файл чеков:
        $logText = date('[Y-m-d H:i:s]') . " Сгенерирован чек для заказа #{$orderId} на сумму {$totalSum} руб.\n";
        file_put_contents(__DIR__ . '/../logs/receipts.log', $logText, FILE_APPEND);
        
        echo " [v] Чек для заказа #{$orderId} успешно создан!\n";
        
        // Подтверждаем Кролику, что чек готов
        $msg->ack();
        
    } catch (Exception $e) 
    {
        echo " [X] Ошибка воркера чеков: " . $e->getMessage() . "\n";
        $msg->nack(false, false, true);
    }
};

// 5. Запускаем прослушивание
$channel->basic_qos(null, 1, false);
$channel->basic_consume($queueName, '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo "Воркер чеков остановлен: " . $exception->getMessage() . "\n";
}

$channel->close();
$connection->close();