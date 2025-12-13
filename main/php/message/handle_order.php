<?php
session_start();
require '../../db.php';

if (!isset($_SESSION['user_id'])) exit;

$sellerId = $_SESSION['user_id'];
$orderId = intval($_POST['order_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$orderId || !in_array($action, ['accept', 'reject'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$status = $action === 'accept' ? 'accepted' : 'rejected';
$stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=? AND seller_id=?");
$stmt->execute([$status, $orderId, $sellerId]);

header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;
