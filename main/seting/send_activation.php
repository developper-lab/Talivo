<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$username || !$password) {
        http_response_code(400);
        echo 'Все поля должны быть заполнены.';
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo 'Пользователь с таким email уже существует.';
        exit;
    }


    $activation_code = bin2hex(random_bytes(16));


    $password_hash = password_hash($password, PASSWORD_DEFAULT);


    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, activation_code) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password_hash, $activation_code]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'uzvenko27@gmail.com';
        $mail->Password   = 'uvdx jnqz kzud mjgo';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('uzvenko27@gmail.com', 'Talivo');
        $mail->addAddress($email, $username);

        $mail->isHTML(true);
        $mail->Subject = 'Активация аккаунта';
        $mail->Body    = "Привет, {$username}!<br>
                         Для активации аккаунта перейдите по ссылке:<br>
                         <a href='http://talivo.local/seting/activate.php?code={$activation_code}'>Активировать аккаунт</a>";
        $mail->AltBody = "Привет, {$username}!\n
                         Для активации аккаунта перейдите по ссылке:\n
                         http://talivo.local/seting/activate.php?code={$activation_code}";

        $mail->send();
        echo 'success';
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Ошибка при отправке письма: ' . $mail->ErrorInfo;
    }
} else {
    http_response_code(405);
    echo 'Метод не разрешен.';
}
