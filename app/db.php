<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'test_crud';
$user = getenv('DB_USER') ?: 'crud_user';
$pass = getenv('DB_PASS') ?: 'crud_pass';

$connect = static function (string $dsn) use ($user, $pass): PDO {
    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
};

$dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = $connect($dsn);
} catch (PDOException $e) {
    // `localhost` can make PDO/MySQL attempt a Unix socket connection.
    // If that socket is missing, retry over TCP to avoid false negatives.
    $isLocalhostSocketError = $host === 'localhost'
        && str_contains($e->getMessage(), '[2002]')
        && str_contains($e->getMessage(), 'No such file or directory');

    if ($isLocalhostSocketError) {
        try {
            $fallbackDsn = "mysql:host=127.0.0.1;port={$port};dbname={$dbName};charset=utf8mb4";
            $pdo = $connect($fallbackDsn);
        } catch (PDOException $fallbackException) {
            $e = $fallbackException;
        }
    }

    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}
