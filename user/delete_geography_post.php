<?php
session_start();
require_once 'db_conn.php';

// Check if user is admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    die(json_encode(['error' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    
    // First, get the image URL to delete the file
    $sql = "SELECT image_url FROM geography_posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Delete the image file if it exists
        if (file_exists($row['image_url'])) {
            unlink($row['image_url']);
        }
    }
    
    // Delete the post from database
    $sql = "DELETE FROM geography_posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete post']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?> 