<?php
// Prevent any output before our JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

session_start();
require_once '../db_conn.php';

try {
    // Check if user is admin
    if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
        throw new Exception('Unauthorized access');
    }

    // Validate post_id
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        throw new Exception('Invalid post ID');
    }

    $postId = (int)$_POST['post_id'];

    // Get the image URL before deleting the post
    $query = "SELECT image_url FROM blog_db.culture_posts WHERE id = $postId";
    $result = mysqli_query($conn, $query);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $imageUrl = $row['image_url'];
        
        // Delete the post from database
        $deleteQuery = "DELETE FROM blog_db.culture_posts WHERE id = $postId";
        
        if (mysqli_query($conn, $deleteQuery)) {
            // Delete the image file
            $imagePath = '../' . $imageUrl;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
        } else {
            throw new Exception('Error deleting post: ' . mysqli_error($conn));
        }
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