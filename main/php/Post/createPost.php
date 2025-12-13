<?php
session_start();
include '../../db.php';
include '../main/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>styles/createPost.css">

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_draft = isset($_POST['draft']);
    
    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $delivery = $_POST['delivery'] ?? '';
    $description = $_POST['description'] ?? '';

    $error = '';
    
    if (!$is_draft) {
        if (!$title || !$price || !$category || !$delivery) {
            $error = "Все обязательные поля должны быть заполнены.";
        } elseif (!is_numeric($price) || $price <= 0) {
            $error = "Цена должна быть положительным числом.";
        } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $error = "Необходимо загрузить изображение.";
        } else {
            $image = $_FILES['image'];
            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array(strtolower($ext), $allowed)) {
                $error = "Допустимые форматы изображения: jpg, jpeg, png, gif.";
            } elseif ($image['size'] > 10 * 1024 * 1024) { // 10MB
                $error = "Размер файла не должен превышать 10MB.";
            }
        }
    }

    if (!$error) {
        $image_path = null;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $newName = uniqid('img_') . '.' . $ext;
            $uploadDir = '../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadPath = $uploadDir . $newName;
            
            if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
                $image_path = $newName;
            }
        }


        $status = $is_draft ? 'draft' : 'published';
        
        $stmt = $pdo->prepare("
            INSERT INTO posts (title, description, price, image, user_id, category, delivery, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $title,
            $description,
            $price,
            $image_path,
            $_SESSION['user_id'] ?? 1, 
            $category,
            $delivery,
            $status
        ]);

        if ($result) {
            if ($is_draft) {
                $success = "Объявление сохранено как черновик!";
            } else {
                $success = "Объявление успешно опубликовано!";
            }
        } else {
            $error = "Ошибка при сохранении в базу данных.";
        }
    }
}
?>

<main class="main">
    <div class="breadcrumbs">
        Главная / Создать объявление
    </div>

    <div class="page-header">
        <h1>Создать новое объявление</h1>
        <a class="cancel-link" href="<?= BASE_URL ?>">Отменить</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="post-form">
        <section class="form-section">
            <h2>Основная информация</h2>
            <p class="section-description">Укажите название и детальное описание вашего товара.</p>

            <div class="form-group">
                <label for="title">Заголовок объявления</label>
                <input type="text" id="title" name="title" required
                       placeholder="Например: iPhone 12 Pro Max, 256GB"
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description" 
                          placeholder="Опишите состояние, комплектацию и причины продажи..."
                          maxlength="3000"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 3000 символов
                </div>
            </div>
        </section>

        <section class="form-section">
            <h2>Цена и категория</h2>
            <p class="section-description">Установите справедливую цену.</p>
            
            <div class="grid-2">
                <div class="form-group">
                    <label for="price">Цена (BYN)</label>
                    <div class="price-input">
                        <input type="number" id="price" name="price" required
                               step="0.01" min="0" 
                               placeholder="0.00"
                               value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                        <span class="currency">BYN</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category">Категория</label>
                    <select id="category" name="category" required>
                        <option value="">Выберите категорию</option>
                        <option value="jewelry" <?= ($_POST['category'] ?? '') == 'jewelry' ? 'selected' : '' ?>>Украшения и аксессуары</option>
                        <option value="clothes" <?= ($_POST['category'] ?? '') == 'clothes' ? 'selected' : '' ?>>Одежда и текстиль</option>
                        <option value="decor" <?= ($_POST['category'] ?? '') == 'decor' ? 'selected' : '' ?>>Домашний декор</option>
                        <option value="wood" <?= ($_POST['category'] ?? '') == 'wood' ? 'selected' : '' ?>>Деревянные изделия</option>
                        <option value="ceramics" <?= ($_POST['category'] ?? '') == 'ceramics' ? 'selected' : '' ?>>Керамика и глина</option>
                        <option value="art" <?= ($_POST['category'] ?? '') == 'art' ? 'selected' : '' ?>>Картины и арт-объекты</option>
                        <option value="cosmetics" <?= ($_POST['category'] ?? '') == 'cosmetics' ? 'selected' : '' ?>>Косметика ручной работы</option>
                        <option value="food" <?= ($_POST['category'] ?? '') == 'food' ? 'selected' : '' ?>>Еда и выпечка</option>
                        <option value="gifts" <?= ($_POST['category'] ?? '') == 'gifts' ? 'selected' : '' ?>>Подарочные наборы</option>
                        <option value="tools" <?= ($_POST['category'] ?? '') == 'tools' ? 'selected' : '' ?>>Инструменты и материалы</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="form-section">
            <h3>Срок доставки</h3>
            <div class="delivery-block">
                <label class="delivery-item">
                    <input type="radio" name="delivery" value="today" required 
                           <?= ($_POST['delivery'] ?? '') == 'today' ? 'checked' : '' ?>> 
                    <span>Сегодня</span>
                </label>
                <label class="delivery-item">
                    <input type="radio" name="delivery" value="1-3"
                           <?= ($_POST['delivery'] ?? '') == '1-3' ? 'checked' : '' ?>> 
                    <span>1–3 дня</span>
                </label>
                <label class="delivery-item">
                    <input type="radio" name="delivery" value="7"
                           <?= ($_POST['delivery'] ?? '') == '7' ? 'checked' : '' ?>> 
                    <span>До 7 дней</span>
                </label>
                <label class="delivery-item">
                    <input type="radio" name="delivery" value="any"
                           <?= ($_POST['delivery'] ?? '') == 'any' ? 'checked' : '' ?>> 
                    <span>Любой</span>
                </label>
            </div>
        </section>

        <section class="form-section">
            <h3>Фотографии</h3>
            <p class="section-description">Первое фото будет на обложке. Перетащите, чтобы изменить порядок.</p>

            <div class="upload-container">
                <label class="upload-zone" id="uploadZone">
                    <input type="file" id="imageInput" name="images[]" accept="image/*" hidden multiple>
                    <div class="upload-content">
                        <div class="upload-icon">📷</div>
                        <div class="upload-text">
                            <strong>Загрузите фото</strong> 
                        </div>
                        <span class="upload-hint">PNG, JPG, GIF до 10MB</span>
                    </div>
                </label>
                
                <div class="preview-container" id="previewContainer">
                </div>
            </div>
        </section>

        <div class="form-actions">
            <button type="submit" name="draft" class="btn ghost">Сохранить как черновик</button>
            <button type="submit" class="btn primary">Опубликовать</button>
        </div>
    </form>
</main>

<script>
// Счетчик символов
const descriptionTextarea = document.getElementById('description');
const charCount = document.getElementById('charCount');

if (descriptionTextarea && charCount) {
    charCount.textContent = descriptionTextarea.value.length;
    descriptionTextarea.addEventListener('input', () => {
        charCount.textContent = descriptionTextarea.value.length;
    });
}

// Drag & Drop загрузка
const uploadZone = document.getElementById('uploadZone');
const imageInput = document.getElementById('imageInput');
const previewContainer = document.getElementById('previewContainer');

if (uploadZone && imageInput && previewContainer) {

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
        uploadZone.addEventListener(event, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    ['dragenter', 'dragover'].forEach(event => {
        uploadZone.addEventListener(event, () => {
            uploadZone.classList.add('highlight');
        });
    });

    ['dragleave', 'drop'].forEach(event => {
        uploadZone.addEventListener(event, () => {
            uploadZone.classList.remove('highlight');
        });
    });

    uploadZone.addEventListener('drop', e => {
        handleFiles(e.dataTransfer.files);
    });

    imageInput.addEventListener('change', () => {
        handleFiles(imageInput.files);
    });

    function handleFiles(files) {
        if (!files.length) return;

        const file = files[0];

        if (!file.type.startsWith('image/')) {
            alert('Выберите изображение');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            alert('Размер файла не должен превышать 10MB');
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            previewContainer.innerHTML = `
                <div class="preview-item">
                    <img src="${e.target.result}" alt="Превью">
                    <button type="button" class="remove-preview">×</button>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }

    previewContainer.addEventListener('click', e => {
        if (e.target.classList.contains('remove-preview')) {
            previewContainer.innerHTML = '';
            imageInput.value = '';
        }
    });
}
</script>


<?php include '../main/footer.php'; ?>