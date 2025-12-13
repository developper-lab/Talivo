<?php
session_start();
if (!isset($pdo)) {
    include __DIR__ . "/../../db.php";
}
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/users/login.php");
    exit;
}

$buyerId = $_SESSION['user_id'];
$userName = $_SESSION['username'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$delivery = $_POST['delivery'] ?? '';
$payment = $_POST['payment'] ?? '';

if (!$phone || !$address || !$delivery || !$payment) {
    die('Заполните все поля формы!');
}

$stmt = $pdo->prepare("SELECT post_id FROM basket WHERE user_id = ?");
$stmt->execute([$buyerId]);
$postIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$postIds) {
    die('Корзина пуста!');
}

$orderNumbers = [];

foreach ($postIds as $postId) {
    $stmt = $pdo->prepare("SELECT user_id, title FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $sellerId = $row['user_id'];
    $title = $row['title'];

    $stmt = $pdo->prepare("
        INSERT INTO orders (buyer_id, seller_id, post_id, phone, address, delivery, payment, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$buyerId, $sellerId, $postId, $phone, $address, $delivery, $payment]);
    $orderId = $pdo->lastInsertId();

    $paymentTypes = [
        'card' => 'Карта',
        'cash' => 'Наличные',
        'online' => 'Онлайн-оплата'
    ];

    $deliveryTypes = [
        'mail' => 'Почта',
        'courier' => 'Курьер',
        'pickup' => 'Самовывоз'
    ];

    $paymentName = $paymentTypes[$payment] ?? $payment;
    $deliveryName = $deliveryTypes[$delivery] ?? $delivery;

    $messageText = "Хочу купить товар: $title\nОплата: $paymentName\nДоставка: $deliveryName\nАдрес: $address\nТелефон: $phone";
    $stmt = $pdo->prepare("INSERT INTO message (sender_id, receiver_id, message, type, order_id) VALUES (?, ?, ?, 'order', ?)");
    $stmt->execute([$buyerId, $sellerId, $messageText, $orderId]);

    $orderNumbers[] = $orderId;
}




$pdo->prepare("DELETE FROM basket WHERE user_id = ?")->execute([$buyerId]);

header("Location: success.php?order_id=" . end($orderNumbers));
exit;
