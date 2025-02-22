<?php
session_start();
require_once 'db_conn.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$post_id = (int)$_GET['id'];

$query = "SELECT dp.*, u.username, DATE_FORMAT(dp.created_at, '%M %d, %Y') as formatted_date 
          FROM demographics_posts dp 
          LEFT JOIN users u ON dp.user_id = u.id 
          WHERE dp.id = ? AND dp.status = 'published'";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($post = mysqli_fetch_assoc($result)) {
    // Sanitize the output
    $post['title'] = htmlspecialchars($post['title']);
    $post['content'] = htmlspecialchars($post['content']);
    $post['username'] = $post['username'] ? htmlspecialchars($post['username']) : 'Anonymous';
    $post['created_at'] = $post['formatted_date'];
    
    echo json_encode($post);
} else {
    echo json_encode(['error' => 'Post not found']);
}

mysqli_stmt_close($stmt);
$conn->close();
?> 