<?php
require 'db_conn.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Check if the current user is an admin
$user_id = $_SESSION['user_id'];
$check_admin = $conn->prepare("SELECT isAdmin FROM users WHERE id = ?");
$check_admin->bind_param("i", $user_id);
$check_admin->execute();
$result = $check_admin->get_result();
$user_data = $result->fetch_assoc();

if (!$user_data['isAdmin']) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// Get user data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $conn->prepare("SELECT id, username, email, isAdmin, isPremium FROM users WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user ID']);
}
?> 