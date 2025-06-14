<?php
require '../db_conn.php';
require '../services/NotificationService.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['notification_id'])) {
    http_response_code(400);
    exit('Invalid request');
}

$user_id = $_SESSION['user_id'];
$notification_id = (int)$_POST['notification_id'];

$notificationService = new NotificationService($conn);
$result = $notificationService->markAsRead($notification_id, $user_id);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to mark notification as read']);
} 