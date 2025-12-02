<?php
session_start();
include '../../db.php';
include '../main/header.php';
?>
<?php

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? '';


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
                    INSERT INTO posts (title, price, image,user_id, rating, count)
                    VALUES (?, ?, ?,?, 0, 0)
                ");
                $stmt->execute([
                    $title,
                    $price,
                    $newName,
                    $_SESSION['user_id']
                ]);

                $success = "Объявление успешно создано!";
            } else {
                $error = "Ошибка при сохранении изображения.";
            }
        }
    }
}
?>

<main>
    <style>
        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        p {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            max-width: 500px;
            margin: 0 auto 20px;
            padding: 15px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
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



        <button type="submit">Создать объявление</button>
    </form>

    <p><a href="<?= BASE_URL ?>">Назад на главную</a></p>
</main>
<?php include '../main/footer.php';
