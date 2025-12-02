<?php
require '../db.php';

$code = $_GET['code'] ?? '';
if (!$code) {
    die('Код активации не указан.');
}


$stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_code = NULL WHERE activation_code = ?");
$stmt->execute([$code]);

if ($stmt->rowCount()) {
    echo 'Аккаунт успешно активирован! <a href="../index.php">Войти</a>';
} else {
    echo 'Неверный код активации или аккаунт уже активирован.';
}
