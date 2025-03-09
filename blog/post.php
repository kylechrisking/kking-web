<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/includes/init.php';
    require_once __DIR__ . '/includes/header.php';

    // Get post slug from URL
    $slug = isset($_GET['slug']) ? $_GET['slug'] : '';

    if (empty($slug)) {
        throw new Exception("No post specified");
    }

    // Fetch post data
    $stmt = $db->prepare("
        SELECT p.*, GROUP_CONCAT(t.name) as tags
        FROM posts p
        LEFT JOIN post_tags pt ON p.id = pt.post_id
        LEFT JOIN tags t ON pt.tag_id = t.id
        WHERE p.slug = ? AND p.status = 'published'
        GROUP BY p.id
    ");

    $stmt->execute([$slug]);
    $post = $stmt->fetch();

    // If post not found, show 404
    if (!$post) {
        header('HTTP/1.0 404 Not Found');
        echo "<h1>Post not found</h1>";
        require_once __DIR__ . '/includes/footer.php';
        exit;
    }
    ?>

    <main class="blog-content single-post">
        <article class="post">
            <header class="post-header">
                <h1><?= htmlspecialchars($post['title']) ?></h1>
                <div class="post-meta">
                    <time datetime="<?= $post['created_at'] ?>">
                        <?= date('F j, Y', strtotime($post['created_at'])) ?>
                    </time>
                    <?php if ($post['tags']): ?>
                        <div class="tags">
                            <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                <span class="tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <?php if ($post['featured_image']): ?>
                <div class="post-image">
                    <img src="<?= htmlspecialchars($post['featured_image']) ?>" 
                         alt="<?= htmlspecialchars($post['title']) ?>">
                </div>
            <?php endif; ?>

            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
        </article>
    </main>

    <?php
    require_once __DIR__ . '/includes/footer.php';
} catch (Exception $e) {
    error_log("Blog Error: " . $e->getMessage());
    echo "<h1>Sorry, there was an error loading the blog post.</h1>";
    echo "<p>Please try again later.</p>";
}