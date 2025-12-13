<?php
session_start();
include "../../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/users/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['username'] ?? '';
include "../main/header.php";
?>
<link rel="stylesheet" href="<?= BASE_URL ?>styles/checkout.css">

<main class="checkout-page">
    <h2>Оформление заказа</h2>
    <form action="checkout_process.php" method="post" class="checkout-form">
        <label>Имя
            <input type="text" name="name" value="<?= htmlspecialchars($userName) ?>" readonly>
        </label>
        <label>Телефон
            <input type="text" name="phone" required>
        </label>
        <label>Адрес
            <input type="text" name="address" required>
        </label>
        <label>Способ доставки
            <select name="delivery" required>
                <option value="courier">Курьер</option>
                <option value="mail">Почта</option>
            </select>
        </label>
        <label>Способ оплаты
            <select name="payment" required>
                <option value="card">Карта</option>
                <option value="cash">Наличные</option>
            </select>
        </label>
        <button type="submit" class="btn submit-order">Подтвердить заказ</button>
    </form>
</main>

<?php
include "../main/footer.php";
