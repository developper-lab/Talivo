<?php
session_start();
$orderId = $_GET['order_id'] ?? 0;

include "../../db.php";
include "../main/header.php";
?>
<main class="success-page">
    <h2>Спасибо за заказ!</h2>
    <p>Ваш заказ №<?= htmlspecialchars($orderId) ?> успешно оформлен.</p>
    <a href="<?= BASE_URL ?>" class="btn">Вернуться на главную</a>
</main>
<?php
include "../main/footer.php";
