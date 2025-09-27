<?php
session_start();
include 'db.php';
include 'php/header.php';
$sql = "SELECT * FROM cards";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<main>
    <div class="slider">
        <div class="slider-track">
            <div class="slide"><img src="/image/image.png" alt="Реклама 1"></div>
            <div class="slide"><img src="/image/1.png" alt="Реклама 2"></div>
            <div class="slide"><img src="/image/image.png" alt="Реклама 3"></div>
            <div class="slide"><img src="/image/1.png" alt="Реклама 4"></div>
            <div class="slide"><img src="/image/image.png" alt="Реклама 1"></div>
            <div class="slide"><img src="/image/1.png" alt="Реклама 2"></div>
            <div class="slide"><img src="/image/image.png" alt="Реклама 3"></div>
            <div class="slide"><img src="/image/1.png" alt="Реклама 4"></div>
        </div>
        <button class="slider-btn prev">&#10094;</button>
        <button class="slider-btn next">&#10095;</button>
        <div class="dots"></div>
    </div>
    <div class="cards">
        <?php foreach ($cards as $card): ?>

            <div class="card">
                <div class="card-img">
                    <img src="<?php echo htmlspecialchars($card['image']) ?>" alt="Товар">
                </div>
                <div class="card-body">
                    <span class="price"><?php echo htmlspecialchars($card['price']) ?>р</span>
                    <p class="title"><?php echo htmlspecialchars($card['title']) ?></p>
                    <div class="rating">
                        <span class="star">★</span>
                        <span class="rate"><?php echo htmlspecialchars($card['rating']) ?></span>
                        <span class="count"><?php echo htmlspecialchars($card['count']) ?> оценок</span>
                    </div>
                    <button class="btn" onclick="window.location.href='php/product.php?id=<?php echo htmlspecialchars($card['id']) ?>'">Заказать</button>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</main>
<?php
include 'php/footer.php'
?>