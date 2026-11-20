<?php
/**
 * includes/db.php — Database Connection Helper (PDO)
 * Connects to MySQL for local XAMPP / Apache environments.
 * Safe fallback if database is not yet running or configured.
 */

function get_db_connection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $host     = getenv('DB_HOST') ?: '127.0.0.1';
    $port     = getenv('DB_PORT') ?: '3306';
    $dbname   = getenv('DB_NAME') ?: 'creativekit3a';
    $user     = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $charset  = 'utf8mb4';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 2,
    ];

    try {
        $pdo = new PDO($dsn, $user, $password, $options);
        return $pdo;
    } catch (\Throwable $e) {
        // Return null so callers can gracefully fallback without crashing
        return null;
    }
}
