<?php
session_start();
include '../../db.php';
include '../main/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        b.id AS basket_id,
        p.id,
        p.title,
        p.price,
        p.image
    FROM basket b
    JOIN posts p ON p.id = b.post_id
    WHERE b.user_id = ?
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = array_sum(array_column($items, 'price'));
?>
<link rel="stylesheet" href="<?= BASE_URL ?>styles/basket.css">

<h2>Корзина</h2>

<?php if (!$items): ?>
    <p>Корзина пустая 😢</p>
<?php else: ?>
    <div class="basket">
        <?php foreach ($items as $item): ?>
            <div class="basket-item">
                <img src="../uploads/<?= htmlspecialchars($item['image']) ?>">
                <div>
                    <p><?= htmlspecialchars($item['title']) ?></p>
                    <strong><?= $item['price'] ?> BYN</strong>
                </div>
                <button class="remove" data-id="<?= $item['basket_id'] ?>">✕</button>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="basket-total">
        <span>Итого</span>
        <strong><?= $total ?> BYN</strong>
    </div>
    <div class="basket-footer">
        <button id="checkout-btn" class="btn checkout">Оформить заказ</button>
    </div>

<?php endif; ?>

<script>
    document.querySelectorAll('.remove').forEach(btn => {
        btn.addEventListener('click', () => {
            fetch('../../ajax/remove_from_basket.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + btn.dataset.id
                })
                .then(res => res.json())
                .then(() => location.reload());
        });
    });
    document.getElementById('checkout-btn').addEventListener('click', () => {
        window.location.href = 'checkout.php';
    });
</script>

<?php include '../main/footer.php'; ?>