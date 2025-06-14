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
    echo '<div class="notification-item">';
    echo '<a href="' . htmlspecialchars($notification['redirect_url']) . '" 
             class="notification-link" 
             onclick="markAsRead(' . $notification['id'] . ')">';
    echo '<div class="notification-content">';
    
    // Simple circle icon
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
    padding: 12px 15px;
    width: 100%;
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

.notification-circle {
    width: 40px;
    height: 40px;
    background-color: #e7f1ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #365486;
}

.notification-text {
    flex: 1;
    font-size: 0.9rem;
    color: #333;
}

.notification-title {
    font-weight: 600;
    color: #365486;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.notification-message {
    color: #4a4947;
    margin-bottom: 4px;
}

.notification-time {
    font-size: 0.8rem;
    color: #666;
}

.notification-item.unread {
    background-color: #f0f7ff;
    border-left: 4px solid #365486;
}

.notification-item.read {
    background-color: #ffffff;
}
</style> 