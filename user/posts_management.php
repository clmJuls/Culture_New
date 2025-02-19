<?php
require_once 'db_conn.php';
session_start();

// Ensure no HTML errors are output
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($_POST['action'] === 'fetch_posts') {
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $per_page = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 6;
            $offset = ($page - 1) * $per_page;
            
            $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            
            $query = "SELECT p.*, u.username, u.profile_picture, 
                     COUNT(DISTINCT l.id) as like_count,
                     IF(? > 0, EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?), 0) as user_liked
                     FROM posts p
                     LEFT JOIN users u ON p.user_id = u.id
                     LEFT JOIN likes l ON p.id = l.post_id
                     GROUP BY p.id, u.username, u.profile_picture
                     ORDER BY p.created_at DESC
                     LIMIT ? OFFSET ?";
            
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("iiii", $currentUserId, $currentUserId, $per_page, $offset);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $posts = [];
            
            while ($row = $result->fetch_assoc()) {
                // Fetch comments for each post
                $comment_query = "SELECT c.*, u.username, u.profile_picture 
                                 FROM comments c 
                                 LEFT JOIN users u ON c.user_id = u.id 
                                 WHERE c.post_id = ? 
                                 ORDER BY c.created_at DESC";
                $comment_stmt = $conn->prepare($comment_query);
                $comment_stmt->bind_param("i", $row['id']);
                $comment_stmt->execute();
                $comments_result = $comment_stmt->get_result();
                
                $row['comments'] = [];
                while ($comment = $comments_result->fetch_assoc()) {
                    $row['comments'][] = $comment;
                }
                
                $posts[] = $row;
            }
            
            echo json_encode([
                'status' => 'success',
                'posts' => $posts
            ]);
            
        } elseif ($_POST['action'] === 'toggle_like') {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("User must be logged in to like posts");
            }
            
            $post_id = (int)$_POST['post_id'];
            $user_id = $_SESSION['user_id'];
            
            // Check if like exists
            $check_query = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("ii", $post_id, $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Unlike
                $delete_query = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
                $stmt = $conn->prepare($delete_query);
                $stmt->bind_param("ii", $post_id, $user_id);
                $stmt->execute();
            } else {
                // Like
                $insert_query = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("ii", $post_id, $user_id);
                $stmt->execute();
            }
            
            echo json_encode(['status' => 'success']);
        } elseif ($_POST['action'] === 'get_post_likes') {
            if (!isset($_POST['post_id'])) {
                throw new Exception("Post ID is required");
            }
            
            $post_id = (int)$_POST['post_id'];
            $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            
            // Get like count and user's like status
            $query = "SELECT 
                COUNT(DISTINCT l.id) as like_count,
                IF(? > 0, EXISTS(SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?), 0) as user_liked
                FROM posts p
                LEFT JOIN likes l ON p.id = l.post_id
                WHERE p.id = ?";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiii", $currentUserId, $post_id, $currentUserId, $post_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            
            echo json_encode([
                'status' => 'success',
                'like_count' => (int)$data['like_count'],
                'user_liked' => (bool)$data['user_liked']
            ]);
        } elseif ($_POST['action'] === 'delete_post') {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("User must be logged in to delete posts");
            }

            if (!isset($_POST['post_id'])) {
                throw new Exception("Post ID is required");
            }

            $post_id = (int)$_POST['post_id'];
            $user_id = $_SESSION['user_id'];
            $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1;
            
            // Check if user owns the post or is admin
            $check_query = "SELECT user_id FROM posts WHERE id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("i", $post_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $post = $result->fetch_assoc();

            if (!$post) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Post not found'
                ]);
                exit;
            }

            // Check if user is authorized to delete the post
            if ($post['user_id'] != $user_id && !$isAdmin) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this post'
                ]);
                exit;
            }

            // Begin transaction
            $conn->begin_transaction();

            try {
                // Delete likes first (due to foreign key constraints)
                $delete_likes = "DELETE FROM likes WHERE post_id = ?";
                $stmt = $conn->prepare($delete_likes);
                $stmt->bind_param("i", $post_id);
                $stmt->execute();

                // Delete comments if you have a comments table
                $delete_comments = "DELETE FROM comments WHERE post_id = ?";
                $stmt = $conn->prepare($delete_comments);
                $stmt->bind_param("i", $post_id);
                $stmt->execute();

                // Finally delete the post
                $delete_post = "DELETE FROM posts WHERE id = ?";
                $stmt = $conn->prepare($delete_post);
                $stmt->bind_param("i", $post_id);
                $stmt->execute();

                // Commit transaction
                $conn->commit();

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Post deleted successfully'
                ]);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        } elseif ($_POST['action'] === 'add_comment') {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("User must be logged in to comment");
            }
            
            $post_id = (int)$_POST['post_id'];
            $user_id = $_SESSION['user_id'];
            $comment_text = trim($_POST['comment_text']);
            
            if (empty($comment_text)) {
                throw new Exception("Comment text cannot be empty");
            }
            
            // Insert the new comment
            $query = "INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iis", $post_id, $user_id, $comment_text);
            
            if (!$stmt->execute()) {
                throw new Exception("Error adding comment: " . $stmt->error);
            }
            
            // Fetch the newly added comment with user data
            $new_comment_id = $stmt->insert_id;
            $fetch_query = "SELECT 
                c.id as comment_id,
                c.post_id,
                c.user_id,
                c.comment_text,
                c.created_at,
                u.username,
                u.profile_picture
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            WHERE c.id = ?";
            
            $fetch_stmt = $conn->prepare($fetch_query);
            $fetch_stmt->bind_param("i", $new_comment_id);
            $fetch_stmt->execute();
            $result = $fetch_stmt->get_result();
            $new_comment = $result->fetch_assoc();
            
            echo json_encode([
                'status' => 'success',
                'comment' => [
                    'id' => $new_comment['comment_id'],
                    'post_id' => $new_comment['post_id'],
                    'user_id' => $new_comment['user_id'],
                    'username' => $new_comment['username'],
                    'profile_picture' => $new_comment['profile_picture'] ?: 'assets/default-profile.png',
                    'comment_text' => $new_comment['comment_text'],
                    'created_at' => $new_comment['created_at']
                ]
            ]);
        } elseif ($_POST['action'] === 'get_comments') {
            $post_id = (int)$_POST['post_id'];
            
            // Updated query to include all necessary user and comment data
            $query = "SELECT 
                c.id as comment_id,
                c.post_id,
                c.user_id,
                c.comment_text,
                c.created_at,
                u.username,
                u.profile_picture,
                u.id as commenter_id
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = ? 
            ORDER BY c.created_at DESC";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $post_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error executing comment query: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $comments = [];
            
            while ($row = $result->fetch_assoc()) {
                // Format the data for each comment
                $comments[] = [
                    'id' => $row['comment_id'],
                    'post_id' => $row['post_id'],
                    'user_id' => $row['user_id'],
                    'username' => $row['username'],
                    'profile_picture' => $row['profile_picture'] ?: 'assets/default-profile.png',
                    'comment_text' => $row['comment_text'],
                    'created_at' => $row['created_at']
                ];
            }
            
            echo json_encode([
                'status' => 'success',
                'comments' => $comments
            ]);
        } else {
            throw new Exception("Invalid action");
        }
        
    } catch (Exception $e) {
        error_log("Error in posts_management.php: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while processing your request'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}

exit;
