<?php
session_start();
require '../db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$username || !$email || !$password) {
        die('Все поля должны быть заполнены.');
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die('Пользователь с таким email уже существует.');
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

        $mail->setFrom('uzvenko27@gmail.com', 'ЭЛИТДОМ');
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
        echo 'Регистрация прошла успешно! Проверьте почту для активации. <a href="../index.php">Домой</a>';
    } catch (Exception $e) {
        echo 'Ошибка при отправке письма: ' . $mail->ErrorInfo;
    }
} else {
    echo 'Метод не разрешен.';
}
