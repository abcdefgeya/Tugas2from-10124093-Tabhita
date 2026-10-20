<?php

$db_host = "127.0.0.1";
$db_nama = "booknest";
$db_user = "root";
$db_pass = "";

$dsn = "mysql:host=".$db_host.";dbname=".$db_nama.";charset=utf8mb4";

$pdo = new PDO($dsn, $db_user, $db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);