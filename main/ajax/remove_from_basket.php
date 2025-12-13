<?php
session_start();
include "../db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare(
        "DELETE FROM basket WHERE id = ? AND user_id = ?"
    );
    $stmt->execute([$id, $_SESSION['user_id']]);
}

echo json_encode(['status' => 'ok']);
