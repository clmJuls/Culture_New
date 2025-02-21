<?php
session_start();
require_once 'db_conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $author_id = $_SESSION['user_id'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/history/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $target_file;
            }
        }
    }

    // Insert into database
    $sql = "INSERT INTO history_posts (title, description, image_url, category, author_id, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $title, $description, $image_url, $category, $author_id, $status);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "History post created successfully!";
        header('Location: history.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Error creating post: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create History Post - Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        .create-post-form {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        textarea {
            height: 200px;
        }

        .submit-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .drop-zone {
            border: 1px dashed #ccc;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .drop-zone__content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .upload-icon {
            color: #0d6efd;
            font-size: 40px;
            margin-bottom: 10px;
        }

        .drop-zone__text {
            color: #444;
            font-size: 16px;
            margin: 0;
        }

        .drop-zone__or {
            color: #666;
            margin: 5px 0;
        }

        .browse-btn {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .browse-btn:hover {
            border-color: #0d6efd;
            color: #0d6efd;
        }

        .drop-zone__input {
            display: none;
        }

        .drop-zone__preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
        }

        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
        }

        .remove-image {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }

        .remove-image:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .remove-image i {
            color: #dc3545;
            font-size: 16px;
        }

        .drop-zone--over {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }
    </style>
</head>
<body>
    <?php include 'components/layout/admin/navbar.php'; ?>

    <div class="create-post-form">
        <h2>Create New History Post</h2>
        
        <form action="create_history_post.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="ancient">Ancient Civilizations</option>
                    <option value="wars">World Wars</option>
                    <option value="renaissance">Renaissance</option>
                    <option value="movements">Revolutionary Movements</option>
                    <option value="colonialism">Colonialism</option>
                    <option value="evolution">Cultural Evolution</option>
                    <option value="figures">Historical Figures</option>
                    <option value="heritage">Cultural Heritage</option>
                </select>
            </div>

            <div class="form-group">
                <label>Featured Image</label>
                <div class="drop-zone">
                    <div class="drop-zone__content">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <p class="drop-zone__text">Drag & drop your file here</p>
                        <p class="drop-zone__or">or</p>
                        <button type="button" class="browse-btn">Browse Files</button>
                    </div>
                    <input type="file" name="image" class="drop-zone__input" accept="image/*" required>
                    <div class="drop-zone__preview" style="display: none;">
                        <img src="" alt="Preview" class="preview-image">
                        <button type="button" class="remove-image">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">Create Post asd</button>
        </form>
    </div>

    <?php include 'components/layout/guest/sidebar.php'; ?>
    <?php include 'components/widgets/chat.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.querySelector('.drop-zone');
        const input = dropZone.querySelector('.drop-zone__input');
        const content = dropZone.querySelector('.drop-zone__content');
        const preview = dropZone.querySelector('.drop-zone__preview');
        const previewImg = preview.querySelector('.preview-image');
        const browseBtn = dropZone.querySelector('.browse-btn');
        const removeBtn = preview.querySelector('.remove-image');

        // Handle browse button click
        browseBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            input.click();
        });

        // Handle file selection
        input.addEventListener('change', function() {
            if (this.files[0]) {
                showPreview(this.files[0]);
            }
        });

        // Prevent defaults for drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Handle drop zone highlighting
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        // Handle dropped files
        dropZone.addEventListener('drop', handleDrop, false);

        // Handle remove button click
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            resetDropZone();
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            dropZone.classList.add('drop-zone--over');
        }

        function unhighlight(e) {
            dropZone.classList.remove('drop-zone--over');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length) {
                input.files = files;
                showPreview(files[0]);
            }
        }

        function showPreview(file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    content.style.display = 'none';
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                alert('Please upload an image file');
                resetDropZone();
            }
        }

        function resetDropZone() {
            input.value = '';
            preview.style.display = 'none';
            content.style.display = 'flex';
            previewImg.src = '';
        }
    });
    </script>
</body>
</html> 