<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Please log in first');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    echo "<h3>FILES Data:</h3>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_type = $_FILES['file']['type'];
        
        echo "<h3>File Info:</h3>";
        echo "Name: $file_name<br>";
        echo "Size: $file_size bytes<br>";
        echo "Type: $file_type<br>";
        echo "Temp: $file_tmp<br>";
        
        // Create unique filename
        $unique_name = uniqid() . '_' . $file_name;
        $uploaded_file = $upload_dir . $unique_name;
        
        if (move_uploaded_file($file_tmp, $uploaded_file)) {
            echo "<h3 style='color: green;'>File uploaded successfully!</h3>";
            echo "Saved as: $uploaded_file<br>";
            
            // Test database insertion
            $stmt = $conn->prepare("INSERT INTO posts (user_id, title, description, file_path, status) VALUES (?, ?, ?, ?, ?)");
            $title = "Test Upload";
            $description = "Test file upload";
            $status = "approved";
            $stmt->bind_param('issss', $user_id, $title, $description, $uploaded_file, $status);
            
            if ($stmt->execute()) {
                echo "<h3 style='color: green;'>Database insertion successful!</h3>";
                echo "Post ID: " . $conn->insert_id . "<br>";
            } else {
                echo "<h3 style='color: red;'>Database insertion failed!</h3>";
                echo "Error: " . $stmt->error . "<br>";
            }
        } else {
            echo "<h3 style='color: red;'>File upload failed!</h3>";
        }
    } else {
        echo "<h3 style='color: red;'>No file uploaded or upload error!</h3>";
        if (isset($_FILES['file'])) {
            echo "Upload error code: " . $_FILES['file']['error'] . "<br>";
        }
    }
    
    echo "<hr>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload Test</title>
</head>
<body>
    <h1>File Upload Test</h1>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label>Select File:</label><br>
            <input type="file" name="file" accept="image/*,video/*,audio/*" required>
        </div>
        <br>
        <button type="submit">Upload Test</button>
    </form>
    
    <script>
        document.querySelector('input[type="file"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                console.log('File selected:', file.name, 'Size:', file.size, 'Type:', file.type);
            }
        });
    </script>
</body>
</html>
