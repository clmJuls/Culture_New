<?php
// Prevent PHP errors from being displayed in output
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON content type header early
header('Content-Type: application/json');

require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$appeal_reason = isset($_POST['appeal_reason']) ? trim($_POST['appeal_reason']) : '';
$user_id = $_SESSION['user_id'];

if (!$post_id || empty($appeal_reason)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

try {
    // Verify post belongs to user and is rejected
    $check_query = "SELECT id FROM posts WHERE id = ? AND user_id = ? AND status = 'rejected'";
    $check_stmt = $conn->prepare($check_query);
    
    if (!$check_stmt) {
        throw new Exception($conn->error);
    }
    
    $check_stmt->bind_param("ii", $post_id, $user_id);
    
    if (!$check_stmt->execute()) {
        throw new Exception($check_stmt->error);
    }
    
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid post or not rejected']);
        exit();
    }

    // Update post status to pending and store appeal reason and appealed_at timestamp
    $update_query = "UPDATE posts SET status = 'pending', appeal_reason = ?, appealed_at = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    
    if (!$update_stmt) {
        throw new Exception($conn->error);
    }
    
    $update_stmt->bind_param("si", $appeal_reason, $post_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception($update_stmt->error);
    }

    echo json_encode(['status' => 'success', 'message' => 'Appeal submitted successfully']);

} catch (Exception $e) {
    error_log("Appeal error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error submitting appeal. Please try again later.']);
} finally {
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($conn)) $conn->close();
} 