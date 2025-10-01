<?php
session_start();
require '../../db.php';
// Проверка авторизации пользователя
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем список продавцов, с которыми велась переписка
$stmt = $pdo->prepare("
    SELECT DISTINCT receiver_id 
    FROM messages 
    WHERE sender_id = ?
");
$stmt->execute([$user_id]);
$sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Если выбран продавец, показываем переписку
$seller_id = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 0;

if ($seller_id) {
    $stmt = $pdo->prepare("
    SELECT m.*,
           CASE 
               WHEN m.sender_type = 'user' THEN u.username
               ELSE s.name
           END AS sender_name
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.id AND m.sender_type = 'user'
    LEFT JOIN selers s ON m.sender_id = s.id AND m.sender_type = 'seller'
    WHERE (m.sender_id = ? AND m.receiver_id = ?) 
       OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.created_at ASC
");
    $stmt->execute([$user_id, $seller_id, $seller_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Отправка сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $seller_id) {
    $message = trim($_POST['message'] ?? '');
    if ($message) {
        $stmt = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, sender_type, message)
            VALUES (?, ?, 'user', ?)
        ");
        $stmt->execute([$user_id, $seller_id, $message]);
        header("Location: Chat.php?seller_id=$seller_id");
        exit;
    }
}
include '../main/header.php';




?>

<h1>Чат пользователя</h1>

<h3>Продавцы:</h3>
<ul>
    <?php foreach ($sellers as $seller): ?>
        <li><a href="Chat.php?seller_id=<?= $seller['receiver_id'] ?>">Продавец <?= $seller['receiver_id'] ?></a></li>
    <?php endforeach; ?>
</ul>

<?php if ($seller_id): ?>
    <h3>Переписка с продавцом <?= $seller_id ?></h3>
    <div class="chat-window">
        <?php foreach ($messages as $msg): ?>
            <?php
            $class = ($msg['sender_type'] === 'user') ? 'msg-user' : 'msg-seller';
            ?>
            <div class="<?= $class ?>">
                <strong><?= htmlspecialchars($msg['sender_name'] ?? 'Неизвестно') ?>:</strong>
                <?= htmlspecialchars($msg['message']) ?>
            </div>
        <?php endforeach; ?>
    </div>


    <form action="" method="post">
        <input type="text" name="message" placeholder="Введите сообщение" required>
        <button type="submit">Отправить</button>
    </form>
<?php endif; ?>

<?php
include '../main/footer.php';
