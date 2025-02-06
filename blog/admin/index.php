<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';

// Check if user is authenticated
if (!isset($_SESSION['admin_authenticated'])) {
    // Not logged in, show only the login form
    $showLoginOnly = true;
} else {
    $showLoginOnly = false;
    $db = new Database();
    $posts = $db->query("
        SELECT p.*, GROUP_CONCAT(t.name) as tag_list 
        FROM posts p 
        LEFT JOIN post_tags pt ON p.id = pt.post_id 
        LEFT JOIN tags t ON pt.tag_id = t.id 
        GROUP BY p.id 
        ORDER BY p.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Admin</title>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/blog-admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="admin-nav">
        <a href="../../" class="nav-logo">KK</a>
        <div class="nav-links">
            <a href="../../blog/" class="nav-link">View Blog</a>
            <?php if (!$showLoginOnly): ?>
                <button onclick="logout()" class="logout-btn">Logout</button>
            <?php endif; ?>
        </div>
    </nav>

    <div class="admin-login" id="loginForm" <?= $showLoginOnly ? '' : 'style="display: none;"' ?>>
        <form onsubmit="return handleLogin(event)">
            <h2>Blog Admin</h2>
            <p class="login-message">Please enter your admin password to continue.</p>
            <input type="password" id="adminPassword" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>

    <div class="admin-panel" id="adminPanel" <?= $showLoginOnly ? 'style="display: none;"' : '' ?>>
        <h1>Blog Management</h1>
        
        <div class="admin-actions">
            <button onclick="showNewPostForm()" class="action-btn">New Post</button>
            <button onclick="refreshPosts()" class="action-btn">Refresh Posts</button>
        </div>
        
        <div id="postForm" style="display: none;">
            <h2>New Post</h2>
            <form onsubmit="return handleNewPost(event)">
                <input type="text" id="postTitle" placeholder="Title" required>
                <input type="text" id="postTags" placeholder="Tags (comma separated)">
                <textarea id="postContent" placeholder="Content (Markdown supported)" required></textarea>
                <div class="form-actions">
                    <button type="button" onclick="cancelPost()" class="cancel-btn">Cancel</button>
                    <button type="submit" class="submit-btn">Publish</button>
                </div>
            </form>
        </div>

        <div id="existingPosts">
            <h2>Existing Posts</h2>
            <div class="posts-list">
                <?php if (!$showLoginOnly): foreach ($posts as $post): ?>
                    <div class="post-item">
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <p class="post-meta">
                            Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?>
                            <span class="post-tags"><?= htmlspecialchars($post['tag_list']) ?></span>
                        </p>
                        <div class="post-actions">
                            <button onclick="editPost(<?= $post['id'] ?>)">Edit</button>
                            <button onclick="deletePost(<?= $post['id'] ?>)" class="delete-btn">Delete</button>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    
    <div id="notification" class="notification" style="display: none;"></div>

    <script src="../../js/admin.js"></script>
</body>
</html> 