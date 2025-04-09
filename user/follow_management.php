<?php
require_once 'db_conn.php';
session_start();

// Ensure no HTML errors are output
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User must be logged in'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $current_user_id = $_SESSION['user_id'];
        $target_user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if (!$target_user_id) {
            throw new Exception('Invalid user ID');
        }

        // Start transaction
        $conn->begin_transaction();

        if ($action === 'follow') {
            // Check if already following
            $check_query = "SELECT * FROM user_follows WHERE follower_id = ? AND following_id = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ii", $current_user_id, $target_user_id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception('Already following this user');
            }

            // Add follow relationship
            $follow_query = "INSERT INTO user_follows (follower_id, following_id) VALUES (?, ?)";
            $stmt = $conn->prepare($follow_query);
            $stmt->bind_param("ii", $current_user_id, $target_user_id);
            $stmt->execute();

            // Update follower counts
            $update_counts = "UPDATE users SET followers_count = followers_count + 1 WHERE id = ?";
            $stmt = $conn->prepare($update_counts);
            $stmt->bind_param("i", $target_user_id);
            $stmt->execute();

            // Update following counts
            $update_following = "UPDATE users SET following_count = following_count + 1 WHERE id = ?";
            $stmt = $conn->prepare($update_following);
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();

        } elseif ($action === 'unfollow') {
            // Remove follow relationship
            $unfollow_query = "DELETE FROM user_follows WHERE follower_id = ? AND following_id = ?";
            $stmt = $conn->prepare($unfollow_query);
            $stmt->bind_param("ii", $current_user_id, $target_user_id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception('Not following this user');
            }

            // Update follower counts
            $update_counts = "UPDATE users SET followers_count = GREATEST(0, followers_count - 1) WHERE id = ?";
            $stmt = $conn->prepare($update_counts);
            $stmt->bind_param("i", $target_user_id);
            $stmt->execute();

            // Update following counts
            $update_following = "UPDATE users SET following_count = GREATEST(0, following_count - 1) WHERE id = ?";
            $stmt = $conn->prepare($update_following);
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();

        } else {
            throw new Exception('Invalid action');
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => $action === 'follow' ? 'Successfully followed user' : 'Successfully unfollowed user'
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn->connect_error === null) {
            $conn->rollback();
        }
        
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