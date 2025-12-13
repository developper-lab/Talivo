<?php
session_start();
include "../db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Сначала войдите в аккаунт'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$postId = $_POST['post_id'] ?? null;

if (!$postId) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Нет товара'
    ]);
    exit;
}

$check = $pdo->prepare(
    "SELECT id FROM basket WHERE user_id = ? AND post_id = ?"
);
$check->execute([$userId, $postId]);

if ($check->fetch()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Товар уже в корзине'
    ]);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO basket (user_id, post_id) VALUES (?, ?)"
);
$stmt->execute([$userId, $postId]);

$count = $pdo->prepare("SELECT COUNT(*) FROM basket WHERE user_id = ?");
$count->execute([$userId]);
$totalCount = $count->fetchColumn();

echo json_encode([
    'status' => 'ok',
    'count' => $totalCount
]);
