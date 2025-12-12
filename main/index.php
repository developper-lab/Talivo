<?php
session_start();
include 'db.php';
include 'php/main/header.php';

$currentUser = $_SESSION['user_id'] ?? null;

// Если юзер авторизован — не показываем его товары
if ($currentUser) {
    $sql = "SELECT * FROM posts 
            WHERE user_id != :uid 
            ORDER BY rating DESC, count DESC 
            LIMIT 8";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['uid' => $currentUser]);
} else {
    // Если гость — показываем всё как раньше
    $sql = "SELECT * FROM posts 
            ORDER BY rating DESC, count DESC 
            LIMIT 8";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="main">
    <section class="promo-section">
        <div class="promo-container">
            <div class="promo-content">
                <div class="top">
                    <h1 class="promo-title">Всегда качественные продукты</h1>
                    <p class="promo-description">Только качественные товары, за которыми мы всегда следим</p>
                </div>
                <a href="#" class="promo-button">Подробнее</a>
            </div>
            <div class="promo-image">
                <img src="images/promo_image.png" alt="Доставка молочных продуктов">
            </div>
        </div>
    </section>

    <section class="popular">
        <div class="popular__title">
            <span>Популярное</span>
            <span class="arrow">›</span>
        </div>

        <div class="cards">
            <?php foreach ($cards as $card): ?>
                <div class="card">
                    <div class="card-img">
                        <img src="php/uploads/<?php echo htmlspecialchars($card['image']) ?>" alt="Товар">
                    </div>
                    <div class="card-body">
                        <span class="price">
                            <?php echo htmlspecialchars($card['price']) ?> BYN
                        </span>
                        <p class="title">
                            <?php echo htmlspecialchars($card['title']) ?>
                        </p>
                        <div class="rating">
                            <span class="star">★</span>
                            <span class="rate">
                                <?php echo htmlspecialchars($card['rating']) ?>
                            </span>
                            <span class="count">
                                (отзывов: <?php echo htmlspecialchars($card['count']) ?>)
                            </span>
                        </div>
                        <button class="btn" type="button" onclick="window.location.href='php/Post/post.php?id=<?php echo htmlspecialchars($card['id']) ?>'">
                            Посмотреть товар
                        </button>
                        <button class="btn_add" type="button" id="basket">
                            Добавить в корзину
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="categories-section">
        <h2 class="categories-title">Категории</h2>

        <div class="category-cards">
            <a href="php/catalog.php?category=jewelry" class="category-card">
                <div class="cat-icon">💍</div>
                <span>Украшения и аксессуары</span>
            </a>

            <a href="php/catalog.php?category=clothes" class="category-card">
                <div class="cat-icon">👗</div>
                <span>Одежда и текстиль</span>
            </a>

            <a href="php/catalog.php?category=decor" class="category-card">
                <div class="cat-icon">🏠</div>
                <span>Домашний декор</span>
            </a>

            <a href="php/catalog.php?category=wood" class="category-card">
                <div class="cat-icon">🪵</div>
                <span>Деревянные изделия</span>
            </a>

            <a href="php/catalog.php?category=ceramics" class="category-card">
                <div class="cat-icon">🏺</div>
                <span>Керамика и глина</span>
            </a>

            <a href="php/catalog.php?category=art" class="category-card">
                <div class="cat-icon">🎨</div>
                <span>Картины и арт-объекты</span>
            </a>

            <a href="php/catalog.php?category=cosmetics" class="category-card">
                <div class="cat-icon">🧴</div>
                <span>Косметика ручной работы</span>
            </a>

            <a href="php/catalog.php?category=food" class="category-card">
                <div class="cat-icon">🍪</div>
                <span>Еда и выпечка</span>
            </a>

            <a href="php/catalog.php?category=gifts" class="category-card">
                <div class="cat-icon">🎁</div>
                <span>Подарочные наборы</span>
            </a>

            <a href="php/catalog.php?category=tools" class="category-card">
                <div class="cat-icon">🛠️</div>
                <span>Инструменты и материалы</span>
            </a>
        </div>
    </section>

</main>
<?php
include 'php/main/footer.php';
?>