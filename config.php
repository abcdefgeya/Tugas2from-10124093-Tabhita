<?php
return [
    'name' => 'BookNest', 'theme' => 'booknest', 'table' => 'books',
    'session_name' => 'booknest_session',
    'database' => ['host' => '127.0.0.1', 'port' => 3306, 'name' => 'booknest', 'user' => 'root', 'password' => ''],
    'fields' => ['code' => ['text', 30], 'title' => ['text', 160], 'author' => ['text', 120], 'publisher' => ['text', 120], 'year' => ['number', 2100]],
];
