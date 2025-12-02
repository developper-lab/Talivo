<?php
session_start();
include '../../db.php';
include '../main/header.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';

    if (!$title || !$description || !$price) {
        $error = "Все поля должны быть заполнены.";
    } elseif (!is_numeric($price)) {
        $error = "Цена должна быть числом.";
    } else {
        $imagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($ext), $allowed)) {
                $error = "Допустимые форматы изображения: jpg, jpeg, png, gif.";
            } else {
                $newName = uniqid('img_') . '.' . $ext;
                $uploadDir = '../uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $uploadPath = $uploadDir . $newName;
                if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
                    $imagePath = $newName;
                } else {
                    $error = "Ошибка при сохранении изображения.";
                }
            }
        }

        if (!isset($error)) {
            $stmt = $pdo->prepare("
                INSERT INTO service_requests (user_id, title, description, price, category, image, status, created_at)
                VALUES (:user_id, :title, :description, :price, :category, :image, 'new', NOW())
            ");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':title' => $title,
                ':description' => $description,
                ':price' => $price,
                ':category' => $category ?: null,
                ':image' => $imagePath
            ]);
            $success = "Заявка успешно создана!";
        }
    }
}

?>
<h1>Оставить заявку на услугу</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
    <label>Кратко о задаче:<br>
        <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
    </label><br><br>

    <label>Подробности:<br>
        <textarea name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </label><br><br>

    <label>Желаемая цена:<br>
        <input type="text" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
    </label><br><br>

    <label>Категория:<br>
        <select name="category">
            <option value="">-- выберите --</option>
            <option value="Дизайн">Дизайн</option>
            <option value="Программирование">Программирование</option>
            <option value="Тексты">Тексты</option>
            <option value="Маркетинг">Маркетинг</option>
        </select>
    </label><br><br>

    <label>Файл / изображение (по желанию):<br>
        <input type="file" name="image" accept="image/*">
    </label><br><br>

    <button type="submit">Отправить заявку</button>
</form>

<?php
include '../main/footer.php';
?>