<?php

define("MYSQL_USER", "root");
define("MYSQL_PASSWORD", "root");
define("MYSQL_HOST", "127.0.0.1");
define("MYSQL_DATABASE", "shopping");

$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);

$pdo = new PDO(
    'mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DATABASE,MYSQL_USER,MYSQL_PASSWORD,$options
);
?>