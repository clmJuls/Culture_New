<?php
require 'db_conn.php';
session_start();
header('Content-Type: application/json');

// At the start of the file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow fetching posts without login
if ($_POST['action'] === 'fetch_posts') {
    fetchPosts($conn, isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
    exit();
}

// Require login for all other actions
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please log in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : 0; // Check if the user is an admin

// Handle different AJAX actions
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action) {
    case 'fetch_posts':
        fetchPosts($conn, $user_id);
        break;
    
    case 'toggle_like':
        toggleLike($conn, $user_id);
        break;
    
    case 'add_comment':
        addComment($conn, $user_id);
        break;
    
    case 'delete_post':
        deletePost($conn, $user_id, $is_admin);
        break;
    
    case 'delete_comment':
        deleteComment($conn, $user_id, $is_admin);
        break;
}

function fetchPosts($conn, $user_id) {
    try {
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $per_page = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 6;
        $offset = ($page - 1) * $per_page;

        $query = "
            SELECT 
                p.*, 
                u.username, 
                u.profile_picture,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND is_active = 1) as like_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
                " . ($user_id ? "(SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = $user_id AND is_active = 1) as user_liked" : "0 as user_liked") . "
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
            LIMIT $per_page OFFSET $offset
        ";
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }

        $posts = [];
        while ($post = mysqli_fetch_assoc($result)) {
            // Add file type checking
            if ($post['file_path']) {
                $file_extension = strtolower(pathinfo($post['file_path'], PATHINFO_EXTENSION));
                $post['is_video'] = in_array($file_extension, ['mp4', 'webm', 'mov']);
            }

            // Fetch comments for this post
            $post_id = $post['id'];
            $comments_query = "
                SELECT c.*, u.username, u.profile_picture 
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = $post_id
                ORDER BY c.created_at ASC
            ";
            $comments_result = mysqli_query($conn, $comments_query);
            $post['comments'] = [];
            while ($comment = mysqli_fetch_assoc($comments_result)) {
                $post['comments'][] = $comment;
            }
            
            $posts[] = $post;
        }

        echo json_encode([
            'success' => true,
            'posts' => $posts,
            'current_user_id' => $user_id,
            'isAdmin' => isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ? true : false
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}


function toggleLike($conn, $user_id) {
    $post_id = $_POST['post_id'];
    
    // Sanitize inputs
    $post_id = (int)$post_id;
    $user_id = (int)$user_id;
    
    // Check if like exists
    $check_like = "SELECT id, is_active FROM likes 
                   WHERE post_id = $post_id AND user_id = $user_id";
    $result = mysqli_query($conn, $check_like);

    if (mysqli_num_rows($result) > 0) {
        $like = mysqli_fetch_assoc($result);
        // Toggle is_active status
        $new_status = $like['is_active'] ? 0 : 1;
        $update = "UPDATE likes 
                   SET is_active = $new_status 
                   WHERE post_id = $post_id AND user_id = $user_id";
        mysqli_query($conn, $update);
        echo json_encode(['status' => $new_status ? 'liked' : 'unliked']);
    } else {
        // Create new like record
        $insert = "INSERT INTO likes (post_id, user_id, is_active) 
                  VALUES ($post_id, $user_id, 1)";
        mysqli_query($conn, $insert);
        echo json_encode(['status' => 'liked']);
    }
}

function addComment($conn, $user_id) {
    $post_id = $_POST['post_id'];
    $comment_text = trim(mysqli_real_escape_string($conn, $_POST['comment_text']));

    if (!empty($comment_text)) {
        $add_comment = "INSERT INTO comments (post_id, user_id, comment_text) VALUES ($post_id, $user_id, '$comment_text')";
        mysqli_query($conn, $add_comment);
        echo json_encode(['status' => 'success']);
        exit();
    }

    echo json_encode(['status' => 'error', 'message' => 'Comment cannot be empty']);
}

function deletePost($conn, $user_id, $is_admin) {
    $post_id = $_POST['post_id'];
    $stmt = "SELECT user_id FROM posts WHERE id = $post_id";
    $result = mysqli_query($conn, $stmt);
    $post = mysqli_fetch_assoc($result);

    // Check if the user is an admin or the owner of the post
    if ($post && ($post['user_id'] == $user_id || $is_admin)) {
        mysqli_query($conn, "DELETE FROM likes WHERE post_id = $post_id");
        mysqli_query($conn, "DELETE FROM comments WHERE post_id = $post_id");
        mysqli_query($conn, "DELETE FROM posts WHERE id = $post_id");
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized to delete this post']);
    }
}

function deleteComment($conn, $user_id, $is_admin) {
    $comment_id = $_POST['comment_id'];
    $stmt = "SELECT user_id FROM comments WHERE id = $comment_id";
    $result = mysqli_query($conn, $stmt);
    $comment = mysqli_fetch_assoc($result);

    // Check if the user is an admin or the owner of the comment
    if ($comment && ($comment['user_id'] == $user_id || $is_admin)) {
        mysqli_query($conn, "DELETE FROM comments WHERE id = $comment_id");
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized to delete this comment']);
    }
}
?>
