<?php
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    require_once __DIR__.'/rb.php';
    R::setup('mysql:host=localhost; dbname=commerce','user', 'root'); 
    if(!R::testConnection()) die('No DB connection!');
?>