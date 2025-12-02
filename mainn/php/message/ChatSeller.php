<?php
session_start();
require '../../db.php';


$seller_id = $_SESSION['seller_id'];

// Получаем ID выбранного клиента
$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;

// Отправка сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $client_id) {
    $message = trim($_POST['message'] ?? '');
    if ($message) {
        $stmt = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, sender_type, message)
            VALUES (?, ?, 'seller', ?)
        ");
        $stmt->execute([$seller_id, $client_id, $message]);
        header("Location: ChatSeller.php?client_id=$client_id");
        exit;
    }
}

// Получаем список клиентов, с которыми есть переписка
$stmt = $pdo->prepare("
    SELECT DISTINCT u.id, u.username
    FROM messages m
    JOIN users u ON (m.sender_id = u.id AND m.sender_type = 'user') 
                 OR (m.receiver_id = u.id AND m.sender_type = 'user')
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY u.username
");
$stmt->execute([$seller_id, $seller_id]);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Получаем переписку с выбранным клиентом
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
            <a href="ChatSeller.php?client_id=<?= htmlspecialchars($client['id']) ?>">
                Клиент <?= htmlspecialchars($client['username']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($client_id): ?>
    <?php
    // Ищем имя клиента по id
    $client_name = 'Неизвестно';
    foreach ($clients as $c) {
        if ($c['id'] == $client_id) {
            $client_name = $c['username'];
            break;
        }
    }
    ?>
    <h3>Переписка с <?= htmlspecialchars($client_name) ?></h3>

    <div class="chat-window" style="border:1px solid #ccc; padding:10px; max-height:400px; overflow-y:auto;">
        <?php if (empty($messages)): ?>
            <p>Нет сообщений. Начните переписку!</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php
                // Выравнивание по тому, кто смотрит чат
                $class = ($msg['sender_id'] == $seller_id) ? 'msg-right' : 'msg-left';
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