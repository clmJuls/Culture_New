<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

// Similar query as in notification.php but with LIMIT
$query = "SELECT 
            p.title AS post_title,
            p.created_at AS post_created_at,
            u.username AS post_creator,
            u.profile_picture AS user_avatar,
            SUBSTRING(u.username, 1, 1) AS avatar_letter,
            c.comment_text,
            c.created_at AS comment_created_at,
            c.user_id AS comment_user_id,
            cu.username AS comment_user,
            l.created_at AS like_created_at,
            l.user_id AS like_user_id,
            lu.username AS like_user
        FROM posts p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN comments c ON p.id = c.post_id
        LEFT JOIN users cu ON c.user_id = cu.id
        LEFT JOIN likes l ON p.id = l.post_id
        LEFT JOIN users lu ON l.user_id = lu.id
        WHERE p.user_id = ?
        ORDER BY GREATEST(
            IFNULL(p.created_at, 0),
            IFNULL(c.created_at, 0),
            IFNULL(l.created_at, 0)
        ) DESC
        LIMIT ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $limit);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="notification-item">';
        echo '<div class="notification-avatar">';
        if (!empty($row['user_avatar'])) {
            echo '<img src="' . htmlspecialchars($row['user_avatar']) . '" alt="User avatar">';
        } else {
            echo '<div class="avatar-letter">' . htmlspecialchars($row['avatar_letter']) . '</div>';
        }
        echo '</div>';
        echo '<div class="notification-content">';
        
        if (!empty($row['comment_text'])) {
            echo '<div><strong>' . htmlspecialchars($row['comment_user']) . '</strong> commented on your post</div>';
        } elseif (!empty($row['like_user'])) {
            echo '<div><strong>' . htmlspecialchars($row['like_user']) . '</strong> liked your post</div>';
        } else {
            echo '<div>New post: ' . htmlspecialchars($row['post_title']) . '</div>';
        }
        
        echo '<div class="notification-time">' . getTimeAgo(strtotime($row['post_created_at'])) . '</div>';
        echo '</div></div>';
    }
} else {
    echo '<div class="notification-item">No notifications</div>';
}

$stmt->close();

function getTimeAgo($timestamp) {
    // ... existing getTimeAgo function from notification.php ...
}
?> 