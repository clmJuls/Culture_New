<?php
require 'db_conn.php';
require 'services/NotificationService.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

// Initialize notification service
$notificationService = new NotificationService($conn);

// Get notifications
$notifications = $notificationService->getUserNotifications($user_id, $limit);

// Display notifications
foreach ($notifications as $notification) {
    $unreadClass = !$notification['is_read'] ? 'unread' : 'read';
    echo '<div class="notification-item ' . $unreadClass . '">';
    echo '<a href="' . htmlspecialchars($notification['redirect_url']) . '" 
             class="notification-link"
             onclick="markAsRead(' . $notification['id'] . ')">';
    echo '<div class="notification-content">';
    
    // Circle icon
    echo '<div class="notification-circle"></div>';
    
    echo '<div class="notification-text">';
    echo '<div class="notification-title">' . htmlspecialchars($notification['title']) . '</div>';
    echo '<div class="notification-message">' . htmlspecialchars($notification['message']) . '</div>';
    echo '<div class="notification-time">' . date('h:i A', strtotime($notification['created_at'])) . '</div>';
    echo '</div>';
    
    echo '</div></a></div>';
}
?>

<style>
.notification-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
    width: 100%;
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
    width: 100%;
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    width: 100%;
}

.notification-circle {
    width: 8px;
    height: 8px;
    background-color: #365486;
    border-radius: 50%;
    margin-top: 6px;
    flex-shrink: 0;
}

.notification-text {
    flex: 1;
    min-width: 0;
    padding-right: 10px;
}

.notification-title {
    font-weight: 600;
    color: #000;
    margin-bottom: 4px;
    font-size: 0.95rem;
}

.notification-message {
    color: #666;
    margin-bottom: 4px;
    font-size: 0.9rem;
}

.notification-time {
    font-size: 0.8rem;
    color: #666;
    text-align: right;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-item.unread .notification-circle {
    background-color: #365486;
}

.notification-item.read .notification-circle {
    background-color: #ccc;
}
</style> 