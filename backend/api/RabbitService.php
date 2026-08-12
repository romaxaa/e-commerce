<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitService 
{
    private $connection;
    private $channel;
    private $exchange = 'shop.events';

    public function __construct() 
    {
        // 1. Подключаемся
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'root');
        $this->channel = $this->connection->channel();

        // 2. Объявляем ОБМЕННИК (тип direct)
        $this->channel->exchange_declare($this->exchange, 'direct', false, true, false);

        // 3. Объявляем ОЧЕРЕДИ
        $this->channel->queue_declare('worker_db', false, true, false, false, false, new AMQPTable(['x-queue-type' => 'quorum']));
        $this->channel->queue_declare('worker_pdf', false, true, false, false, false, new AMQPTable(['x-queue-type' => 'quorum']));
        $this->channel->queue_declare('worker_email', false, true, false, false, false, new AMQPTable(['x-queue-type' => 'quorum']));

        // 4. СВЯЗЫВАЕМ очереди с обменником по ключу 'order.created'
        // Мы говорим Кролику: "Если пришло событие order.created, скопируй его во ВСЕ эти три очереди"
        $this->channel->queue_bind('worker_db', $this->exchange, 'order.created');
        $this->channel->queue_bind('worker_pdf', $this->exchange, 'order.created');
        $this->channel->queue_bind('worker_email', $this->exchange, 'order.created');
        //нужно здесь убрать обменики worker_pdf и worker_email
    }

    // Метод для публикации любого события
    public function publish(string $routingKey, array $data) 
    {
        $payload = json_encode($data);
        $msg = new AMQPMessage($payload, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT // Делаем сообщение стойким к перезагрузкам
        ]);

        $this->channel->basic_publish($msg, $this->exchange, $routingKey);
    }

    public function __destruct() 
    {
        $this->channel->close();
        $this->connection->close();
    }
}





