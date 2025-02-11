<?php
try {
    $db = new PDO(
        'mysql:host=localhost;dbname=blog;charset=utf8mb4',
        'your_database_user',
        'your_database_password',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
} 