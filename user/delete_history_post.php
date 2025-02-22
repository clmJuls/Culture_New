<?php
session_start();
require_once 'db_conn.php';

// Check if user is admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($post_id > 0) {
        // Get image path before deleting
        $sql = "SELECT image_url FROM history_posts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        // Delete from database
        $sql = "DELETE FROM history_posts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $post_id);
        
        if ($stmt->execute()) {
            // Delete image file if exists
            if ($row && !empty($row['image_url']) && file_exists($row['image_url'])) {
                unlink($row['image_url']);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to delete post']);
        }
    } else {
        echo json_encode(['error' => 'Invalid post ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?> 