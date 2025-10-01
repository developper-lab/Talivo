<?php
session_start();
require '../../db.php';
// Проверяем авторизацию до любого вывода HTML
if (!isset($_SESSION['seller_id'])) {
    header("Location: login.php");
    exit;
}
include '../main/header.php';

// Берём id продавца из сессии
$seller_id = $_SESSION['seller_id'];
// Получаем товары продавца
$stmt = $pdo->prepare("SELECT * FROM posts WHERE seller_id = ?");
$stmt->execute([$seller_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
<p>Ваша категория: <b><?php echo htmlspecialchars($_SESSION['category']); ?></b></p>

<a href="../Post/createPost.php">Добавить товар</a>
<h2>Мои товары</h2>

<?php if ($products): ?>
    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Товар">
                <b><?= htmlspecialchars($product['title']) ?></b> —
                <?= number_format($product['price'], 2, '.', ' ') ?> ₽<br>
                <?= htmlspecialchars($product['description']) ?><br>
                <a href="edit_product.php?id=<?= $product['id'] ?>">Редактировать</a> |
                <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Удалить товар?')">Удалить</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>У вас пока нет товаров.</p>
<?php endif; ?>
<a href="logout.php">Выйти</a>

<?php
include '../main/footer.php';
?>