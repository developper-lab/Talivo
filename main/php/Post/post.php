<?php
session_start();

define('BASE_PATH', dirname(__DIR__));
include BASE_PATH . '../../db.php';

if (!isset($_GET['id'])) {
    die('Объявление не найдено');
}

$id = (int)$_GET['id'];

/* ===== ПОЛУЧАЕМ ОБЪЯВЛЕНИЕ ===== */
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$card = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$card) {
    die('Объявление не найдено');
}

/* ===== УВЕЛИЧИВАЕМ ПРОСМОТРЫ ===== */
/* НЕ увеличиваем, если смотрит владелец */
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $card['user_id']) {
    $stmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
    $stmt->execute([$id]);
}

/* ===== ОБРАБОТКА ОТЗЫВА ===== */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['submit_review']) &&
    isset($_SESSION['user_id'])
) {
    $rating = (int)$_POST['rating'];
    $rating = max(1, min(5, $rating));

    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        INSERT INTO reviews (post_id, user_id, rating, comment)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$id, $user_id, $rating, $comment]);

    $stmt = $pdo->prepare("
        SELECT AVG(rating) avg_rating, COUNT(*) cnt
        FROM reviews WHERE post_id = ?
    ");
    $stmt->execute([$id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        UPDATE posts SET rating = ?, count = ? WHERE id = ?
    ");
    $stmt->execute([
        round($res['avg_rating']),
        $res['cnt'],
        $id
    ]);

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

include BASE_PATH . '/main/header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>styles/post.css">

<main>
    <?php if ($card): ?>

        <div class="product">
            <div class="product-img">
                <img src="../uploads/<?= htmlspecialchars($card['image']) ?>" alt="Товар">
            </div>

            <div class="product-body">
                <h1><?= htmlspecialchars($card['title']) ?></h1>

                <span class="price"><?= htmlspecialchars($card['price']) ?> p</span>

                <p class="meta">
                    Категория: <?= htmlspecialchars($card['category']) ?>
                </p>

                <p class="views">
                    👁 <?= (int)$card['views'] + 1 ?> просмотров
                </p>

                <div class="rating">
                    <span class="star">★</span>
                    <span class="rate"><?= (int)$card['rating'] ?></span>
                    <span class="count"><?= (int)$card['count'] ?> оценок</span>
                </div>

                <p class="description">
                    <?= $card['description']
                        ? htmlspecialchars($card['description'])
                        : 'Описание отсутствует' ?>
                </p>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="post" class="review-form">
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

                        <label>Комментарий:</label>
                        <textarea name="comment" rows="3"></textarea>

                        <button type="submit" name="submit_review">
                            Оставить отзыв
                        </button>
                    </form>
                <?php endif; ?>

                <h3>Отзывы</h3>

                <div class="reviews-carousel">
                    <button class="prev">‹</button>
                    <div class="reviews-wrapper">

                        <?php
                        $stmt = $pdo->prepare("
                    SELECT r.*, u.username
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    WHERE r.post_id = ?
                    ORDER BY r.created_at DESC
                ");
                        $stmt->execute([$id]);
                        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($reviews):
                            foreach ($reviews as $rev):
                        ?>
                                <div class="review-slide">
                                    <strong><?= htmlspecialchars($rev['username']) ?></strong>
                                    <div class="stars">
                                        <?= str_repeat('★', $rev['rating']) ?>
                                        <?= str_repeat('☆', 5 - $rev['rating']) ?>
                                    </div>
                                    <?php if ($rev['comment']): ?>
                                        <p><?= htmlspecialchars($rev['comment']) ?></p>
                                    <?php endif; ?>
                                </div>
                        <?php
                            endforeach;
                        else:
                            echo '<p>Пока нет отзывов.</p>';
                        endif;
                        ?>

                    </div>
                    <button class="next">›</button>
                </div>

                <a class="bttn" href="../message/Chat.php?user_id=<?= $card['user_id'] ?>">
                    Написать продавцу
                </a>
            </div>
        </div>

    <?php endif; ?>
</main>

<script>
    const wrapper = document.querySelector('.reviews-wrapper');
    const slides = document.querySelectorAll('.review-slide');
    let index = 0;

    document.querySelector('.prev').onclick = () => show(index - 1);
    document.querySelector('.next').onclick = () => show(index + 1);

    function show(i) {
        index = (i + slides.length) % slides.length;
        wrapper.style.transform = `translateX(-${index * 100}%)`;
    }
</script>

<?php include BASE_PATH . '/main/footer.php'; ?>