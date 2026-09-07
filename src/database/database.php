<?php

declare(strict_types=1);

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $dbName = $_ENV['DB_NAME'] ?? 'Muebleria';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';
    $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbName, $charset);

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $pdo;
    } catch (PDOException $exception) {
        throw new RuntimeException(
            sprintf(
                'No se pudo conectar a la base de datos %s en %s:%s. %s',
                $dbName,
                $host,
                $port,
                $exception->getMessage(),
            ),
            0,
            $exception,
        );
    }
}

function getDBWithoutDatabase(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';
    $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

    $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $host, $port, $charset);

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $pdo;
    } catch (PDOException $exception) {
        throw new RuntimeException(
            sprintf(
                'No se pudo conectar a MySQL en %s:%s. %s',
                $host,
                $port,
                $exception->getMessage(),
            ),
            0,
            $exception,
        );
    }
}

class Database
{
    public function getConnection(): PDO
    {
        return getDB();
    }

    public function getConnectionWithoutDatabase(): PDO
    {
        return getDBWithoutDatabase();
    }

    /**
     * @param callable(PDO): mixed $transaction
     * @return mixed
     */
    public function runTransaction(callable $transaction)
    {
        $connection = $this->getConnection();

        if ($connection->inTransaction()) {
            throw new RuntimeException('No se pueden anidar transacciones.');
        }

        try {
            $connection->beginTransaction();
            $result = $transaction($connection);
            $connection->commit();

            return $result;
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }
}
