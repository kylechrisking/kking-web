<?php
require_once '../../../includes/config.php';
require_once '../../../includes/database.php';
require_once '../../../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_authenticated'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = new Database();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $stmt = $db->query("
            SELECT p.*, GROUP_CONCAT(t.name) as tag_list 
            FROM posts p 
            LEFT JOIN post_tags pt ON p.id = pt.post_id 
            LEFT JOIN tags t ON pt.tag_id = t.id 
            GROUP BY p.id 
            ORDER BY p.created_at DESC
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $slug = generateSlug($data['title']);
        $excerpt = getExcerpt($data['content']);
        
        $stmt = $db->query(
            "INSERT INTO posts (title, content, slug, excerpt, status) VALUES (?, ?, ?, ?, ?)",
            [$data['title'], $data['content'], $slug, $excerpt, 'published']
        );
        
        $postId = $db->lastInsertId();
        saveTags($db, $postId, $data['tags']);
        
        echo json_encode([
            'success' => true,
            'id' => $postId,
            'slug' => $slug
        ]);
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        
        if ($id) {
            $slug = generateSlug($data['title']);
            $excerpt = getExcerpt($data['content']);
            
            $db->query(
                "UPDATE posts SET title = ?, content = ?, slug = ?, excerpt = ? WHERE id = ?",
                [$data['title'], $data['content'], $slug, $excerpt, $id]
            );
            
            saveTags($db, $id, $data['tags']);
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db->query("DELETE FROM posts WHERE id = ?", [$id]);
            echo json_encode(['success' => true]);
        }
        break;
} 