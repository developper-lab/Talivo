<?php
session_start();
include '../../db.php';
include '../main/header.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username, is_active FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['is_active']) {
            echo '<h1>Добро пожаловать, <?php echo htmlspecialchars($_SESSION["surname"]); ?>!</h1>';
            echo '<a href="../logout.php">Выйти</a>';
        } else {
            echo "<p>Ваш аккаунт ещё не активирован. Проверьте почту.</p>";
            echo '<a href="../../seting/send_activation.php">Выслать код снова</a>';
        }
    } else {
        session_destroy();
        header("Location: index.php");
        exit;
    }
} else {
    echo '
    <h2>Авторизация</h2>
    <form action="../login.php" method="post">
        Email: <input type="email" name="email" required><br>
        Пароль: <input type="password" name="password" required><br>
        <button type="submit">Войти</button>
    </form>

    <h2>Регистрация</h2>
    <form action="../register.php" method="post">
        Имя: <input type="text" name="username" required><br>
        Email: <input type="email" name="email" required><br>
        Пароль: <input type="password" name="password" required><br>
        <button type="submit">Зарегистрироваться</button>
    </form>
    ';
}

include '../main/footer.php';
