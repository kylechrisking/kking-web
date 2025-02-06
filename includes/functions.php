<?php

function generateSlug($text) {
    // Convert text to lowercase
    $text = strtolower($text);
    // Replace non-alphanumeric characters with hyphens
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    // Remove multiple consecutive hyphens
    $text = preg_replace('/-+/', '-', $text);
    // Remove leading and trailing hyphens
    return trim($text, '-');
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function getExcerpt($content, $length = 150) {
    $excerpt = strip_tags($content);
    if (strlen($excerpt) > $length) {
        $excerpt = substr($excerpt, 0, $length) . '...';
    }
    return htmlspecialchars($excerpt);
}

function saveTags($db, $postId, $tagString) {
    // Delete existing tags for this post
    $db->query("DELETE FROM post_tags WHERE post_id = ?", [$postId]);
    
    // Split tags and trim whitespace
    $tags = array_map('trim', explode(',', $tagString));
    
    foreach ($tags as $tag) {
        if (empty($tag)) continue;
        
        // Create slug for tag
        $slug = generateSlug($tag);
        
        // Try to find existing tag
        $existingTag = $db->query(
            "SELECT id FROM tags WHERE slug = ?",
            [$slug]
        )->fetch(PDO::FETCH_ASSOC);
        
        if ($existingTag) {
            $tagId = $existingTag['id'];
        } else {
            // Create new tag
            $db->query(
                "INSERT INTO tags (name, slug) VALUES (?, ?)",
                [$tag, $slug]
            );
            $tagId = $db->lastInsertId();
        }
        
        // Link tag to post
        $db->query(
            "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)",
            [$postId, $tagId]
        );
    }
} 