<?php
session_start();
require_once 'db_conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$postId = isset($data['id']) ? intval($data['id']) : 0;

if ($postId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

// First, check if the post exists
$checkQuery = "SELECT image_url FROM demographics_posts WHERE id = ?";
$checkStmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($checkStmt, "i", $postId);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo json_encode(['success' => false, 'message' => 'Post not found']);
    mysqli_stmt_close($checkStmt);
    exit;
}

// Delete the post
$query = "DELETE FROM demographics_posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $postId);

if (mysqli_stmt_execute($stmt)) {
    // If post had an image, delete it from the server
    if (!empty($post['image_url'])) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . $post['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($checkStmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?> 