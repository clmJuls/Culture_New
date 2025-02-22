<?php
session_start();
require_once 'db_conn.php';

// Check if user is admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    die('Unauthorized access');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $author_id = $_SESSION['user_id'];

    // Handle file upload
    $target_dir = "uploads/history/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $file_name = uniqid() . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    
    // Check if image file is actual image
    if (getimagesize($_FILES["image"]["tmp_name"]) === false) {
        die("File is not an image.");
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert into database
        $sql = "INSERT INTO history_posts (title, description, content, category, image_url, user_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $title, $description, $content, $category, $target_file, $author_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to create post']);
        }
    } else {
        echo json_encode(['error' => 'Failed to upload image']);
    }
}
?> 