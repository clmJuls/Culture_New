<?php
// Prevent any output before JSON response
ob_start();

// Update the path to db_conn.php - adjust this path based on your directory structure
require_once 'db_conn.php';  // Go up two levels to find db_conn.php
// OR if db_conn.php is in the root directory:
// require_once '../db_conn.php';

session_start();

// Ensure only JSON is output
header('Content-Type: application/json');

// Disable error reporting to prevent HTML errors in output
error_reporting(0);

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not logged in');
    }

    $user_id = $_SESSION['user_id'];

    // Verify premium status
    $query = "SELECT isPremium FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user['isPremium']) {
        throw new Exception('Premium membership required');
    }

    if (!isset($_FILES['background'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['background'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Allowed types: JPG, PNG, GIF');
    }
    
    $max_size = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $max_size) {
        throw new Exception('File too large. Maximum size: 10MB');
    }
    
    // Create uploads directory
    $upload_dir = 'uploads/backgrounds/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;
    
    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to upload file');
    }
    
    // Update database
    $db_filepath = 'uploads/backgrounds/' . $filename;
    $query = "UPDATE users SET profile_background = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $db_filepath, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update database: ' . $conn->error);
    }

    // Clear any output buffers
    ob_clean();
    
    echo json_encode([
        'success' => true,
        'message' => 'Background updated successfully',
        'path' => $db_filepath
    ]);

} catch (Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
$conn->close(); 