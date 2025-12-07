<?php
session_start();
include '../db.php';
include 'main/header.php';

$currentUser = $_SESSION['user_id'] ?? null;

// сортировка
$sort = $_GET['sort'] ?? 'popular';
switch ($sort) {
    case 'cheap':
        $orderBy = "price ASC";
        break;
    case 'expensive':
        $orderBy = "price DESC";
        break;
    default:
        $orderBy = "rating DESC, count DESC";
}

// доставка
$delivery = $_GET['delivery'] ?? 'all';

$category = $_GET['category'] ?? '';

// собираем условия
$where = [];
$params = [];

// исключаем товары юзера
if ($currentUser) {
    $where[] = "user_id != :uid";
    $params['uid'] = $currentUser;
}

// фильтр по доставке
if ($delivery !== 'all') {
    $where[] = "delivery = :delivery";
    $params['delivery'] = $delivery;
}

// фильтр по категории
if ($category) {
    $where[] = "category = :category";
    $params['category'] = $category;
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

$sql = "SELECT * FROM posts 
        $whereSql
        ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<link rel="stylesheet" href="<?= BASE_URL ?>styles/catalog.css">

<main class="main catalogs">
    <section class="filter-box">
        <div class="filter-section">
            <h3>Цена, BYN</h3>
            <div class="price-inputs">
                <input type="number" placeholder="От 2">
                <input type="number" placeholder="До 1000">
            </div>
        </div>

        <div class="filter-section">
            <h3>Срок доставки</h3>

            <label class="radio-item">
                <input type="radio" name="delivery" data-delivery="today">
                <span>Сегодня</span>
            </label>

            <label class="radio-item">
                <input type="radio" name="delivery" data-delivery="1-3">
                <span>1–3 дня</span>
            </label>

            <label class="radio-item">
                <input type="radio" name="delivery" data-delivery="7">
                <span>До 7 дней</span>
            </label>

            <label class="radio-item">
                <input type="radio" name="delivery" data-delivery="all">
                <span>Любой</span>
            </label>
        </div>



        <div class="filter-section">
            <h3>Категории</h3>
            <div class="categories">
                <label data-category="">Все категории</label>
                <label data-category="jewelry">Украшения и аксессуары</label>
                <label data-category="clothes">Одежда и текстиль</label>
                <label data-category="decor">Домашний декор</label>
                <label class="hidden" data-category="wood">Деревянные изделия</label>
                <label class="hidden" data-category="ceramics">Керамика и глина</label>
                <label class="hidden" data-category="art">Картины и арт-объекты</label>
                <label class="hidden" data-category="cosmetics">Косметика ручной работы</label>
                <label class="hidden" data-category="food">Еда и выпечка</label>
                <label class="hidden" data-category="gifts">Подарочные наборы</label>
                <label class="hidden" data-category="tools">Инструменты и материалы</label>
            </div>
            <a href="#" class="more">Показать еще</a>
        </div>





        <div class="filter-section">
            <h3>Способ доставки</h3>
            <label><span class="dot green"></span>Курьер</label>
            <label><span class="dot green"></span>Почта</label>
        </div>
    </section>

    <section class="popular">
        <div class="popular__title">
            <span>Популярное</span>
            <div class="dropdown">
                <button class="dropdown-btn">
                    <span class="arrow">▼</span>
                </button>

                <ul class="dropdown-menu">
                    <li data-sort="popular">Популярные</li>
                    <li data-sort="cheap">Подешевле</li>
                    <li data-sort="expensive">Подороже</li>
                </ul>
            </div>

        </div>

        <div class="cards">
            <?php if (!$cards): ?>
                <p>Товаров нету</p>
            <?php else: ?>
                <?php foreach ($cards as $card): ?>
                    <div class="card">
                        <div class="card-img">
                            <img src="uploads/<?php echo htmlspecialchars($card['image']) ?>" alt="Товар">
                        </div>
                        <div class="card-body">
                            <span class="price">
                                <?php echo htmlspecialchars($card['price']) ?>
                            </span>
                            <p class="title">
                                <?php echo htmlspecialchars($card['title']) ?>
                            </p>
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="rate">
                                    <?php echo htmlspecialchars($card['rating']) ?>
                                </span>
                                <span class="count">
                                    <?php echo htmlspecialchars($card['count']) ?>
                                </span>
                            </div>
                            <button class="btn" type="submit" onclick="window.location.href='Post/post.php?id=<?php echo htmlspecialchars($card['id']) ?>'">
                                Посмотреть товар
                            </button>
                            <button class="btn_add" type="submit" id="basket">
                                Добавить в корзину
                            </button>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </section>
</main>
<script>
    const dropdown = document.querySelector('.dropdown');
    const btn = document.querySelector('.dropdown-btn');

    // Открыть меню
    btn.addEventListener('click', () => {
        dropdown.classList.toggle('active');
    });

    // Закрыть при клике вне
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });

    // Обработка клика по сортировке
    document.querySelectorAll('.dropdown-menu li').forEach(item => {
        item.addEventListener('click', () => {
            const sortType = item.getAttribute('data-sort');
            window.location.search = 'sort=' + sortType;
        });
    });

    document.querySelectorAll('input[name="delivery"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const value = radio.getAttribute('data-delivery');

            const url = new URL(window.location.href);
            url.searchParams.set('delivery', value);

            window.location.href = url.toString();
        });
    });


    // Скрываем/показываем остальные категории
    const moreBtn = document.querySelector('.more');
    const labels = document.querySelectorAll('.categories label');

    moreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        labels.forEach((label, index) => {
            if (index >= 4) label.classList.toggle('hidden');
        });
        moreBtn.textContent = moreBtn.textContent === "Показать еще" ? "Скрыть" : "Показать еще";
    });

    // Фильтр по категориям
    labels.forEach(label => {
        label.addEventListener('click', () => {
            const category = label.dataset.category; // пусто для "Все категории"
            const url = new URL(window.location.href);
            if (category) {
                url.searchParams.set('category', category);
            } else {
                url.searchParams.delete('category');
            }
            window.location.href = url.toString();
        });
    });
</script>

<?php
include "main/footer.php";
