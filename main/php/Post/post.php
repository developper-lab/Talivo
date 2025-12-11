<?php
session_start();
define('BASE_PATH', dirname(__DIR__));
include BASE_PATH . '../../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Получаем карточку
    $sql = 'SELECT * FROM posts WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    // === ОБРАБОТКА ОТЗЫВА ===
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
        $rating = intval($_POST['rating']);
        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;

        $comment = trim($_POST['comment']);
        $user_id = $_SESSION['user_id'];

        if ($rating < 1 || $rating > 5) {
            $error = "Рейтинг должен быть от 1 до 5.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO reviews (post_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$card['id'], $user_id, $rating, $comment]);

            $stmt2 = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as cnt FROM reviews WHERE post_id = ?");
            $stmt2->execute([$card['id']]);
            $res = $stmt2->fetch(PDO::FETCH_ASSOC);

            $stmt3 = $pdo->prepare("UPDATE posts SET rating = ?, count = ? WHERE id = ?");
            $avg_rating = round($res['avg_rating']); // округляем до целого
            $stmt3->execute([$avg_rating, $res['cnt'], $card['id']]);

            // Перезагрузка страницы
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

include BASE_PATH . '/main/header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>styles/post.css">

<main>
    <?php if ($card): ?>
        <div class="product">
            <div class="product-img">
                <img src="../uploads/<?php echo htmlspecialchars($card['image']); ?>" alt="Товар">
            </div>

            <div class="product-body">
                <h1><?php echo htmlspecialchars($card['title']); ?></h1>
                <span class="price"><?php echo htmlspecialchars($card['price']); ?>p</span>
                <p class="meta">Категория: <?= htmlspecialchars($card['category']) ?></p>

                <div class="rating">
                    <span class="star">★</span>
                    <span class="rate"><?php echo htmlspecialchars($card['rating']); ?></span>
                    <span class="count"><?php echo htmlspecialchars($card['count']); ?> оценок</span>
                </div>

                <p class="description">
                    <?php if ($card['description']) {
                        echo htmlspecialchars($card['description']);
                    } else {
                        echo  "Описание отсутствует";
                    } ?>
                </p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="" method="post" class="review-form">
                        <h3>Оставить отзыв</h3>
                        <label>Рейтинг:</label>
                        <select name="rating" required>
                            <option value="">--</option>
                            <option value="1">★</option>
                            <option value="2">★★</option>
                            <option value="3">★★★</option>
                            <option value="4">★★★★</option>
                            <option value="5">★★★★★</option>
                        </select>
                        <br><br>
                        <label>Комментарий:</label><br>
                        <textarea name="comment" rows="3"></textarea><br><br>
                        <button type="submit" name="submit_review">Оставить отзыв</button>
                    </form>
                <?php endif; ?>
                <h3>Отзывы</h3>
                <div class="reviews-carousel">
                    <button class="prev">‹</button>
                    <div class="reviews-wrapper">
                        <?php
                        $stmt = $pdo->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.post_id = ? ORDER BY created_at DESC");
                        $stmt->execute([$card['id']]);
                        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($reviews) {
                            foreach ($reviews as $rev) {
                                echo '<div class="review-slide">';
                                echo '<strong>' . htmlspecialchars($rev['username']) . '</strong>';
                                echo '<div class="stars">' . str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']) . '</div>';
                                if ($rev['comment']) echo '<p>' . htmlspecialchars($rev['comment']) . '</p>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Пока нет отзывов.</p>';
                        }
                        ?>
                    </div>
                    <button class="next">›</button>
                </div>


                <a class="bttn" href="../message/Chat.php?user_id=<?= $card['user_id'] ?>">Написать продавцу</a>
            </div>


        </div>

    <?php endif; ?>
    <script>
        const wrapper = document.querySelector('.reviews-wrapper');
        const slides = document.querySelectorAll('.review-slide');
        const prev = document.querySelector('.prev');
        const next = document.querySelector('.next');
        let index = 0;

        function showSlide(i) {
            if (i < 0) index = slides.length - 1;
            else if (i >= slides.length) index = 0;
            else index = i;
            wrapper.style.transform = `translateX(-${index * 100}%)`;
        }

        prev.addEventListener('click', () => showSlide(index - 1));
        next.addEventListener('click', () => showSlide(index + 1));
    </script>

</main>

<?php
include BASE_PATH . "/main/footer.php"
?>