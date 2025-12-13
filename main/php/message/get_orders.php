<?php
session_start();
require '../../db.php';

if (!isset($_SESSION['user_id'])) exit;

$sellerId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT o.*, p.title, u.username AS buyer_name
    FROM orders o
    JOIN posts p ON o.post_id = p.id
    JOIN users u ON o.buyer_id = u.id
    WHERE o.seller_id = ?
    ORDER BY o.created_at ASC
");
$stmt->execute([$sellerId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($orders);
