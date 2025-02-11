<?php
require_once 'includes/db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: index.php');
    exit;
}

// Fetch post with tags
$stmt = $db->prepare("
    SELECT 
        p.*,
        GROUP_CONCAT(t.name) as tags,
        GROUP_CONCAT(t.slug) as tag_slugs
    FROM posts p
    LEFT JOIN post_tags pt ON p.id = pt.post_id
    LEFT JOIN tags t ON pt.tag_id = t.id
    WHERE p.slug = ? AND p.status = 'published'
    GROUP BY p.id
");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: index.php');
    exit;
}

// Split tags into arrays
$tagNames = $post['tags'] ? explode(',', $post['tags']) : [];
$tagSlugs = $post['tag_slugs'] ? explode(',', $post['tag_slugs']) : [];
$tags = array_map(function($name, $slug) {
    return ['name' => $name, 'slug' => $slug];
}, $tagNames, $tagSlugs);

require_once 'includes/header.php';
?>

<article class="blog-post">
    <header class="post-header">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <time datetime="<?= $post['created_at'] ?>">
            <?= date('F j, Y', strtotime($post['created_at'])) ?>
        </time>
        
        <?php if (!empty($tags)): ?>
            <div class="post-tags">
                <?php foreach ($tags as $tag): ?>
                    <a href="index.php?tag=<?= urlencode($tag['slug']) ?>" class="tag-link">
                        <?= htmlspecialchars($tag['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </header>
    
    <div class="post-content markdown-body">
        <?= marked($post['content']) ?>
    </div>
    
    <footer class="post-footer">
        <a href="index.php" class="back-link">← Back to Blog</a>
    </footer>
</article>

<script>
// Initialize markdown parser
marked.setOptions({
    breaks: true,
    gfm: true,
    headerIds: false
});

// Parse markdown content
document.querySelector('.markdown-body').innerHTML = 
    marked(document.querySelector('.markdown-body').textContent);
</script>

<?php require_once 'includes/footer.php'; ?> 