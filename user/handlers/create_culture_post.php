<?php
// Prevent any output before our JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

session_start();
require_once '../db_conn.php';

try {
    // Check if user is admin
    if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
        throw new Exception('Unauthorized access');
    }

    // Validate and process the uploaded image
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Image upload failed');
    }

    // Create uploads directory if it doesn't exist
    $uploadDir = '../uploads/culture/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Generate unique filename
    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
    $uploadPath = $uploadDir . $fileName;
    $imageUrl = 'uploads/culture/' . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save image');
    }

    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $userId = $_SESSION['user_id'];

    // Insert post into database - using correct table name with blog_db prefix
    $query = "INSERT INTO blog_db.culture_posts (title, description, content, category, image_url, user_id) 
              VALUES ('$title', '$description', '$content', '$category', '$imageUrl', $userId)";

    if (!mysqli_query($conn, $query)) {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }

    $postId = mysqli_insert_id($conn);
    
    // Since we don't have a post_culture_elements table, we'll skip that part
    // If you need to store culture elements, you'll need to create that table first
    
    echo json_encode(['success' => true, 'message' => 'Post created successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?> 