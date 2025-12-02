<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        die('Введите email и пароль.');
    }


    $stmt = $pdo->prepare("SELECT id, username, password, is_active FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('Пользователь не найден.');
    }




    if (!password_verify($password, $user['password'])) {
        die('Неверный пароль.');
    }


    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = 'user';
    header("Location: user/account.php");
    exit;
} else {
    http_response_code(405);
    echo 'Метод не разрешен.';
}
