<?php
require 'vendor/autoload.php';
use Meilisearch\Client;

try {
    // ВАЖНО: используем имя сервиса из docker-compose, а не localhost
    $client = new Client('http://meilisearch:7700', 'asdasd123');
    
    // Проверяем версию, чтобы понять, что соединение установлено
    $version = $client->version();
    echo "Meilisearch работает! Версия: " . $version['pkgVersion'];
} catch (Exception $e) {
    echo "Ошибка соединения: " . $e->getMessage();
}