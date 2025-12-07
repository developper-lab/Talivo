<?php
session_start();
include '../db.php';
include 'main/header.php';
$sql = "SELECT * FROM posts ORDER BY rating DESC, count DESC LIMIT 8";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="<?= BASE_URL ?>styles/catalog.css">

<main class="main catalogs">
    <section class="filter-box">
        <div class="filter-section">
            <h3>Цена, BYN</h3>
            <div class="price-inputs">
                <input type="number" placeholder="От 2">
                <input type="number" placeholder="До 1000">
            </div>
        </div>

        <div class="filter-section">
            <h3>Срок доставки</h3>
            <label class="radio-item">
                <input type="radio" name="delivery">
                <span>Сегодня</span>
            </label>

            <label class="radio-item">
                <input type="radio" name="delivery">
                <span>1–3 дня</span>
            </label>

            <label class="radio-item">
                <input type="radio" name="delivery">
                <span>До 7 дней</span>
            </label>

            <label class="radio-item">
                <input type="radio" name="delivery">
                <span>Любой</span>
            </label>
        </div>


        <div class="filter-section">
            <h3>Цвет</h3>
            <label><span class="dot black"></span>Черный</label>
            <label><span class="dot red"></span>Красный</label>
            <label><span class="dot blue"></span>Синий</label>
            <a href="#" class="more">Еще 52</a>
        </div>

        <div class="filter-section">
            <h3>Вид материала</h3>
            <label><span class="dot silver"></span>Серебро</label>
            <label><span class="dot gold"></span>Золото</label>
            <label><span class="dot wood"></span>Дерево</label>
            <a href="#" class="more">Еще 52</a>
        </div>


        <div class="filter-section">
            <h3>Способ доставки</h3>
            <label><span class="dot green"></span>Курьер</label>
            <label><span class="dot green"></span>Почта</label>
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
                        <img src="uploads/<?php echo htmlspecialchars($card['image']) ?>" alt="Товар">
                    </div>
                    <div class="card-body">
                        <span class="price">
                            <?php echo htmlspecialchars($card['price']) ?>
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
                                <?php echo htmlspecialchars($card['count']) ?>
                            </span>
                        </div>
                        <button class="btn" type="submit" onclick="window.location.href='php/Post/post.php?id=<?php echo htmlspecialchars($card['id']) ?>'">
                            Посмотреть товар
                        </button>
                        <button class="btn_add" type="submit" id="basket">
                            Добавить в корзину
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </section>
</main>

<?php
include "main/footer.php";
