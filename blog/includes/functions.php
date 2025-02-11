<?php
// Markdown parsing function
function marked($content) {
    return htmlspecialchars($content);
}

// Pagination helper
function paginate($total, $per_page, $current_page) {
    $total_pages = ceil($total / $per_page);
    $pages = [];
    
    // Always show first page
    $pages[] = 1;
    
    // Calculate range around current page
    $start = max(2, $current_page - 2);
    $end = min($total_pages - 1, $current_page + 2);
    
    // Add ellipsis after first page if needed
    if ($start > 2) {
        $pages[] = '...';
    }
    
    // Add pages around current page
    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }
    
    // Add ellipsis before last page if needed
    if ($end < $total_pages - 1) {
        $pages[] = '...';
    }
    
    // Add last page if there is more than one page
    if ($total_pages > 1) {
        $pages[] = $total_pages;
    }
    
    return $pages;
}

// Search helper
function buildSearchQuery($search, $tag) {
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
    
    return [
        'where' => $where,
        'params' => $params
    ];
} 