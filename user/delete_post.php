<?php
require 'db_conn.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    try {
        $post_id = (int)$_POST['post_id'];
        $user_id = $_SESSION['user_id'];

        // First verify that the post belongs to the user and is rejected
        $verify_query = "SELECT id FROM posts WHERE id = ? AND user_id = ? AND status = 'rejected'";
        $verify_stmt = $conn->prepare($verify_query);
        $verify_stmt->bind_param("ii", $post_id, $user_id);
        $verify_stmt->execute();
        $result = $verify_stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Post not found or unauthorized to delete");
        }

        // If verification passes, delete the post
        $delete_query = "DELETE FROM posts WHERE id = ? AND user_id = ? AND status = 'rejected'";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $post_id, $user_id);
        
        if ($delete_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Post deleted successfully']);
        } else {
            throw new Exception("Error deleting post");
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
} 