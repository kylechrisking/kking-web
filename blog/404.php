<?php require_once 'includes/header.php'; ?>

<div class="error-page">
    <div class="error-content">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <div class="error-actions">
            <a href="/blog" class="button">Return to Blog</a>
            <a href="javascript:history.back()" class="button secondary">Go Back</a>
        </div>
        
        <div class="suggested-posts">
            <h3>Recent Posts You Might Like</h3>
            <?php
            $recent_posts = $db->query("
                SELECT title, slug 
                FROM posts 
                WHERE status = 'published' 
                ORDER BY created_at DESC 
                LIMIT 3
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($recent_posts as $post): ?>
                <a href="post.php?slug=<?= $post['slug'] ?>" class="suggested-post">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 