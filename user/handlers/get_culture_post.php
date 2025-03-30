<?php
// Prevent any output before our JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

session_start();
require_once '../db_conn.php';

try {
    // Validate post_id
    if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
        throw new Exception('Invalid post ID');
    }

    $postId = (int)$_GET['post_id'];

    // Get the post details
    $query = "SELECT cp.*, u.username 
              FROM blog_db.culture_posts cp 
              LEFT JOIN blog_db.users u ON cp.user_id = u.id 
              WHERE cp.id = $postId";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true, 
            'post' => $row
        ]);
    } else {
        throw new Exception('Post not found');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?> 