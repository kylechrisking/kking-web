<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();

// Pagination settings
$postsPerPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $postsPerPage;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';

// Build query based on filters
$where = ["status = 'published'"];
$params = [];

if ($search) {
    $where[] = "(title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($tag) {
    $where[] = "EXISTS (
        SELECT 1 FROM post_tags pt 
        JOIN tags t ON pt.tag_id = t.id 
        WHERE pt.post_id = p.id AND t.slug = ?
    )";
    $params[] = $tag;
}

// Get total posts for pagination
$totalQuery = "SELECT COUNT(DISTINCT p.id) as total FROM posts p";
if (!empty($where)) {
    $totalQuery .= " WHERE " . implode(" AND ", $where);
}
$total = $db->query($totalQuery, $params)->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($total / $postsPerPage);

// Get posts
$query = "
    SELECT p.*, GROUP_CONCAT(t.name) as tag_list 
    FROM posts p 
    LEFT JOIN post_tags pt ON p.id = pt.post_id 
    LEFT JOIN tags t ON pt.tag_id = t.id
";
if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params[] = $postsPerPage;
$params[] = $offset;

$posts = $db->query($query, $params)->fetchAll(PDO::FETCH_ASSOC);
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
                $popularTags = $db->query("
                    SELECT t.name, t.slug, COUNT(*) as count 
                    FROM tags t 
                    JOIN post_tags pt ON t.id = pt.tag_id 
                    GROUP BY t.id 
                    ORDER BY count DESC 
                    LIMIT 10
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($popularTags as $t): ?>
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
                                <?php foreach (explode(',', $post['tag_list']) as $tag): ?>
                                    <span class="post-tag"><?= trim(htmlspecialchars($tag)) ?></span>
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
        <?php if ($totalPages > 1): ?>
            <nav class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $tag ? '&tag=' . urlencode($tag) : '' ?>" class="prev-page">Previous</a>
                <?php endif; ?>
                
                <div class="page-numbers">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if (
                            $i == 1 || 
                            $i == $totalPages || 
                            ($i >= $page - 2 && $i <= $page + 2)
                        ): ?>
                            <a 
                                href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $tag ? '&tag=' . urlencode($tag) : '' ?>" 
                                class="page-number <?= $i === $page ? 'active' : '' ?>"
                            >
                                <?= $i ?>
                            </a>
                        <?php elseif (
                            $i == 2 || 
                            $i == $totalPages - 1
                        ): ?>
                            <span class="page-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $tag ? '&tag=' . urlencode($tag) : '' ?>" class="next-page">Next</a>
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