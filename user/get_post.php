<?php
require_once 'db_conn.php';

if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    
    $sql = "SELECT gp.*, u.username, DATE_FORMAT(gp.created_at, '%M %d, %Y') as formatted_date 
            FROM geography_posts gp 
            LEFT JOIN users u ON gp.user_id = u.id 
            WHERE gp.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Convert to safe HTML
        $row['title'] = htmlspecialchars($row['title']);
        $row['description'] = htmlspecialchars($row['description']);
        $row['content'] = nl2br(htmlspecialchars($row['content']));
        $row['username'] = $row['username'] ? htmlspecialchars($row['username']) : 'Anonymous';
        $row['created_at'] = $row['formatted_date'];
        
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Post not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No post ID provided']);
}

$conn->close();
?> 