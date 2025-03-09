<?php
require_once 'includes/init.php';
require_once 'includes/header.php';
?>

<main class="blog-content">
    <h1>Latest Posts</h1>
    
    <div class="posts-grid">
        <?php
        // Fetch latest posts
        $stmt = $db->query("
            SELECT p.*, GROUP_CONCAT(t.name) as tags
            FROM posts p
            LEFT JOIN post_tags pt ON p.id = pt.post_id
            LEFT JOIN tags t ON pt.tag_id = t.id
            WHERE p.status = 'published'
            GROUP BY p.id
            ORDER BY p.created_at DESC
            LIMIT 10
        ");
        
        $posts = $stmt->fetchAll();
        
        foreach ($posts as $post): ?>
            <article class="post-card">
                <?php if ($post['featured_image']): ?>
                    <img src="<?= htmlspecialchars($post['featured_image']) ?>" 
                         alt="<?= htmlspecialchars($post['title']) ?>">
                <?php endif; ?>
                
                <div class="post-content">
                    <h2><a href="post.php?slug=<?= htmlspecialchars($post['slug']) ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a></h2>
                    
                    <div class="post-meta">
                        <time><?= date('F j, Y', strtotime($post['created_at'])) ?></time>
                        <?php if ($post['tags']): ?>
                            <div class="tags">
                                <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                    <span class="tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <p><?= htmlspecialchars($post['excerpt']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>