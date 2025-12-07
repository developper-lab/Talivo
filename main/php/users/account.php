<?php
session_start();
include '../../db.php';
include '../main/header.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username, is_active FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare("SELECT id, title, price, image FROM posts WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <div class="user-items">
                <h2 class="section-title">Ваши объявления</h2>

                <?php if (count($items) > 0): ?>
                    <div class="items-grid">
                        <?php foreach ($items as $item): ?>
                            <div class="item-card">
                                <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="" class="item-img">

                                <h3 class="item-title">
                                    <?= htmlspecialchars($item['title']) ?>
                                </h3>

                                <p class="item-price">
                                    <?= htmlspecialchars($item['price']) ?> BYN
                                </p>

                                <a href="<?= BASE_URL ?>php/Post/editPost.php?id=<?= $item['id'] ?>" class="btn_account">
                                    Редактировать
                                </a>

                                <a href="<?= BASE_URL ?>php/Post/deletePost.php?id=<?= $item['id'] ?>" class="btn_account delete">
                                    Удалить
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php else: ?>
                    <p class="empty-msg">У вас пока нет объявлений.</p>
                <?php endif; ?>
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

    <main class="main">
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
    </main>

<?php
}

include '../main/footer.php';
