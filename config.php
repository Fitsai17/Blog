<?php
$host = 'localhost';
$db = 'postgres';
$user = 'postgres';
$password = 'weyzer';

$dsn = "pgsql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    die('Підключення до бази даних не вдалося: ' . $e->getMessage());
}
