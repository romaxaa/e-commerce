<?php
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    //указываем название docker контейнера который находиться в docker-compose.yml
    require_once __DIR__.'/rb.php';
    R::setup('mysql:host=mysql; dbname=ecommerce','user', 'root'); 
    if(!R::testConnection()) die('No DB connection!');
?>