<?php
session_start();
require '../../db.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$partner_id = intval($_GET['partner_id'] ?? 0);
if (!$partner_id) exit;

$stmt = $pdo->prepare("
    SELECT 
        m.*, 
        u.username AS sender_name,
        o.id AS order_id,
        o.seller_id,
        o.status,
        IF(o.id IS NOT NULL, 'order', 'message') AS type
    FROM message m
    LEFT JOIN users u ON m.sender_id = u.id
    LEFT JOIN orders o ON o.id = m.order_id
    WHERE (m.sender_id = :uid AND m.receiver_id = :pid)
       OR (m.sender_id = :pid AND m.receiver_id = :uid)
    ORDER BY m.created_at ASC
");
$stmt->execute(['uid' => $user_id, 'pid' => $partner_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdo->prepare("
    UPDATE message 
    SET read_at = NOW() 
    WHERE sender_id = ? 
      AND receiver_id = ? 
      AND read_at IS NULL
")->execute([$partner_id, $user_id]);

header('Content-Type: application/json');
echo json_encode($messages);
