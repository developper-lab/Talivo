<?php
$host = 'localhost:3306';
$dbname = 'tal';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('SET NAMES utf8mb4');
} catch (PDOException $e) {
    die("Ошибка подключения" . $e->getMessage());
}
