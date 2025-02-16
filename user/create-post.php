<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to update your information.');
            window.location.href = '../user/auth/login.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $culture_elements = isset($_POST['culture_elements']) ? implode(',', $_POST['culture_elements']) : '';
    $learning_styles = isset($_POST['learning_styles']) ? implode(',', $_POST['learning_styles']) : ''; // Capture learning styles
    $uploaded_file = '';

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $uploaded_file = $upload_dir . uniqid() . '_' . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file);
    }

    // Insert post into database
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, description, file_path, culture_elements, learning_styles) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssss', $user_id, $title, $description, $uploaded_file, $culture_elements, $learning_styles); // Add learning_styles

    if ($stmt->execute()) {
        echo "<script>
                alert('Post created successfully!');
                window.location.href = 'create-post.php';
              </script>";
    } else {
        echo "<script>
                alert('An error occurred while creating the post.');
              </script>";
    }

    $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  </head>
    <body>
    <style>
    /* General */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('https://socialstudieshelp.com/wp-content/uploads/2024/02/Exploring-the-Cultural-Diversity-of-Europe.webp');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            justify-content: center; 
            align-items: center;
        }
        .drag-drop-zone {
        width: 100%;
        height: 200px;
        border: 2px dashed #ccc;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
        margin: 15px 0;
    }

    .drag-drop-zone.dragover {
        background-color: #e3f2fd;
        border-color: #2196f3;
    }

    .drag-drop-zone i {
        font-size: 48px;
        color: #666;
        margin-bottom: 10px;
    }

    .drag-drop-zone p {
        margin: 0;
        color: #666;
        text-align: center;
    }

    .file-preview {
        margin-top: 15px;
        text-align: center;
    }

    .file-preview img, 
    .file-preview video {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .file-name {
        margin-top: 8px;
        font-size: 14px;
        color: #666;
    }

    .remove-file {
        background: #ff4444;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 8px;
    }
    </style>
    
    <!-- Navigation Bar -->
    <div class="navbar">
        <div style="display: flex; align-items: center;">
           <img src="assets/logo/logo.png "alt="Kulturifiko Logo">
            <h1>Kulturabase</h1>
        </div>
        <div>
            <a href="Home.php">Home</a>
            <a href="create-post.php" class="active">+ Create</a>
            <a href="explore.php">Explore</a>
            <a href="notification.php">Notification</a>
            <div class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown()">Menu</a>
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="settings.php">Settings</a>
                </div>
            </div>
            <a href="#" onclick="handleLogout()">Log Out</a>
        </div>
    </div>

    <style>
    /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #365486;
            padding: 20px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar img {
            height: 50px;
            width: auto;
        }

        .navbar h1 {
            color: #DCF2F1;
            font-size: 2rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .navbar a {
            color: #DCF2F1;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1rem;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 30px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #7FC7D9;
            color: #0F1035;
        }

        .navbar a.active {
            background-color: #1e3c72;
            color: #fff;
        }
        
    /* Dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
        }

        .dropdown-content a {
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-content a:last-child {
            border-bottom: none;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

    /* Toggle class for show/hide */
        .show {
            display: block;
        }
    </style>

    <script>
        function toggleDropdown() {
            var dropdownContent = document.querySelector(".dropdown-content");
            dropdownContent.classList.toggle("show");
        }
        function handleLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'auth/logout.php';
            }
        }
    </script>


<div class="container" style="max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); background-color: #f9f9f9; position: relative; display: flex; flex-direction: column; height: 500px;">
    <h1 style="text-align: center; margin-bottom: 20px;">Create a Post</h1>

    <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px; flex: 1; overflow-y: auto; padding-bottom: 50px;">
        <!-- Title Input -->
        <input type="text" name="title" placeholder="Title" maxlength="300" required style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%;">

        <!-- Description Input -->
        <textarea name="description" placeholder="Caption..." style="padding: 30px; border: 1px solid #ccc; border-radius: 4px; width: 100%; height: 120px;" required></textarea>

        <!-- File Upload -->
        <div class="drag-drop-zone" id="drag-drop-zone">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Drag & drop your file here</p>
            <p>or</p>
            <p>Click to select a file</p>
            <input type="file" name="file" id="file-input" accept="image/*,video/*" style="display: none;">
        </div>
        <div class="file-preview" id="file-preview"></div>
        <!-- Culture Elements (Hidden for Non-Admin Users) -->
        <?php if ($_SESSION['isAdmin'] == 1) { ?>
            <div id="culture-elements" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <h3 style="margin-bottom: 10px;">Select Culture Elements</h3>
                <label><input type="checkbox" name="culture_elements[]" value="Geography"> Geography</label><br>
                <label><input type="checkbox" name="culture_elements[]" value="History"> History</label><br>
                <label><input type="checkbox" name="culture_elements[]" value="Demographics"> Demographics</label><br>
                <label><input type="checkbox" name="culture_elements[]" value="Culture"> Culture</label><br>
            </div>
        <?php } ?>

        <!-- Learning Styles -->
        <div style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <h3 style="margin-bottom: 10px;">Select Learning Styles</h3>
            <label><input type="checkbox" name="learning_styles[]" value="Visual"> Visual</label><br>
            <label><input type="checkbox" name="learning_styles[]" value="Auditory & Oral"> Auditory & Oral</label><br>
            <label><input type="checkbox" name="learning_styles[]" value="Read & Write"> Read & Write</label><br>
            <label><input type="checkbox" name="learning_styles[]" value="Kinesthetic"> Kinesthetic</label><br>
        </div>

        <!-- Submit Button -->
        <button type="submit" style="padding: 10px; background-color: #007bff; color: white; font-size: 16px; border: none; border-radius: 4px; cursor: pointer;">Post</button>
    </form>
</div>



  
  <script>
        const dragDropZone = document.getElementById('drag-drop-zone');
    const fileInput = document.getElementById('file-input');
    const filePreview = document.getElementById('file-preview');
    const form = document.getElementById('post-form');

    // Handle click on drag-drop zone
    dragDropZone.addEventListener('click', () => fileInput.click());

    // Handle drag events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dragDropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Add/remove dragover class
    ['dragenter', 'dragover'].forEach(eventName => {
        dragDropZone.addEventListener(eventName, () => {
            dragDropZone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dragDropZone.addEventListener(eventName, () => {
            dragDropZone.classList.remove('dragover');
        }, false);
    });

    // Handle dropped files
    dragDropZone.addEventListener('drop', handleDrop, false);
    fileInput.addEventListener('change', handleFileSelect, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            fileInput.files = files;
            showPreview(file);
        }
    }

    function showPreview(file) {
        filePreview.innerHTML = '';
        
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.file = file;
            filePreview.appendChild(img);

            const reader = new FileReader();
            reader.onload = (e) => { img.src = e.target.result; };
            reader.readAsDataURL(file);
        } else if (file.type.startsWith('video/')) {
            const video = document.createElement('video');
            video.controls = true;
            filePreview.appendChild(video);

            const reader = new FileReader();
            reader.onload = (e) => { video.src = e.target.result; };
            reader.readAsDataURL(file);
        }

        // Add file name and remove button
        const fileInfo = document.createElement('div');
        fileInfo.className = 'file-name';
        fileInfo.textContent = file.name;
        filePreview.appendChild(fileInfo);

        const removeButton = document.createElement('button');
        removeButton.className = 'remove-file';
        removeButton.textContent = 'Remove';
        removeButton.onclick = removeFile;
        filePreview.appendChild(removeButton);
    }

    function removeFile(e) {
        e.preventDefault();
        fileInput.value = '';
        filePreview.innerHTML = '';
    }
    function previewFile() {
        const fileInput = document.querySelector('input[name="file"]');
        const filePreview = document.getElementById('file-preview');

        filePreview.innerHTML = '';

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewElement = document.createElement('img');
                previewElement.src = e.target.result;
                previewElement.style.maxWidth = '100%';
                previewElement.style.height = 'auto';
                filePreview.appendChild(previewElement);
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }

  </script>
  

<style>
  .container {
    position: fixed;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            width: 100%;
            max-width: 600px;
            padding: 40px;
        }

        h1 {
            text-align: center;
            font-size: 30px;
            margin-bottom: 10px;
            color: #333;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            margin-top: 10px;
            background-color: #fff;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        textarea {
            height: 100px;
            resize: none;
        }

/* Styling for file preview */
.file-preview {
  margin-top: 10px;
  text-align: center;
}

.file-preview-image {
  max-width: 100%;
  max-height: 250px;
  border: 1px solid #ddd;
  padding: 10px;
}

.file-preview-video {
  max-width: 100%;
  max-height: 250px;
  border: 1px solid #ddd;
  padding: 10px;
}

        .post-btn {
            display: block;
            width: 100%;
            background-color: #0d11d6;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .post-btn:hover {
            background-color: #1062c0;
        }
</style>

<style>
  /* Container for main content */
  .main-container {
    position: fixed;
    width: 80%; 
    max-height: 160px;
    max-width: 200px; 
    background: rgba(255, 255, 255, 0.8); 
    padding: 20px;
    border-radius: 8px;
    margin-left: 900px; 
}

.right-bar h2 {
    font-size: 22px;
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}
    </style>

</body>
</head>
</html>