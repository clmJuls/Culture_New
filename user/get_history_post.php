<?php
session_start();
require_once 'db_conn.php';

if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    
    $sql = "SELECT hp.*, u.username, 
            DATE_FORMAT(hp.created_at, '%M %d, %Y') as formatted_date 
            FROM history_posts hp 
            LEFT JOIN users u ON hp.user_id = u.id 
            WHERE hp.id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Format the response
        $response = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'content' => $row['content'],
            'image_url' => $row['image_url'],
            'category' => $row['category'],
            'author' => $row['username'],
            'created_at' => $row['formatted_date']
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Post not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No post ID provided']);
}
?> 