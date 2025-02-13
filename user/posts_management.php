<?php
require 'db_conn.php';
session_start();

// Ensure user is logged in
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
    $query = "
        SELECT 
            p.*, 
            u.username, 
            u.profile_picture,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = $user_id) as user_liked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ";
    $result = mysqli_query($conn, $query);

    $posts = [];
    while ($post = mysqli_fetch_assoc($result)) {
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
        'posts' => $posts,
        'current_user_id' => $user_id,
        'isAdmin' => isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ? true : false
    ]);
}


function toggleLike($conn, $user_id) {
    $post_id = $_POST['post_id'];
    $check_like = "SELECT id FROM likes WHERE post_id = $post_id AND user_id = $user_id";
    $result = mysqli_query($conn, $check_like);

    if (mysqli_num_rows($result) > 0) {
        $unlike = "DELETE FROM likes WHERE post_id = $post_id AND user_id = $user_id";
        mysqli_query($conn, $unlike);
        echo json_encode(['status' => 'unliked']);
    } else {
        $like = "INSERT INTO likes (post_id, user_id) VALUES ($post_id, $user_id)";
        mysqli_query($conn, $like);
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
