<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
check_auth();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch post data
$stmt = $db->prepare("
    SELECT p.*, GROUP_CONCAT(t.name) as tags
    FROM posts p
    LEFT JOIN post_tags pt ON p.id = pt.post_id
    LEFT JOIN tags t ON pt.tag_id = t.id
    WHERE p.id = ?
    GROUP BY p.id
");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $status = $_POST['status'] ?? 'draft';
    $tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
    
    // Generate slug from title if it changed
    if ($title !== $post['title']) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    } else {
        $slug = $post['slug'];
    }
    
    try {
        $db->beginTransaction();
        
        // Update post
        $stmt = $db->prepare("
            UPDATE posts 
            SET title = ?, slug = ?, content = ?, excerpt = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $slug, $content, $excerpt, $status, $id]);
        
        // Remove old tags
        $stmt = $db->prepare("DELETE FROM post_tags WHERE post_id = ?");
        $stmt->execute([$id]);
        
        // Add new tags
        foreach ($tags as $tagName) {
            $stmt = $db->prepare("
                INSERT INTO tags (name, slug) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
            ");
            $tagSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagName)));
            $stmt->execute([$tagName, $tagSlug]);
            $tagId = $db->lastInsertId();
            
            $stmt = $db->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$id, $tagId]);
        }
        
        $db->commit();
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error updating post: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post | Blog Admin</title>
    <link rel="stylesheet" href="../assets/css/blog.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>Edit Post</h1>
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
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Content (Markdown supported)</label>
                    <textarea id="content" name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="excerpt">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3"><?= htmlspecialchars($post['excerpt']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="tags">Tags (comma separated)</label>
                    <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($post['tags']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="featured_image">Featured Image</label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*">
                    <?php if ($post['featured_image']): ?>
                        <div class="current-image">
                            <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt="Current featured image">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="social_image">Social Sharing Image</label>
                    <input type="file" id="social_image" name="social_image" accept="image/*">
                    <p class="help-text">Recommended size: 1200x630 pixels</p>
                    <?php if ($post['social_image']): ?>
                        <div class="current-image">
                            <img src="<?= htmlspecialchars($post['social_image']) ?>" alt="Current social image">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="button">Update Post</button>
                </div>
            </form>
        </main>
    </div>
    
    <script>
        var simplemde = new SimpleMDE({ 
            element: document.getElementById("content"),
            spellChecker: false,
            autosave: {
                enabled: true,
                unique_id: "blog_post_edit_<?= $id ?>"
            }
        });
    </script>
</body>
</html> 