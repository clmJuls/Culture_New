<?php
require_once 'db_conn.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($_POST['action'] === 'fetch_posts') {
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $per_page = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 10;
            $offset = ($page - 1) * $per_page;
            $filter = isset($_POST['filter']) ? $_POST['filter'] : '';
            $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            
            $learning_styles = isset($_POST['learning_styles']) && is_array($_POST['learning_styles']) 
                ? array_map('strval', $_POST['learning_styles']) 
                : [];
            
            if ($filter === 'following' && isset($_SESSION['user_id'])) {
                $query = "SELECT p.*, u.username, u.full_name, u.profile_picture, u.isPremium,
                         COUNT(DISTINCT l.id) as like_count,
                         COUNT(DISTINCT c.id) as comment_count,
                         IF(? > 0, EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?), 0) as user_liked,
                         DATE_FORMAT(p.created_at, '%M %d, %Y') as formatted_date
                         FROM posts p
                         INNER JOIN users u ON p.user_id = u.id
                         INNER JOIN user_follows f ON p.user_id = f.following_id
                         LEFT JOIN likes l ON p.id = l.post_id
                         LEFT JOIN comments c ON p.id = c.post_id
                         WHERE f.follower_id = ? AND p.status = 'approved'
                         GROUP BY p.id, u.username, u.profile_picture
                         ORDER BY p.created_at DESC
                         LIMIT ? OFFSET ?";
                
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iiiii", $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $per_page, $offset);
            } else {
                $query = "SELECT DISTINCT p.*, u.username, u.profile_picture, u.isPremium as is_premium,
                         COUNT(DISTINCT l.id) as like_count,
                         IF(? > 0, EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?), 0) as user_liked
                         FROM posts p
                         LEFT JOIN users u ON p.user_id = u.id
                         LEFT JOIN likes l ON p.id = l.post_id
                         WHERE p.status = 'approved'";
                if (!empty($learning_styles)) {
                    $query .= " AND (";
                    $conditions = array();
                    foreach ($learning_styles as $style) {
                        $conditions[] = "p.learning_styles LIKE ?";
                    }
                    $query .= implode(" OR ", $conditions) . ")";
                }
                
                $query .= " GROUP BY p.id, u.username, u.profile_picture
                           ORDER BY p.created_at DESC";
                
                if ($per_page > 0) {
                    $offset = ($page - 1) * $per_page;
                    $query .= " LIMIT ? OFFSET ?";
                    
                    if (!empty($learning_styles)) {
                        $types = "ii" . str_repeat('s', count($learning_styles)) . "ii";
                        $params = array_merge(
                            [$currentUserId, $currentUserId],
                            array_map(function($style) { return "%$style%"; }, $learning_styles),
                            [$per_page, $offset]
                        );
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param($types, ...$params);
                    } else {
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("iiii", $currentUserId, $currentUserId, $per_page, $offset);
                    }
                } else {
                    if (!empty($learning_styles)) {
                        $types = "ii" . str_repeat('s', count($learning_styles));
                        $params = array_merge(
                            [$currentUserId, $currentUserId],
                            array_map(function($style) { return "%$style%"; }, $learning_styles)
                        );
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param($types, ...$params);
                    } else {
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("ii", $currentUserId, $currentUserId);
                    }
                }
            }
            
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $posts = [];
            
            while ($row = $result->fetch_assoc()) {
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
                'posts' => $posts,
                'current_user_id' => $_SESSION['user_id'] ?? null
            ]);
            
        } elseif ($_POST['action'] === 'fetch_user_posts') {
            if (!isset($_POST['user_id'])) {
                throw new Exception("User ID is required");
            }
            
            $user_id = (int)$_POST['user_id'];
            $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            
            $query = "SELECT p.*, u.username, u.full_name, u.profile_picture, u.isPremium as is_premium,
                     COUNT(DISTINCT l.id) as like_count,
                     COUNT(DISTINCT c.id) as comment_count,
                     IF(? > 0, EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?), 0) as user_liked,
                     DATE_FORMAT(p.created_at, '%M %d, %Y') as formatted_date
                     FROM posts p
                     LEFT JOIN users u ON p.user_id = u.id
                     LEFT JOIN likes l ON p.id = l.post_id
                     LEFT JOIN comments c ON p.id = c.post_id
                     WHERE p.user_id = ? AND p.status = 'approved'
                     GROUP BY p.id, u.username, u.profile_picture
                     ORDER BY p.created_at DESC";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $currentUserId, $currentUserId, $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error fetching user posts: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $posts = [];
            
            while ($row = $result->fetch_assoc()) {
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

            $check_query = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("ii", $post_id, $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $delete_query = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
                $stmt = $conn->prepare($delete_query);
                $stmt->bind_param("ii", $post_id, $user_id);
                $stmt->execute();
            } else {
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

            if ($post['user_id'] != $user_id && !$isAdmin) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this post'
                ]);
                exit;
            }

            $conn->begin_transaction();

            try {
                $delete_likes = "DELETE FROM likes WHERE post_id = ?";
                $stmt = $conn->prepare($delete_likes);
                $stmt->bind_param("i", $post_id);
                $stmt->execute();

                $delete_comments = "DELETE FROM comments WHERE post_id = ?";
                $stmt = $conn->prepare($delete_comments);
                $stmt->bind_param("i", $post_id);
                $stmt->execute();

                $delete_post = "DELETE FROM posts WHERE id = ?";
                $stmt = $conn->prepare($delete_post);
                $stmt->bind_param("i", $post_id);
                $stmt->execute();

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
            
            $query = "INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iis", $post_id, $user_id, $comment_text);
            
            if (!$stmt->execute()) {
                throw new Exception("Error adding comment: " . $stmt->error);
            }
            
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
                    'profile_picture' => $new_comment['profile_picture'] ? htmlspecialchars($new_comment['profile_picture']) : 'assets/hero/v07_20@Shanks.png',
                    'comment_text' => $new_comment['comment_text'],
                    'created_at' => $new_comment['created_at']
                ]
            ]);
        } elseif ($_POST['action'] === 'get_comments') {
            $post_id = (int)$_POST['post_id'];
            
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
                $comments[] = [
                    'id' => $row['comment_id'],
                    'post_id' => $row['post_id'],
                    'user_id' => $row['user_id'],
                    'username' => $row['username'],
                    'profile_picture' => $row['profile_picture'] ? htmlspecialchars($row['profile_picture']) : 'assets/hero/v07_20@Shanks.png',
                    'comment_text' => $row['comment_text'],
                    'created_at' => $row['created_at']
                ];
            }
            
            echo json_encode([
                'status' => 'success',
                'comments' => $comments
            ]);
        } elseif ($_POST['action'] === 'delete_comment') {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("User must be logged in to delete comments");
            }

            if (!isset($_POST['comment_id'])) {
                throw new Exception("Comment ID is required");
            }

            $comment_id = (int)$_POST['comment_id'];
            $user_id = $_SESSION['user_id'];
            $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1;

            $query = "SELECT user_id FROM comments WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $comment = $result->fetch_assoc();

            if (!$comment) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Comment not found'
                ]);
                exit;
            }

            if ($comment['user_id'] != $user_id && !$isAdmin) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this comment'
                ]);
                exit;
            }

            $delete_query = "DELETE FROM comments WHERE id = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param("i", $comment_id);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Comment deleted successfully'
                ]);
            } else {
                throw new Exception("Error deleting comment: " . $stmt->error);
            }
        } elseif ($_POST['action'] === 'update_status') {
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
                throw new Exception("Unauthorized access");
            }
            
            if (!isset($_POST['post_id']) || !isset($_POST['status'])) {
                throw new Exception("Missing required parameters");
            }
            
            $post_id = (int)$_POST['post_id'];
            $status = $_POST['status'];
            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
            
            if (!in_array($status, ['pending', 'approved', 'rejected'])) {
                throw new Exception("Invalid status value");
            }
            
            // Get the user_id of the post owner before updating
            $get_user_query = "SELECT user_id, title FROM posts WHERE id = ?";
            $user_stmt = $conn->prepare($get_user_query);
            $user_stmt->bind_param("i", $post_id);
            $user_stmt->execute();
            $post_result = $user_stmt->get_result();
            $post_data = $post_result->fetch_assoc();
            
            if (!$post_data) {
                throw new Exception("Post not found");
            }
            
            $query = "UPDATE posts SET status = ?, message = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $message = $status === 'rejected' ? $reason : '';
            $stmt->bind_param("ssi", $status, $message, $post_id);
            
            if ($stmt->execute()) {
                // Create notification after successful status update
                require_once 'services/NotificationService.php';
                $notificationService = new NotificationService($conn);
                
                $notificationTitle = "";
                $notificationMessage = "";
                $redirectUrl = "";
                
                if ($status === 'approved') {
                    $notificationTitle = "Post Approved";
                    $notificationMessage = "Your post \"" . $post_data['title'] . "\" has been approved!";
                    $redirectUrl = "explore.php";
                } else if ($status === 'rejected') {
                    $notificationTitle = "Post Rejected";
                    $notificationMessage = "Your post \"" . $post_data['title'] . "\" has been rejected.";
                    if ($reason) {
                        $notificationMessage .= " Reason: " . $reason;
                    }
                    $redirectUrl = "my-posts.php";
                }
                
                if ($notificationTitle) {
                    $notificationService->createNotification(
                        $post_data['user_id'],
                        $notificationTitle,
                        $notificationMessage,
                        $redirectUrl
                    );
                }
                
                echo json_encode(['status' => 'success']);
            } else {
                throw new Exception("Database error: " . $conn->error);
            }
            
            $stmt->close();
        } else {
            throw new Exception("Invalid action");
        }
        
    } catch (Exception $e) {
        error_log("Error in posts_management.php: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}

exit;
