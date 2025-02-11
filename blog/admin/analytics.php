<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/analytics_helper.php';

$analytics = new AnalyticsHelper($db);
$period = $_GET['period'] ?? '7days';
$stats = $analytics->getStats($period);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics | Blog Admin</title>
    <link rel="stylesheet" href="../assets/css/blog.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>Analytics</h1>
            <div class="nav-links">
                <select id="period" onchange="window.location.href='?period='+this.value">
                    <option value="24h" <?= $period === '24h' ? 'selected' : '' ?>>Last 24 Hours</option>
                    <option value="7days" <?= $period === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="30days" <?= $period === '30days' ? 'selected' : '' ?>>Last 30 Days</option>
                </select>
                <a href="index.php" class="button">Back to Dashboard</a>
            </div>
        </nav>
        
        <main class="admin-content">
            <div class="analytics-grid">
                <div class="analytics-card">
                    <h3>Total Pageviews</h3>
                    <div class="stat-value"><?= number_format($stats['pageviews']) ?></div>
                </div>
                
                <div class="analytics-card">
                    <h3>Popular Posts</h3>
                    <ul class="analytics-list">
                        <?php foreach ($stats['popular_posts'] as $post): ?>
                            <li>
                                <a href="../post.php?slug=<?= $post['slug'] ?>">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                                <span class="view-count"><?= number_format($post['views']) ?> views</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="analytics-card">
                    <h3>Top Referrers</h3>
                    <ul class="analytics-list">
                        <?php foreach ($stats['top_referrers'] as $ref): ?>
                            <li>
                                <?= htmlspecialchars(parse_url($ref['referrer'], PHP_URL_HOST)) ?>
                                <span class="view-count"><?= number_format($ref['count']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="analytics-card">
                    <h3>Popular Searches</h3>
                    <ul class="analytics-list">
                        <?php foreach ($stats['search_terms'] as $search): ?>
                            <li>
                                <?= htmlspecialchars(parse_url($search['page_url'], PHP_URL_QUERY)) ?>
                                <span class="view-count"><?= number_format($search['count']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 