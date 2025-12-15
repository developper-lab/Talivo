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
<style>
    /* Основной контейнер */
    .chat-container {
        display: flex;
        gap: 20px;
        padding: 20px;
        height: 80vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Левый aside с диалогами */
    .chat-aside {
        width: 280px;
        background: #e6f4ea;
        /* светло-зеленый фон */
        border-radius: 16px;
        padding: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        overflow-y: auto;
        border-left: 4px solid #22c55e;
        /* зеленая акцентная полоска */
    }

    /* Заголовок диалогов */
    .dialogs-title {
        margin-bottom: 12px;
        font-size: 20px;
        font-weight: 700;
        color: #065f46;
        border-bottom: 1px solid #a7f3d0;
        padding-bottom: 8px;
    }

    /* Список диалогов */
    .dialogs-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .dialog-item {
        margin-bottom: 10px;
    }

    .dialog-link {
        display: block;
        padding: 12px 16px;
        background: #ffffff;
        border-radius: 12px;
        text-decoration: none;
        color: #065f46;
        transition: 0.2s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .dialog-link:hover {
        background: #d1fae5;
        transform: translateX(4px);
    }

    .dialog-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dialog-name {
        font-weight: 600;
    }

    .dialog-unread {
        width: 10px;
        height: 10px;
        background: #16a34a;
        border-radius: 50%;
    }

    .dialog-last {
        margin-top: 4px;
        font-size: 13px;
        color: #065f46;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Правый main с перепиской */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: linear-gradient(to bottom, #f0fdf4, #e6f4ea);
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    /* Заголовок чата */
    .chat-header {
        padding: 16px;
        font-weight: 700;
        border-bottom: 1px solid #d1fae5;
        background: #22c55e;
        /* насыщенный зеленый */
        color: #fff;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    /* Окно сообщений */
    .chat-window {
        flex: 1;
        padding: 16px;
        overflow-y: auto;
        background: #f0fdf4;
        /* светло-зеленый */
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* Сообщения */
    .msg {
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 14px;
        animation: fadeIn 0.2s;
        word-wrap: break-word;
    }

    .me {
        align-self: flex-end;
        background: #bbf7d0;
        /* светлый зеленый для своих */
    }

    .other {
        align-self: flex-start;
        background: #d1fae5;
        /* зеленый для чужих */
    }

    /* Форма отправки */
    .chat-form {
        display: flex;
        gap: 10px;
        padding: 12px;
        border-top: 1px solid #d1fae5;
        background: #e6f4ea;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }

    .chat-input {
        flex: 1;
        padding: 12px 18px;
        border-radius: 30px;
        border: 1px solid #22c55e;
        outline: none;
        background: #fff;
    }

    .chat-send {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        border: none;
        background: #16a34a;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
    }

    /* Анимация появления сообщений */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }
</style>
<h1 class="container">Чат</h1>

<div class="chat-container container">
    <!-- Левый aside с диалогами -->
    <aside class="chat-aside">
        <h3 class="dialogs-title">Мои диалоги</h3>
        <ul class="dialogs-list">
            <?php foreach ($dialogData as $dialog): ?>
                <li class="dialog-item">
                    <a class="dialog-link" href="Chat.php?user_id=<?= $dialog['partner_id'] ?>">
                        <div class="dialog-top">
                            <strong class="dialog-name"><?= htmlspecialchars($dialog['partner_name']) ?></strong>
                            <?php if ($dialog['is_unread']): ?>
                                <span class="dialog-unread"></span>
                            <?php endif; ?>
                        </div>
                        <div class="dialog-last">
                            <?= htmlspecialchars(mb_strimwidth($dialog['last_message'], 0, 40, '…')) ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- Правый main с перепиской -->
    <main class="chat-main">
        <?php if ($partner_id): ?>
            <?php
            $stmt = $pdo->prepare("SELECT username FROM users WHERE id=?");
            $stmt->execute([$partner_id]);
            $partnerName = $stmt->fetchColumn();
            ?>
            <div class="chat-header"><?= htmlspecialchars($partnerName ?: 'Пользователь') ?></div>
            <div class="chat-window" id="chatWindow"></div>
            <form id="chatForm" class="chat-form">
                <input class="chat-input" name="message" placeholder="Сообщение..." required>
                <button class="chat-send">➤</button>
            </form>
        <?php else: ?>
            <div class="chat-header">Выбери диалог</div>
        <?php endif; ?>
    </main>
</div>


<script>
    const chatWindow = document.querySelector('.chat-window');
    const chatForm = document.getElementById('chatForm');
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