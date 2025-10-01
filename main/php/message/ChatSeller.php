<?php
session_start();
require '../../db.php';

// Проверка, что вошёл продавец
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../../index.php");
    exit;
}

$seller_id = $_SESSION['seller_id'];
$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;

// 1️⃣ Отправка сообщения (до любого вывода)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $client_id) {
    $message = trim($_POST['message'] ?? '');
    if ($message) {
        $stmt = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, sender_type, message)
            VALUES (?, ?, 'seller', ?)
        ");
        $stmt->execute([$seller_id, $client_id, $message]);
        header("Location: chatSeller.php?client_id=$client_id");
        exit;
    }
}

// 2️⃣ Получаем список клиентов
$stmt = $pdo->prepare("
    SELECT DISTINCT sender_id 
    FROM messages 
    WHERE receiver_id = ?
");
$stmt->execute([$seller_id]);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3️⃣ Получаем переписку с выбранным клиентом (если есть)
$messages = [];
if ($client_id) {
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
    $stmt->execute([$seller_id, $client_id, $client_id, $seller_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include '../main/header.php';
?>

<h1>Чат продавца</h1>

<h3>Клиенты:</h3>
<ul>
    <?php foreach ($clients as $client): ?>
        <li>
            <a href="chatSeller.php?client_id=<?= $client['sender_id'] ?>">
                Клиент <?= $client['sender_id'] ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($client_id): ?>
    <h3>Переписка с клиентом <?= $client_id ?></h3>
    <div class="chat-window" style="border:1px solid #ccc; padding:10px; max-height:400px; overflow-y:auto;">
        <?php foreach ($messages as $msg): ?>
            <?php
            $class = ($msg['sender_type'] === 'user') ? 'msg-user' : 'msg-seller';
            $sender_name = $msg['sender_name'] ?? 'Неизвестно';
            ?>
            <div class="<?= $class ?>" style="margin:5px 0; padding:5px; <?= $class === 'msg-user' ? 'background:#f0f0f0;text-align:left;' : 'background:#d1e7dd;text-align:right;' ?>">
                <strong><?= htmlspecialchars($sender_name) ?>:</strong>
                <?= htmlspecialchars($msg['message']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="" method="post" style="margin-top:10px;">
        <input type="text" name="message" placeholder="Введите сообщение" required style="width:70%;">
        <button type="submit">Отправить</button>
    </form>
<?php endif; ?>

<?php
include '../main/footer.php';
?>