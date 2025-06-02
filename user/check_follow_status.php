<?php
require_once 'db_conn.php';
session_start();

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User must be logged in',
        'is_following' => false
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $current_user_id = $_SESSION['user_id'];
        $target_user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

        if (!$target_user_id) {
            throw new Exception('Invalid user ID');
        }

        // Check if already following
        $check_query = "SELECT * FROM user_follows WHERE follower_id = ? AND following_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $current_user_id, $target_user_id);
        $stmt->execute();
        
        $is_following = $stmt->get_result()->num_rows > 0;

        echo json_encode([
            'status' => 'success',
            'is_following' => $is_following
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'is_following' => false
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method',
        'is_following' => false
    ]);
}

$conn->close();
?> 