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
?>

            <div class="profile-box">
                <h1 class="welcome-title">
                    Добро пожаловать,
                    <?= htmlspecialchars($_SESSION['username']) ?>!
                </h1>

                <nav class="profile-nav">
                    <a class="btn_account" href="<?= BASE_URL ?>php/Post/createPost.php">Создать объявление</a>
                    <a class="btn_account logout" href="logout.php">Выйти</a>
                </nav>
            </div>

        <?php
        } else {
        ?>

            <div class="not-active">
                <p>Ваш аккаунт ещё не активирован. Проверьте почту.</p>
                <a href="../../seting/send_activation.php" class="btn_account">Выслать код снова</a>
            </div>

    <?php
        }
    } else {
        session_destroy();
        header("Location: index.php");
        exit;
    }
} else {
    ?>

    <div class="auth-wrapper">

        <h2>Авторизация</h2>
        <form action="login.php" method="post" class="auth-form">
            <label class="label_form">Email:
                <input type="email" name="email" required class="input_form">
            </label>

            <label class="label_form">Пароль:
                <input type="password" name="password" required class="input_form">
            </label>

            <button type="submit" class="btn">Войти</button>
        </form>

        <h2>Регистрация</h2>
        <form action="register.php" method="post" class="auth-form">
            <label class="label_form">Имя:
                <input type="text" name="username" required class="input_form">
            </label>

            <label class="label_form">Email:
                <input type="email" name="email" required class="input_form">
            </label>

            <label class="label_form">Пароль:
                <input type="password" name="password" required class="input_form">
            </label>

            <button type="submit" class="btn">Зарегистрироваться</button>
        </form>

    </div>

<?php
}

include '../main/footer.php';
