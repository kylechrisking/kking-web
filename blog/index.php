<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'includes/init.php';
    echo "Init loaded<br>";
    
    require_once 'includes/header.php';
    echo "Header loaded<br>";
    
    echo "Everything working!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$db = new Database();

// Pagination settings
$per_page = 5;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';

// Build query conditions
$query = buildSearchQuery($search, $tag);
$where = $query['where'];
$params = $query['params'];

// Get total posts for pagination
$total_query = "SELECT COUNT(DISTINCT p.id) as total FROM posts p";
if (!empty($where)) {
    $total_query .= " WHERE " . implode(" AND ", $where);
}
$stmt = $db->prepare($total_query);
$stmt->execute($params);
$total_posts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get posts for current page
$query = "
    SELECT 
        p.*,
        GROUP_CONCAT(t.name) as tags,
        GROUP_CONCAT(t.slug) as tag_slugs
    FROM posts p
    LEFT JOIN post_tags pt ON p.id = pt.post_id
    LEFT JOIN tags t ON pt.tag_id = t.id
";
if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$stmt = $db->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get popular tags for tag cloud
$popular_tags = $db->query("
    SELECT t.name, t.slug, COUNT(*) as count 
    FROM tags t 
    JOIN post_tags pt ON t.id = pt.tag_id 
    JOIN posts p ON pt.post_id = p.id 
    WHERE p.status = 'published'
    GROUP BY t.id 
    ORDER BY count DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kyle's Tech Blog</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/blog.css">
    <!-- Add markdown parser for blog content -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="blog-nav">
        <a href="/" class="nav-logo">KK</a>
        <div class="nav-links">
            <a href="/" class="nav-link">Portfolio</a>
            <button class="theme-toggle" aria-label="Toggle theme">
                <svg class="sun-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg class="moon-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
        </div>
    </nav>

    <main class="blog-content">
        <header class="blog-header">
            <h1>Tech Blog</h1>
            <p>Thoughts on technology, development, and the IT industry</p>
            
            <!-- Search form -->
            <form class="search-form" action="" method="GET">
                <input 
                    type="search" 
                    name="search" 
                    placeholder="Search posts..."
                    value="<?= htmlspecialchars($search) ?>"
                >
                <button type="submit">Search</button>
            </form>
            
            <!-- Tags filter -->
            <div class="tags-filter">
                <?php
                foreach ($popular_tags as $t): ?>
                    <a 
                        href="?tag=<?= urlencode($t['slug']) ?>" 
                        class="tag-link <?= $tag === $t['slug'] ? 'active' : '' ?>"
                    >
                        <?= htmlspecialchars($t['name']) ?>
                        <span class="tag-count"><?= $t['count'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </header>

        <?php if ($search || $tag): ?>
            <div class="filter-info">
                <?php if ($search): ?>
                    <p>Search results for: "<?= htmlspecialchars($search) ?>"</p>
                <?php endif; ?>
                <?php if ($tag): ?>
                    <p>Posts tagged with: <?= htmlspecialchars($tag) ?></p>
                <?php endif; ?>
                <a href="?" class="clear-filters">Clear filters</a>
            </div>
        <?php endif; ?>

        <div class="posts-grid">
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <p>No posts yet. Check back soon!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <div class="post-content">
                            <header>
                                <time datetime="<?= $post['created_at'] ?>">
                                    <?= date('F j, Y', strtotime($post['created_at'])) ?>
                                </time>
                                <h2><?= htmlspecialchars($post['title']) ?></h2>
                            </header>
                            <div class="post-excerpt">
                                <?php
                                $excerpt = strip_tags($post['content']);
                                echo htmlspecialchars(substr($excerpt, 0, 150)) . '...';
                                ?>
                            </div>
                            <div class="post-tags">
                                <?php foreach (explode(',', $post['tags']) as $i => $tag_name): ?>
                                    <?php $tag_slug = explode(',', $post['tag_slugs'])[$i]; ?>
                                    <a href="?tag=<?= urlencode($tag_slug) ?>" class="tag-link">
                                        <?= htmlspecialchars($tag_name) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <a href="post.php?id=<?= $post['id'] ?>" class="read-more">
                                Read More
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_posts > $per_page): ?>
            <nav class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $tag ? '&tag=' . urlencode($tag) : '' ?>" class="prev-page">Previous</a>
                <?php endif; ?>
                
                <div class="page-numbers">
                    <?php foreach (paginate($total_posts, $per_page, $current_page) as $page): ?>
                        <?php if ($page === '...'): ?>
                            <span class="page-ellipsis">...</span>
                        <?php else: ?>
                            <a 
                                href="?page=<?= $page ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $tag ? '&tag=' . urlencode($tag) : '' ?>" 
                                class="page-number <?= $page === $current_page ? 'active' : '' ?>"
                            >
                                <?= $page ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($current_page < ceil($total_posts / $per_page)): ?>
                    <a href="?page=<?= $current_page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $tag ? '&tag=' . urlencode($tag) : '' ?>" class="next-page">Next</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </main>

    <script src="../js/theme.js"></script>
    <script>
        // Initialize markdown parser
        marked.setOptions({
            breaks: true,
            gfm: true,
            headerIds: false
        });
    </script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?> 