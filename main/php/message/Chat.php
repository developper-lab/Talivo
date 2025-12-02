так сообщения больше не выводятся!!
<?php
session_start();
require '../../db.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем партнёра
$partner_id = intval($_GET['user_id'] ?? $_GET['partner_id'] ?? 0);

// Получаем всех собеседников
$stmt = $pdo->prepare("
    SELECT DISTINCT 
        CASE 
            WHEN sender_id = :uid THEN receiver_id
            ELSE sender_id
        END AS partner_id
    FROM message
    WHERE sender_id = :uid OR receiver_id = :uid
");
$stmt->execute(['uid' => $user_id]);
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Отправка нового сообщения через POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $partner_id) {
    $message = trim($_POST['message'] ?? '');
    if ($message !== '') {
        $stmt = $pdo->prepare("INSERT INTO message (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $partner_id, $message]);
        exit; // AJAX не требует редиректа
    }
}

include '../main/header.php';
?>

<h1>Чат</h1>

<?php if ($partner_id): ?>
    <?php
    // Получаем имя собеседника
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$partner_id]);
    $partnerName = $stmt->fetchColumn();
    ?>
    <h3>Переписка с <?= htmlspecialchars($partnerName ?: 'Пользователем #' . $partner_id) ?></h3>

    <div class="chat-window" style="border:1px solid #ccc; padding:10px; max-height:400px; overflow-y:auto;"></div>

    <form id="chat-form" style="display: flex; gap: 10px; margin-top: 15px; align-items: center;">
        <input
            type="text"
            name="message"
            placeholder="Введите сообщение..."
            required
            style="flex: 1; padding: 10px 15px; border: 1px solid #ccc; border-radius: 20px; outline: none; transition: 0.3s;"
            onfocus="this.style.borderColor='#007bff'"
            onblur="this.style.borderColor='#ccc'">
        <button
            type="submit"
            style="background: #007bff; color: #fff; border: none; border-radius: 20px; padding: 10px 20px; cursor: pointer; font-weight: 500; transition: 0.3s;"
            onmouseover="this.style.background='#0056b3'"
            onmouseout="this.style.background='#007bff'">
            Отправить
        </button>
    </form>

    <hr>
<?php endif; ?>

<h3>Мои диалоги:</h3>
<ul>
    <?php foreach ($partners as $p): ?>
        <?php if (!empty($p['partner_id'])): ?>
            <?php
            $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$p['partner_id']]);
            $partnerName = $stmt->fetchColumn();
            ?>
            <li>
                <a href="Chat.php?user_id=<?= htmlspecialchars($p['partner_id']) ?>">
                    <?= htmlspecialchars($partnerName ?: 'Пользователь #' . $p['partner_id']) ?>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<script>
    const chatWindow = document.querySelector('.chat-window');
    const chatForm = document.getElementById('chat-form');
    const partnerId = <?= json_encode($partner_id) ?>;
    const userId = <?= json_encode($user_id) ?>;

    // Функция загрузки сообщений
    async function fetchMessages() {
        if (!partnerId) return;

        const res = await fetch('get_messages.php?partner_id=' + partnerId);
        const messages = await res.json();

        chatWindow.innerHTML = messages.map(msg => {
            const isMy = msg.sender_id == userId;
            return `
            <div style="
                margin:5px 0;
                padding:8px 12px;
                border-radius:10px;
                max-width:70%;
                ${isMy ? 'background:#d1e7dd; margin-left:auto; text-align:right;' : 'background:#f0f0f0; text-align:left;'}
            ">
                <strong>${msg.sender_name}</strong><br>
                ${msg.message.replace(/\n/g,'<br>')}<br>
                <small style="color:#777; font-size:12px;">${msg.created_at}</small>
            </div>
        `;
        }).join('');

        chatWindow.scrollTop = chatWindow.scrollHeight; // прокрутка вниз
    }

    // Отправка сообщений через AJAX
    chatForm.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(chatForm);
        await fetch('Chat.php?user_id=' + partnerId, {
            method: 'POST',
            body: formData
        });
        chatForm.message.value = '';
        fetchMessages();
    });

    // Автообновление каждые 2 секунды
    setInterval(fetchMessages, 2000);
    fetchMessages();
</script>

<?php include '../main/footer.php'; ?>