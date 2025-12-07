<?php
session_start();
include '../../db.php';
include '../main/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>styles/createPost.css">

<?php

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $delivery = $_POST['delivery'] ?? '';

    if (!$category || !$delivery) {
        $error = "Выберите категорию и срок доставки.";
    }

    if (!$title || !$price) {
        $error = "Все поля должны быть заполнены.";
    } elseif (!is_numeric($price)) {
        $error = "Цена должна быть числом.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = "Ошибка при загрузке изображения.";
    } else {
        $image = $_FILES['image'];
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array(strtolower($ext), $allowed)) {
            $error = "Допустимые форматы изображения: jpg, jpeg, png, gif.";
        } else {
            $newName = uniqid('img_') . '.' . $ext;
            $uploadDir = '../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadPath = $uploadDir . $newName;

            if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
                // Сохраняем в базу
                $stmt = $pdo->prepare("
                    INSERT INTO posts (title, description, price, image, user_id, category, delivery, rating, count)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)  

                ");
                $stmt->execute([
                    $title,
                    $_POST['description'],
                    $price,
                    $newName,
                    $_SESSION['user_id'],
                    $category,
                    $delivery
                ]);


                $success = "Объявление успешно создано!";
            } else {
                $error = "Ошибка при сохранении изображения.";
            }
        }
    }
}
?>

<main class="main">

    <h1>Создать объявление</h1>

    <?php if (!empty($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>


    <form action="" method="post" enctype="multipart/form-data">
        <label>Заголовок:<br>
            <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </label><br><br>

        <label>Описание:<br>
            <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </label><br><br>

        <label>Цена:<br>
            <input type="text" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
        </label><br><br>

        <label>Картинка:<br>
            <input type="file" name="image" accept="image/*">
        </label><br><br>


        <label>Категория:<br>
            <select name="category" required>
                <option value="" disabled>Выберите категорию</option>

                <option value="jewelry">Украшения и аксессуары</option>
                <option value="clothes">Одежда и текстиль</option>
                <option value="decor">Домашний декор</option>
                <option value="wood">Деревянные изделия</option>
                <option value="ceramics">Керамика и глина</option>
                <option value="art">Картины и арт-объекты</option>
                <option value="cosmetics">Косметика ручной работы</option>
                <option value="food">Еда и выпечка</option>
                <option value="gifts">Подарочные наборы</option>
                <option value="tools">Инструменты и материалы</option>

            </select>
        </label>
        <br><br>

        <label>Срок доставки:</label>
        <div class="delivery-block">
            <label class="delivery-item">
                <input type="radio" name="delivery" value="today" required> Сегодня
            </label>

            <label class="delivery-item">
                <input type="radio" name="delivery" value="1-3"> 1–3 дня
            </label>

            <label class="delivery-item">
                <input type="radio" name="delivery" value="7"> До 7 дней
            </label>

            <label class="delivery-item">
                <input type="radio" name="delivery" value="any"> Любой
            </label>
        </div>
        <br>

        <button type="submit">Создать объявление</button>
    </form>

    <p><a href="<?= BASE_URL ?>">Назад на главную</a></p>
</main>
<?php include '../main/footer.php';
