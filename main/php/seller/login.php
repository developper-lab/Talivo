<?php
session_start();
include '../../db.php'; // Подключение к PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Находим пользователя
    $stmt = $pdo->prepare("SELECT * FROM selers WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Авторизация успешна
        $_SESSION['seller_id'] = $user['id'];
        $_SESSION['role'] = 'seller';
        $_SESSION['name'] = $user['name'];
        $_SESSION['category'] = $user['category'];

        // Перенаправляем на страницу продавца
        header("Location: accountSeller.php");
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
?>

<h2>Вход</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Войти</button>
    <a href="registration.php">нет аккаунта?</a>
</form>

<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>