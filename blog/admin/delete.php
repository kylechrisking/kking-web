<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    try {
        $db->beginTransaction();
        
        // Remove post tags
        $stmt = $db->prepare("DELETE FROM post_tags WHERE post_id = ?");
        $stmt->execute([$id]);
        
        // Delete the post
        $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        
        $db->commit();
        
        // Clean up unused tags
        $db->query("
            DELETE FROM tags 
            WHERE id NOT IN (
                SELECT DISTINCT tag_id FROM post_tags
            )
        ");
        
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        die("Error deleting post: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
} 