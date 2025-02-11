<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $status = $_POST['status'] ?? 'draft';
    $tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
    
    // Generate slug from title
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    try {
        $db->beginTransaction();
        
        // Insert post
        $stmt = $db->prepare("
            INSERT INTO posts (title, slug, content, excerpt, status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $slug, $content, $excerpt, $status]);
        $postId = $db->lastInsertId();
        
        // Handle tags
        foreach ($tags as $tagName) {
            // Insert or get tag
            $stmt = $db->prepare("
                INSERT INTO tags (name, slug) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
            ");
            $tagSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagName)));
            $stmt->execute([$tagName, $tagSlug]);
            $tagId = $db->lastInsertId();
            
            // Link tag to post
            $stmt = $db->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$postId, $tagId]);
        }
        
        $db->commit();
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error creating post: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post | Blog Admin</title>
    <link rel="stylesheet" href="../assets/css/blog.css">
    <link rel="stylesheet" href="css/admin.css">
    <!-- Add Markdown Editor -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>Create New Post</h1>
            <div class="nav-links">
                <a href="index.php" class="button">Back to Dashboard</a>
            </div>
        </nav>
        
        <main class="admin-content">
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" class="post-form">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Content (Markdown supported)</label>
                    <textarea id="content" name="content" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="excerpt">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="tags">Tags (comma separated)</label>
                    <input type="text" id="tags" name="tags">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="button">Create Post</button>
                </div>
            </form>
        </main>
    </div>
    
    <script>
        // Initialize SimpleMDE
        var simplemde = new SimpleMDE({ 
            element: document.getElementById("content"),
            spellChecker: false,
            autosave: {
                enabled: true,
                unique_id: "blog_post_content"
            }
        });
    </script>
</body>
</html> 