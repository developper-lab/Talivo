<?php
session_start();
require '../../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id    = $_SESSION['user_id'];
$partner_id = intval($_GET['user_id'] ?? $_GET['partner_id'] ?? 0);

/* =========================
   СПИСОК ДИАЛОГОВ
========================= */

$stmt = $pdo->prepare("
    SELECT 
        CASE 
            WHEN m.sender_id = :uid THEN m.receiver_id
            ELSE m.sender_id
        END AS partner_id,
        MAX(m.id) AS last_message_id
    FROM message m
    WHERE m.sender_id = :uid OR m.receiver_id = :uid
    GROUP BY partner_id
");
$stmt->execute(['uid' => $user_id]);
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dialogData = [];

foreach ($partners as $p) {
    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            u.username AS partner_name
        FROM message m
        JOIN users u ON u.id = ?
        WHERE m.id = ?
    ");
    $stmt->execute([$p['partner_id'], $p['last_message_id']]);
    $lastMsg = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lastMsg) continue;

    $dialogData[] = [
        'partner_id'   => $p['partner_id'],
        'partner_name' => $lastMsg['partner_name'],
        'last_message' => $lastMsg['message'],
        'is_unread'    => (
            $lastMsg['sender_id'] != $user_id &&
            array_key_exists('read_at', $lastMsg) &&
            $lastMsg['read_at'] === null
        )
    ];
}

/* =========================
   ОТПРАВКА СООБЩЕНИЯ
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $partner_id) {
    $message = trim($_POST['message'] ?? '');
    if ($message !== '') {
        $stmt = $pdo->prepare("
            INSERT INTO message (sender_id, receiver_id, message)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user_id, $partner_id, $message]);
        exit;
    }
}

include '../main/header.php';
?>

<h1>Чат</h1>

<?php if ($partner_id): ?>
    <?php
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$partner_id]);
    $partnerName = $stmt->fetchColumn();
    ?>
    <h3>Переписка с <?= htmlspecialchars($partnerName ?: 'Пользователем #' . $partner_id) ?></h3>

    <div class="chat-window"
        style="border:1px solid #ccc; padding:10px; max-height:400px; overflow-y:auto;">
    </div>

    <form id="chat-form" style="display:flex; gap:10px; margin-top:15px;">
        <input type="text" name="message" placeholder="Введите сообщение..." required
            style="flex:1; padding:10px 15px; border:1px solid #ccc; border-radius:20px;">
        <button type="submit"
            style="background:#007bff; color:#fff; border:none; border-radius:20px; padding:10px 20px;">
            Отправить
        </button>
    </form>
<?php endif; ?>

<hr>

<h3>Мои диалоги</h3>
<ul>
    <?php foreach ($dialogData as $dialog): ?>
        <li>
            <a href="Chat.php?user_id=<?= $dialog['partner_id'] ?>">
                <strong><?= htmlspecialchars($dialog['partner_name']) ?></strong>:
                <?= htmlspecialchars(mb_strimwidth($dialog['last_message'], 0, 40, '...')) ?>
                <?php if ($dialog['is_unread']): ?>
                    <span style="color:red; font-weight:bold;">●</span>
                <?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<script>
    const chatWindow = document.querySelector('.chat-window');
    const chatForm = document.getElementById('chat-form');
    const partnerId = <?= json_encode($partner_id) ?>;
    const userId = <?= json_encode($user_id) ?>;

    async function fetchMessages() {
        if (!partnerId) return;

        const res = await fetch('get_messages.php?partner_id=' + partnerId);
        const messages = await res.json();

        chatWindow.innerHTML = messages.map(msg => {
            const isMy = msg.sender_id == userId;
            const style = isMy ?
                'background:#d1e7dd; margin-left:auto; text-align:right;' :
                'background:#f0f0f0;';

            let content = msg.message.replace(/\n/g, '<br>');

            if (msg.type === 'order' && msg.seller_id == userId && msg.status === 'pending') {
                content += `
                <br>
                <button onclick="handleOrder(${msg.order_id}, 'accept')">Принять</button>
                <button onclick="handleOrder(${msg.order_id}, 'reject')">Отклонить</button>
            `;
            }

            return `
        <div style="margin:5px 0; padding:8px 12px; border-radius:10px; max-width:70%; ${style}">
            <strong>${msg.sender_name}</strong><br>
            ${content}<br>
            <small style="color:#777">${msg.created_at}</small>
        </div>`;
        }).join('');

        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    chatForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(chatForm);
        await fetch('Chat.php?user_id=' + partnerId, {
            method: 'POST',
            body: formData
        });
        chatForm.message.value = '';
        fetchMessages();
    });

    setInterval(fetchMessages, 2000);
    fetchMessages();

    async function handleOrder(orderId, action) {
        const res = await fetch('handle_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `order_id=${orderId}&action=${action}`
        });
        const data = await res.json();
        if (data.success) fetchMessages();
    }
</script>

<?php include '../main/footer.php'; ?>