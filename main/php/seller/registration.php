<?php
session_start();
include '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $category = $_POST['category'] ?? 'Игрушки';

    $stmt = $pdo->prepare("SELECT * FROM selers WHERE email=?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $error = "Email уже зарегистрирован";
    } else {
        $stmt = $pdo->prepare("INSERT INTO selers (name, email, password, category) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $password, $category])) {
            $_SESSION['seller_id'] = $pdo->lastInsertId();
            $_SESSION['name'] = $name;
            $_SESSION['category'] = $category;
            header("Location: accountSeller.php");
            exit;
        } else {
            $error = "Ошибка регистрации";
        }
    }
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Имя" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Пароль" required>

    <select name="category" required>
        <option value="Игрушки">Игрушки</option>
        <option value="Одежда">Одежда</option>
        <option value="Электроника">Электроника</option>
        <option value="Книги">Книги</option>
    </select>

    <button type="submit">Зарегистрироваться</button>
    <a href="login.php">Есть аккаунт?</a>
</form>

<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>