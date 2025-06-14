<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$appeal_reason = isset($_POST['appeal_reason']) ? trim($_POST['appeal_reason']) : '';
$user_id = $_SESSION['user_id'];

if (!$post_id || empty($appeal_reason)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

// Verify post belongs to user and is rejected
$check_query = "SELECT id FROM posts WHERE id = ? AND user_id = ? AND status = 'rejected'";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $post_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid post or not rejected']);
    exit();
}

// Update post status to pending and store appeal reason
$update_query = "UPDATE posts SET status = 'pending', appeal_reason = ?, updated_at = NOW() WHERE id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("si", $appeal_reason, $post_id);

if ($update_stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Appeal submitted successfully']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Error submitting appeal']);
}

$update_stmt->close();
$check_stmt->close();
$conn->close(); 