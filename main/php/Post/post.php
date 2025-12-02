<?php
session_start();
define('BASE_PATH', dirname(__DIR__));
include BASE_PATH . '../../db.php';
include BASE_PATH . '/main/header.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = 'SELECT * FROM posts WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$card) {
        echo '<p>Товар не найден</p>';
        exit;
    }
}
?>

<main>
    <?php if ($card): ?>
        <div class="product">
            <div class="product-img">
                <img src="../uploads/<?php echo htmlspecialchars($card['image']); ?>" alt="Товар">
            </div>

            <div class="product-body">
                <h1><?php echo htmlspecialchars($card['title']); ?></h1>
                <span class="price"><?php echo htmlspecialchars($card['price']); ?>p</span>

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

                <a class="bttn" href="../message/Chat.php?user_id=<?= $card['user_id'] ?>">Написать продавцу</a>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php
include BASE_PATH . "/main/footer.php"
?>