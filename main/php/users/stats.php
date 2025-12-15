<?php
session_start();
include '../../db.php';
include '../main/header.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT title, views 
    FROM posts 
    WHERE user_id = ?
    ORDER BY views DESC
");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="<?= BASE_URL ?>styles/stats.css">

<main class="stats-page">
    <h1>📊 Статистика объявлений</h1>

    <table class="stats-table">
        <tr>
            <th>Объявление</th>
            <th>Просмотры</th>
        </tr>

        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= htmlspecialchars($post['title']) ?></td>
                <td><?= $post['views'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <canvas id="viewsChart"></canvas>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const data = {
        labels: <?= json_encode(array_column($posts, 'title')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($posts, 'views')) ?>,
        }]
    };

    new Chart(document.getElementById('viewsChart'), {
        type: 'bar',
        data: data,
    });
</script>

<?php include '../main/footer.php'; ?>