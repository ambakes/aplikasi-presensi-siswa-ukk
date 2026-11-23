<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function cariUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_user, username, password FROM users WHERE username = :username'
        );
        $stmt->execute(['username' => $username]);
        $akun = $stmt->fetch(PDO::FETCH_ASSOC);

        return $akun === false ? null : $akun;
    }
}
