<?php
session_start();
require '../../db.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$partner_id = intval($_GET['partner_id'] ?? 0);

if (!$partner_id) exit;

$stmt = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM message m
    LEFT JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = :uid AND m.receiver_id = :pid)
       OR (m.sender_id = :pid AND m.receiver_id = :uid)
    ORDER BY m.created_at ASC
");
$stmt->execute(['uid' => $user_id, 'pid' => $partner_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);
