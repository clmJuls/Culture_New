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
    $author_id = $_SESSION['user_id'];

    // Handle file upload
    $target_dir = "uploads/geography/";
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

    // Resize and save image
    $maxWidth = 800; // Maximum width for the image
    $maxHeight = 600; // Maximum height for the image

    list($width, $height) = getimagesize($_FILES["image"]["tmp_name"]);
    
    // Calculate new dimensions while maintaining aspect ratio
    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = min($maxWidth/$width, $maxHeight/$height);
        $new_width = round($width * $ratio);
        $new_height = round($height * $ratio);
    } else {
        $new_width = $width;
        $new_height = $height;
    }

    // Create new image
    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Handle different image types
    switch($file_extension) {
        case 'jpeg':
        case 'jpg':
            $source = imagecreatefromjpeg($_FILES["image"]["tmp_name"]);
            break;
        case 'png':
            $source = imagecreatefrompng($_FILES["image"]["tmp_name"]);
            // Preserve transparency
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            break;
        case 'gif':
            $source = imagecreatefromgif($_FILES["image"]["tmp_name"]);
            break;
        default:
            die('Unsupported image type');
    }

    // Resize image
    imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Save image with appropriate quality/compression
    switch($file_extension) {
        case 'jpeg':
        case 'jpg':
            imagejpeg($new_image, $target_file, 80); // 80 is the quality (0-100)
            break;
        case 'png':
            imagepng($new_image, $target_file, 8); // 8 is the compression level (0-9)
            break;
        case 'gif':
            imagegif($new_image, $target_file);
            break;
    }

    // Free up memory
    imagedestroy($new_image);
    imagedestroy($source);

    $image_url = $target_file;

    // Insert into database
    $sql = "INSERT INTO geography_posts (title, description, content, image_url, user_id) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $title, $description, $content, $image_url, $author_id);

    if ($stmt->execute()) {
        header("Location: geography.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?> 