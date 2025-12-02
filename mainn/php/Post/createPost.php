<?php
session_start();
include '../../db.php';
include '../main/header.php';
?>
<?php

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';

    if (!$title || !$description || !$price) {
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
                    INSERT INTO posts (title, description, price, image, category, seller_id, rating, count) 
                    VALUES (?, ?, ?, ?, ?, ?, 0, 0)
                ");
                $stmt->execute([
                    $title,
                    $description,
                    $price,
                    $newName,
                    $category,
                    $_SESSION['seller_id']
                ]);
                $success = "Объявление успешно создано!";
            } else {
                $error = "Ошибка при сохранении изображения.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Создать объявление</title>
</head>

<body>
    <h1>Создать объявление</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
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
                <option value="Игрушки">Игрушки</option>
                <option value="Одежда">Одежда</option>
                <option value="Электроника">Электроника</option>
                <option value="Книги">Книги</option>
            </select>
        </label><br><br>

        <button type="submit">Создать объявление</button>
    </form>

    <p><a href="../index.php">Назад на главную</a></p>
</body>

</html>

<?php include '../main/footer.php';
