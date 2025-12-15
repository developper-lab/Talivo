<?php
session_start();
include '../../db.php';
include '../main/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>styles/account.css">

<main class="account-page">

    <?php
    if (isset($_SESSION['user_id'])):

        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT username, is_active FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            session_destroy();
            header("Location: index.php");
            exit;
        }

        $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_posts,
            COALESCE(SUM(views),0) AS total_views,
            MAX(views) AS max_views
        FROM posts
        WHERE user_id = ?
    ");
        $stmt->execute([$user_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
        SELECT id, title, price, image, views 
        FROM posts 
        WHERE user_id = ?
    ");
        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($user['is_active']):
    ?>

            <section class="profile-header">
                <div class="profile-info">
                    <div class="avatar">
                        <?= strtoupper(mb_substr($user['username'], 0, 1)) ?>
                    </div>
                    <div>
                        <h1><?= htmlspecialchars($user['username']) ?></h1>
                        <span class="status active">Аккаунт активен</span>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="<?= BASE_URL ?>php/Post/createPost.php" class="btn primary">+ Создать объявление</a>
                    <a href="stats.php" class="btn outline">📊 Статистика</a>
                    <a href="logout.php" class="btn danger">Выйти</a>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div>
                        <h3><?= $stats['total_posts'] ?></h3>
                        <p>Объявлений</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">👁</div>
                    <div>
                        <h3><?= $stats['total_views'] ?></h3>
                        <p>Просмотров</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🔥</div>
                    <div>
                        <h3><?= $stats['max_views'] ?? 0 ?></h3>
                        <p>Максимум</p>
                    </div>
                </div>
            </section>

            <section class="user-items">
                <h2 class="section-title">Ваши объявления</h2>

                <?php if (count($items) > 0): ?>
                    <div class="items-grid">
                        <?php foreach ($items as $item): ?>
                            <div class="item-card">
                                <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" class="item-img">

                                <div class="item-body">
                                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                                    <p class="price"><?= htmlspecialchars($item['price']) ?> BYN</p>
                                    <p class="views">👁 <?= $item['views'] ?> просмотров</p>
                                </div>

                                <div class="item-actions">
                                    <a href="<?= BASE_URL ?>php/Post/editPost.php?id=<?= $item['id'] ?>" class="btn outline">
                                        Редактировать
                                    </a>
                                    <a href="<?= BASE_URL ?>php/Post/deletePost.php?id=<?= $item['id'] ?>" class="btn danger">
                                        Удалить
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>У вас пока нет объявлений</p>
                        <a href="<?= BASE_URL ?>php/Post/createPost.php" class="btn primary">
                            Создать первое объявление
                        </a>
                    </div>
                <?php endif; ?>
            </section>

        <?php
        else:
        ?>
            <div class="not-active">
                <p>Ваш аккаунт ещё не активирован. Проверьте почту.</p>
                <a href="../../seting/send_activation.php" class="btn primary">
                    Выслать код снова
                </a>
            </div>

        <?php
        endif;
    else:
        ?>

        <div class="auth-wrapper">

            <div class="auth-tabs">
                <button class="tab-btn active" data-tab="login">Вход</button>
                <button class="tab-btn" data-tab="register">Регистрация</button>
            </div>

            <!-- ВХОД -->
            <form action="login.php" method="post" class="auth-form tab-content active" id="login">
                <label class="label_form">Email:
                    <input type="email" name="email" required class="input_form">
                </label>

                <label class="label_form">Пароль:
                    <input type="password" name="password" required class="input_form">
                </label>

                <button type="submit" class="btn">Войти</button>
            </form>

            <!-- РЕГИСТРАЦИЯ -->
            <form action="register.php" method="post" class="auth-form tab-content" id="register">
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

    <?php endif; ?>

</main>

<script>
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });
</script>
<?php include '../main/footer.php'; ?>