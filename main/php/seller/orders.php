<?php
session_start();
include "../../db.php";

if (!isset($_SESSION['user_id'])) {
    die('Войдите, чтобы смотреть заказы');
}

$sellerId = $_SESSION['user_id'];

if (isset($_POST['order_id'], $_POST['action'])) {
    $status = $_POST['action'] === 'accept' ? 'accepted' : 'rejected';
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND seller_id = ?");
    $stmt->execute([$status, $_POST['order_id'], $sellerId]);
}

$orders = $pdo->prepare("
    SELECT o.*, p.title, u.username AS buyer_name
    FROM orders o
    JOIN posts p ON o.post_id = p.id
    JOIN users u ON o.buyer_id = u.id
    WHERE o.seller_id = ?
    ORDER BY o.created_at DESC
");
$orders->execute([$sellerId]);
$orders = $orders->fetchAll(PDO::FETCH_ASSOC);
include '../main/header.php'
?>

<h2>Заказы на ваши товары</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Товар</th>
        <th>Покупатель</th>
        <th>Телефон</th>
        <th>Адрес</th>
        <th>Доставка</th>
        <th>Оплата</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['title']) ?></td>
            <td><?= htmlspecialchars($order['buyer_name']) ?></td>
            <td><?= htmlspecialchars($order['phone']) ?></td>
            <td><?= htmlspecialchars($order['address']) ?></td>
            <td><?= htmlspecialchars($order['delivery']) ?></td>
            <td><?= htmlspecialchars($order['payment']) ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td>
                <?php if ($order['status'] === 'pending'): ?>
                    <form method="post">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" name="action" value="accept">Принять</button>
                        <button type="submit" name="action" value="reject">Отклонить</button>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
include "../main/footer.php";
