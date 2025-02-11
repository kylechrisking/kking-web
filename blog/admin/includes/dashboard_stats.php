<?php
function getDashboardStats($db) {
    $stats = [];
    
    // Total posts
    $stats['total_posts'] = $db->query("
        SELECT COUNT(*) FROM posts
    ")->fetchColumn();
    
    // Published posts
    $stats['published_posts'] = $db->query("
        SELECT COUNT(*) FROM posts WHERE status = 'published'
    ")->fetchColumn();
    
    // Total tags
    $stats['total_tags'] = $db->query("
        SELECT COUNT(*) FROM tags
    ")->fetchColumn();
    
    // Recent activity
    $stats['recent_posts'] = $db->query("
        SELECT title, status, created_at 
        FROM posts 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    return $stats;
} 