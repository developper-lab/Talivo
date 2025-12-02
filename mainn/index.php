<?php
session_start();
include 'db.php';
include 'php/main/header.php';
$sql = "SELECT * FROM posts";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlS = "SELECT * FROM service_requests";
$stmtS = $pdo->prepare($sqlS);
$stmtS->execute();
$sellers = $stmtS->fetchAll(PDO::FETCH_ASSOC);
?>
<main>
    <div class="slider">
        <div class="slider-track">
            <div class="slide"><img src="/image/image1.png" alt="Реклама 1"></div>
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
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>

            <?php foreach ($cards as $card): ?>

                <div class="card">
                    <div class="card-img">
                        <img src="php/uploads/<?php echo htmlspecialchars($card['image']) ?>" alt="Товар">
                    </div>
                    <div class="card-body">
                        <span class="price"><?php echo htmlspecialchars($card['price']) ?>р</span>
                        <p class="title"><?php echo htmlspecialchars($card['title']) ?></p>
                        <div class="rating">
                            <span class="star">★</span>
                            <span class="rate"><?php echo htmlspecialchars($card['rating']) ?></span>
                            <span class="count"><?php echo htmlspecialchars($card['count']) ?> оценок</span>
                        </div>
                        <button class="btn" onclick="window.location.href='php/user/product.php?id=<?php echo htmlspecialchars($card['id']) ?>'">Посмотреть товар</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'seller'): ?>

            <?php foreach ($sellers as $seller): ?>

                <div class="card">
                    <div class="card-img">
                        <img src="php/uploads/<?php echo htmlspecialchars($seller['image'] ?? '') ?>" alt="Товар">

                    </div>
                    <div class="card-body">
                        <span class="price"><?php echo htmlspecialchars($seller['price']) ?>р</span>
                        <p class="title"><?php echo htmlspecialchars($seller['title']) ?></p>
                        <p class="descriptionUs"><?php echo htmlspecialchars($seller['description']) ?></p>
                        <button class="btn" onclick="window.location.href='php/message/ChatSeller.php?client_id=<?= htmlspecialchars($seller['user_id']) ?>'">
                            Предложить услугу
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($cards as $card): ?>

                <div class="card">
                    <div class="card-img">
                        <img src="php/uploads/<?php echo htmlspecialchars($card['image']) ?>" alt="Товар">
                    </div>
                    <div class="card-body">
                        <span class="price"><?php echo htmlspecialchars($card['price']) ?>р</span>
                        <p class="title"><?php echo htmlspecialchars($card['title']) ?></p>
                        <div class="rating">
                            <span class="star">★</span>
                            <span class="rate"><?php echo htmlspecialchars($card['rating']) ?></span>
                            <span class="count"><?php echo htmlspecialchars($card['count']) ?> оценок</span>
                        </div>
                        <button class="btn" onclick="window.location.href='php/user/product.php?id=<?php echo htmlspecialchars($card['id']) ?>'">Посмотреть товар</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</main>

<?php
include 'php/main/footer.php'
?>