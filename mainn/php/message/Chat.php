<?php
session_start();
require '../../db.php';

// Проверка авторизации
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем список всех продавцов, с которыми есть переписка
$stmt = $pdo->prepare("
    SELECT DISTINCT
        CASE
            WHEN sender_type = 'seller' THEN sender_id
            WHEN sender_type = 'user' THEN receiver_id
        END AS seller_id
    FROM messages
    WHERE sender_id = ? OR receiver_id = ?
    ORDER BY seller_id
");
$stmt->execute([$user_id, $user_id]);
$sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Выбранный продавец
$seller_id = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 0;

// Получаем переписку с выбранным продавцом
$messages = [];
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
        <li>
            <a href="Chat.php?seller_id=<?= htmlspecialchars($seller['seller_id']) ?>">
                Продавец <?= htmlspecialchars($seller['seller_id']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($seller_id): ?>
    <h3>Переписка с продавцом <?= htmlspecialchars($seller_id) ?></h3>
    <div class="chat-window" style="border:1px solid #ccc; padding:10px; max-height:400px; overflow-y:auto;">
        <?php if (empty($messages)): ?>
            <p>Нет сообщений. Начните переписку!</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php
                // Выравнивание по тому, кто смотрит чат
                $class = ($msg['sender_id'] == $user_id) ? 'msg-right' : 'msg-left';
                ?>
                <div class="<?= $class ?>" style="margin:5px 0; padding:5px; <?= $class === 'msg-right' ? 'background:#d1e7dd;text-align:right;' : 'background:#f0f0f0;text-align:left;' ?>">
                    <strong><?= htmlspecialchars($msg['sender_name'] ?? 'Неизвестно') ?>:</strong>
                    <?= htmlspecialchars($msg['message']) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form action="" method="post" style="margin-top:10px;">
        <input type="text" name="message" placeholder="Введите сообщение" required style="width:70%;">
        <button type="submit">Отправить</button>
    </form>
<?php endif; ?>

<?php include '../main/footer.php'; ?>