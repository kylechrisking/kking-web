<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
check_auth();

// Fetch posts for admin dashboard
$stmt = $db->query("
    SELECT 
        p.id, 
        p.title, 
        p.status, 
        p.created_at,
        GROUP_CONCAT(t.name) as tags
    FROM posts p
    LEFT JOIN post_tags pt ON p.id = pt.post_id
    LEFT JOIN tags t ON pt.tag_id = t.id
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Blog</title>
    <link rel="stylesheet" href="../assets/css/blog.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>Blog Admin</h1>
            <div class="nav-links">
                <a href="create.php" class="button">New Post</a>
                <a href="logout.php" class="button">Logout</a>
            </div>
        </nav>
        
        <main class="admin-content">
            <h2>Posts</h2>
            <table class="posts-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Tags</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= htmlspecialchars($post['title']) ?></td>
                            <td>
                                <span class="status-badge <?= $post['status'] ?>">
                                    <?= ucfirst($post['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($post['tags'] ?? '') ?></td>
                            <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?= $post['id'] ?>" class="button">Edit</a>
                                <form method="POST" action="delete.php" class="inline">
                                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                    <button type="submit" class="button delete" 
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html> 