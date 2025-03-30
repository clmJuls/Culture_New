<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

if ($isAdmin) {
    // Query for admin users - include post requests and new posts
    $query = "SELECT 
                'post_request' as type,
                p.id,
                p.title,
                p.created_at,
                u.username,
                u.profile_picture,
                p.status
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.status = 'pending'
            
            UNION ALL
            
            SELECT 
                'new_post' as type,
                p2.id,
                p2.title,
                p2.created_at,
                u2.username,
                u2.profile_picture,
                p2.status
            FROM posts p2
            JOIN users u2 ON p2.user_id = u2.id
            WHERE p2.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            
            UNION ALL
            
            SELECT 
                'interaction' as type,
                p3.id,
                p3.title,
                GREATEST(
                    COALESCE(MAX(c.created_at), '1970-01-01'),
                    COALESCE(MAX(l.created_at), '1970-01-01')
                ) as created_at,
                u3.username,
                u3.profile_picture,
                p3.status
            FROM posts p3
            JOIN users u3 ON p3.user_id = u3.id
            LEFT JOIN comments c ON p3.id = c.post_id
            LEFT JOIN likes l ON p3.id = l.post_id
            WHERE p3.status = 'approved'
            GROUP BY p3.id
            ORDER BY created_at DESC
            LIMIT ?";
} else {
    // Query for regular users - show their post creations and interactions
    $query = "SELECT 
                'new_post' as type,
                p.id,
                p.title,
                p.created_at,
                u.username,
                u.profile_picture,
                p.status
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id = ?
            AND p.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            
            UNION ALL
            
            SELECT 
                'interaction' as type,
                p2.id,
                p2.title,
                GREATEST(
                    COALESCE(MAX(c.created_at), '1970-01-01'),
                    COALESCE(MAX(l.created_at), '1970-01-01')
                ) as created_at,
                u2.username,
                u2.profile_picture,
                p2.status
            FROM posts p2
            JOIN users u2 ON p2.user_id = u2.id
            LEFT JOIN comments c ON p2.id = c.post_id
            LEFT JOIN likes l ON p2.id = l.post_id
            WHERE p2.user_id = ?
            GROUP BY p2.id
            ORDER BY created_at DESC
            LIMIT ?";
}

$stmt = $conn->prepare($query);
if ($isAdmin) {
    $stmt->bind_param("i", $limit);
} else {
    $stmt->bind_param("iii", $user_id, $user_id, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $creationTime = date('h:i A', strtotime($row['created_at']));
    
    echo '<div class="notification-item">';
    
    // Show different content based on notification type
    if ($row['type'] === 'post_request' && $isAdmin) {
        echo '<a href="post-requests.php" class="notification-link">';
        echo '<div class="notification-content">';
        echo '<img src="' . ($row['profile_picture'] ?: 'assets/default-avatar.png') . '" alt="Profile" class="notification-avatar">';
        echo '<div class="notification-text">';
        echo '<strong>' . htmlspecialchars($row['username']) . '</strong> submitted a new post for review: "' . htmlspecialchars($row['title']) . '"';
        echo '<div class="notification-time">' . $creationTime . '</div>';
        echo '</div></div></a>';
    } elseif ($row['type'] === 'new_post') {
        echo '<a href="explore.php" class="notification-link">';
        echo '<div class="notification-content">';
        echo '<img src="' . ($row['profile_picture'] ?: 'assets/default-avatar.png') . '" alt="Profile" class="notification-avatar">';
        echo '<div class="notification-text">';
        echo '<strong>' . htmlspecialchars($row['username']) . '</strong> created a new post: "' . htmlspecialchars($row['title']) . '"';
        echo '<div class="notification-time">' . $creationTime . '</div>';
        echo '</div></div></a>';
    } else {
        echo '<a href="explore.php" class="notification-link">';
        echo '<div class="notification-content">';
        echo '<img src="' . ($row['profile_picture'] ?: 'assets/default-avatar.png') . '" alt="Profile" class="notification-avatar">';
        echo '<div class="notification-text">';
        echo 'New activity on your post: "' . htmlspecialchars($row['title']) . '"';
        echo '<div class="notification-time">' . $creationTime . '</div>';
        echo '</div></div></a>';
    }
    
    echo '</div>';
}

function getTimeAgo($timestamp) {
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return date('h:i A', $timestamp);
    } elseif ($difference < 3600) {
        return floor($difference / 60) . "m ago";
    } elseif ($difference < 86400) {
        return floor($difference / 3600) . "h ago";
    } elseif ($difference < 604800) {
        return floor($difference / 86400) . "d ago";
    } else {
        return date('M j', $timestamp);
    }
}
?>

<style>
.notification-item {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.notification-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.notification-text {
    flex: 1;
    font-size: 0.9rem;
    color: #333;
}

.notification-time {
    font-size: 0.8rem;
    color: #666;
    margin-top: 4px;
}

strong {
    color: #365486;
}
</style> 