<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$db = new Database();
$post = $db->query("SELECT * FROM posts WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - Kyle's Tech Blog</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/blog.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body>
    <!-- Navigation (same as index.php) -->
    <nav class="blog-nav">
        <a href="/" class="nav-logo">KK</a>
        <div class="nav-links">
            <a href="/blog" class="nav-link">Back to Blog</a>
            <a href="/" class="nav-link">Portfolio</a>
            <button class="theme-toggle" aria-label="Toggle theme">
                <!-- Theme toggle SVGs (same as index.php) -->
            </button>
        </div>
    </nav>

    <main class="blog-content">
        <article class="post-full">
            <header class="post-header">
                <time datetime="<?= $post['created_at'] ?>">
                    <?= date('F j, Y', strtotime($post['created_at'])) ?>
                </time>
                <h1><?= htmlspecialchars($post['title']) ?></h1>
                <div class="post-tags">
                    <?php foreach (explode(',', $post['tags']) as $tag): ?>
                        <span class="post-tag"><?= trim(htmlspecialchars($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
            </header>
            
            <div class="post-body" id="postContent"></div>
        </article>
    </main>

    <script src="../js/theme.js"></script>
    <script>
        // Parse markdown content
        marked.setOptions({
            breaks: true,
            gfm: true,
            headerIds: false
        });
        
        document.getElementById('postContent').innerHTML = marked.parse(<?= json_encode($post['content']) ?>);
    </script>
</body>
</html> 