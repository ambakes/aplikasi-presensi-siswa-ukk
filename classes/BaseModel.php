<?php
abstract class BaseModel
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function setConnection(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    abstract public function getAll(): array;
}
