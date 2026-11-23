<?php
// classes/Database.php - koneksi PDO dengan mode ERRMODE_EXCEPTION
// Pengaturan koneksi dibaca dari config/config.php

class Database
{
    private $pdo;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';
        $db = $config['db'];

        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";

        $this->pdo = new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}