<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/ai_helper.php';
check_auth();

// Load API key from config
$config = require '../config.php';
$ai = new AIHelper($config['openai_api_key']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'] ?? '';
    $type = $_POST['type'] ?? 'tech_news';
    
    try {
        $post = $ai->generatePost($topic, $type);
        
        // Generate slug from title
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $post['title'])));
        
        $db->beginTransaction();
        
        // Insert post
        $stmt = $db->prepare("
            INSERT INTO posts (title, slug, content, excerpt, status, meta_description) 
            VALUES (?, ?, ?, ?, 'draft', ?)
        ");
        $stmt->execute([
            $post['title'],
            $slug,
            $post['content'],
            $post['excerpt'],
            $post['meta_description']
        ]);
        $postId = $db->lastInsertId();
        
        // Handle tags
        foreach ($post['tags'] as $tagName) {
            $tagSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagName)));
            
            $stmt = $db->prepare("
                INSERT INTO tags (name, slug) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
            ");
            $stmt->execute([$tagName, $tagSlug]);
            $tagId = $db->lastInsertId();
            
            $stmt = $db->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$postId, $tagId]);
        }
        
        $db->commit();
        header('Location: edit.php?id=' . $postId);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error generating post: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Post | Blog Admin</title>
    <link rel="stylesheet" href="../assets/css/blog.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>Generate AI Post</h1>
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
                    <label for="topic">Topic</label>
                    <input type="text" id="topic" name="topic" required 
                           placeholder="e.g., Latest Windows 11 Update, Apple M3 Chip, etc.">
                </div>
                
                <div class="form-group">
                    <label for="type">Content Type</label>
                    <select id="type" name="type">
                        <option value="tech_news">Tech News</option>
                        <option value="update">Software Update</option>
                        <option value="vulnerability">Security Vulnerability</option>
                        <option value="event">Tech Event</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="button">Generate Post</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html> 