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

// Get user's premium status from database
$stmt = $conn->prepare("SELECT isPremium FROM users WHERE id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$isPremium = $user['isPremium'];
$stmt->close();

// Define file size limits based on premium status
$maxFileSize = $isPremium ? (10 * 1024 * 1024) : (2 * 1024 * 1024); // 10MB or 2MB in bytes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = array(); // Array to store validation errors

    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $culture_elements = isset($_POST['culture_elements']) ? implode(',', $_POST['culture_elements']) : '';
    $learning_styles = isset($_POST['learning_styles']) ? implode(',', $_POST['learning_styles']) : '';
    $uploaded_file = '';

    // Handle file upload with validation
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_size = $_FILES['file']['size'];
        $max_size = $isPremium ? (10 * 1024 * 1024) : (2 * 1024 * 1024);
        $size_limit_text = $isPremium ? "10MB" : "2MB";

        if ($file_size > $max_size) {
            $errors[] = "File size exceeds {$size_limit_text} limit" . ($isPremium ? ' for Premium users' : '');
        } else {
            $upload_dir = 'uploads/';
            $uploaded_file = $upload_dir . uniqid() . '_' . basename($_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file);
        }
    }

    // Only proceed with database insertion if there are no errors
    if (empty($errors)) {
        $status = (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) ? 'approved' : 'pending';

        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, description, file_path, culture_elements, learning_styles, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issssss', $user_id, $title, $description, $uploaded_file, $culture_elements, $learning_styles, $status);

        if ($stmt->execute()) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showSuccessModal();
                    });
                  </script>";
        } else {
            $errors[] = "An error occurred while creating the post.";
        }
        $stmt->close();
    }

    // If there are errors, display them
    if (!empty($errors)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const errorMessages = " . json_encode($errors) . ";
                    showErrors(errorMessages);
                });
              </script>";
    }
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
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 80px;
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
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        .file-preview img,
        .file-preview video {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            transition: background-color 0.3s ease;
        }

        .remove-file:hover {
            background: #cc0000;
        }
    </style>

    <!-- Navigation Bar -->

    <?php
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
        include 'components/layout/admin/navbar.php';
    } else {
        include 'components/layout/guest/navbar.php';
    }
    ?>

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
                <input type="file" name="file" id="file-input"
                    accept="image/*,video/mp4,video/webm,video/mov,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain"
                    style="display: none;">
            </div>
            <div class="file-size-info" style="color: #666;">
                <?php if ($isPremium): ?>
                    <i class="fas fa-crown" style="color: #FFD700;"></i>
                    <span>Premium user: Upload files up to 10MB</span>
                <?php else: ?>
                    <i class="fas fa-info-circle"></i>
                    <span>Free user: Upload files up to 2MB</span>
                <?php endif; ?>
            </div>
            <div class="file-preview" id="file-preview"></div>

            <!-- Learning Styles -->
            <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="color: #1a73e8; font-size: 16px; margin-bottom: 15px;">Select Learning Styles</h3>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="learning_styles[]" value="Visual">
                            <span style="color: #202124; font-size: 14px;">Visual</span>
                        </label>
                        <i class="fas fa-info-circle info-icon"
                            data-info="Visual learners prefer information presented through images, diagrams, charts, and other visual aids. They learn best when concepts are illustrated visually."
                            style="color: #1a73e8; cursor: help;"
                            onmouseover="showInfo(event)"
                            onmouseout="hideInfo()">
                        </i>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="learning_styles[]" value="Auditory & Oral">
                            <span style="color: #202124; font-size: 14px;">Auditory & Oral</span>
                        </label>
                        <i class="fas fa-info-circle info-icon"
                            data-info="Auditory and oral learners process information best through listening and speaking. They benefit from discussions, lectures, and verbal explanations."
                            style="color: #1a73e8; cursor: help;"
                            onmouseover="showInfo(event)"
                            onmouseout="hideInfo()">
                        </i>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="learning_styles[]" value="Read & Write">
                            <span style="color: #202124; font-size: 14px;">Read & Write</span>
                        </label>
                        <i class="fas fa-info-circle info-icon"
                            data-info="Read and write learners prefer written information. They learn best through reading texts and writing notes, making lists, and working with written materials."
                            style="color: #1a73e8; cursor: help;"
                            onmouseover="showInfo(event)"
                            onmouseout="hideInfo()">
                        </i>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="learning_styles[]" value="Kinesthetic">
                            <span style="color: #202124; font-size: 14px;">Kinesthetic</span>
                        </label>
                        <i class="fas fa-info-circle info-icon"
                            data-info="Kinesthetic learners learn through physical activities and hands-on experiences. They prefer learning by doing, experimenting, and engaging in practical applications."
                            style="color: #1a73e8; cursor: help;"
                            onmouseover="showInfo(event)"
                            onmouseout="hideInfo()">
                        </i>
                    </div>
                </div>
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
        dragDropZone.addEventListener('click', () => fileInput.click());

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dragDropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
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
            const file = files[0];
            if (!file) return;

            // Remove size validation here to allow any file upload initially
            showPreview(file);

            // Update learning style checkboxes based on file type
            const visualCheckbox = document.querySelector('input[name="learning_styles[]"][value="Visual"]');
            const auditoryCheckbox = document.querySelector('input[name="learning_styles[]"][value="Auditory & Oral"]');
            const readWriteCheckbox = document.querySelector('input[name="learning_styles[]"][value="Read & Write"]');
            const kinestheticCheckbox = document.querySelector('input[name="learning_styles[]"][value="Kinesthetic"]');
            [visualCheckbox, auditoryCheckbox, readWriteCheckbox, kinestheticCheckbox].forEach(checkbox => {
                if (checkbox) checkbox.checked = false;
            });
            if (file.type.startsWith('image/')) {
                if (visualCheckbox) visualCheckbox.checked = true;
            } else if (file.type.startsWith('video/')) {
                if (visualCheckbox) visualCheckbox.checked = true;
                if (auditoryCheckbox) auditoryCheckbox.checked = true;
            } else if (file.type === 'audio/mp3' || file.type === 'audio/mpeg') {
                if (auditoryCheckbox) auditoryCheckbox.checked = true;
            } else if (
                file.type === 'application/pdf' ||
                file.type === 'application/msword' ||
                file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
                file.type === 'application/vnd.ms-excel' ||
                file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                file.type === 'text/plain'
            ) {
                if (readWriteCheckbox) readWriteCheckbox.checked = true;
            }
        }

        function showPreview(file) {
            filePreview.innerHTML = '';
            dragDropZone.style.display = 'none';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.file = file;
                filePreview.appendChild(img);

                const reader = new FileReader();
                reader.onload = (e) => {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.controls = true;
                filePreview.appendChild(video);

                const reader = new FileReader();
                reader.onload = (e) => {
                    video.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Handle documents
                const docIcon = document.createElement('i');
                docIcon.className = getDocumentIconClass(file.type);
                docIcon.style.fontSize = '48px';
                docIcon.style.color = '#365486';
                docIcon.style.margin = '20px 0';
                filePreview.appendChild(docIcon);
            }

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

        function removeFile() {
            filePreview.innerHTML = '';
            dragDropZone.style.display = 'flex';
            fileInput.value = '';
            const checkboxes = document.querySelectorAll('input[name="learning_styles[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
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

        // Helper function to get the appropriate Font Awesome icon class
        function getDocumentIconClass(fileType) {
            switch (fileType) {
                case 'application/pdf':
                    return 'fas fa-file-pdf';
                case 'application/msword':
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    return 'fas fa-file-word';
                case 'application/vnd.ms-excel':
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    return 'fas fa-file-excel';
                case 'text/plain':
                    return 'fas fa-file-alt';
                default:
                    return 'fas fa-file';
            }
        }

        function showErrors(errors) {
            const errorDiv = document.getElementById('error-messages');
            errorDiv.innerHTML = errors.map(error => `<div>${error}</div>`).join('');
            errorDiv.style.display = 'block';

            // Scroll to error messages
            errorDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Pass PHP variables to JavaScript
        const userIsPremium = <?php echo json_encode($isPremium); ?>;
        const maxFileSize = <?php echo json_encode($maxFileSize); ?>;

        // Update your form validation script
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            const errors = [];
            const fileInput = document.getElementById('file-input');

            // Validate file size if a file is selected
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const sizeLimit = userIsPremium ? (10 * 1024 * 1024) : (2 * 1024 * 1024);
                const sizeLimitText = userIsPremium ? "10MB" : "2MB";

                if (file.size > sizeLimit) {
                    errors.push(`File size exceeds ${sizeLimitText} limit${userIsPremium ? ' for Premium users' : ''}`);
                }
            }

            // If there are validation errors, show them in the modal
            if (errors.length > 0) {
                showValidationModal(errors);
            } else {
                // If no errors, submit the form
                this.submit();
            }
        });

        function showValidationModal(errors) {
            const modal = document.getElementById('validationModal');
            const messagesDiv = document.getElementById('validation-messages');
            messagesDiv.innerHTML = errors.map(error => `<div>${error}</div>`).join('');
            modal.style.display = 'block';
        }

        function closeValidationModal() {
            document.getElementById('validationModal').style.display = 'none';
        }

        // Close modal when clicking the X
        document.querySelectorAll('.modal .close').forEach(closeBtn => {
            closeBtn.onclick = function() {
                this.closest('.modal').style.display = 'none';
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>


    <style>
        .container {
            position: relative;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            width: 100%;
            max-width: 800px;
            padding: 40px;
            margin: 120px auto 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('https://socialstudieshelp.com/wp-content/uploads/2024/02/Exploring-the-Cultural-Diversity-of-Europe.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 80px 20px;
        }

        /* Form container */
        form {
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        /* Scrollbar styling */
        form::-webkit-scrollbar {
            width: 8px;
        }

        form::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        form::-webkit-scrollbar-thumb {
            background: #365486;
            border-radius: 4px;
        }

        form::-webkit-scrollbar-thumb:hover {
            background: #2a4268;
        }

        /* Form elements styling */
        input[type="text"],
        textarea {
            width: 100%;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #ddd;
            font-size: 16px;
            margin-top: 10px;
            background-color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        /* Culture Elements and Learning Styles sections */
        #culture-elements,
        div[style*="padding: 10px; border: 1px solid #ccc;"] {
            background-color: #fff;
            padding: 20px !important;
            border-radius: 8px !important;
            border: 2px solid #ddd !important;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Checkbox styling */
        input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        label {
            display: flex;
            align-items: center;
            margin: 10px 0;
            font-size: 15px;
            color: #444;
        }

        /* Submit button styling */
        button[type="submit"] {
            background-color: #365486;
            color: white;
            padding: 15px 30px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 20px;
            font-weight: 600;
        }

        button[type="submit"]:hover {
            background-color: #2a4268;
        }

        /* Drag and drop zone enhancement */
        .drag-drop-zone {
            background-color: #fff;
            border: 2px dashed #365486;
            padding: 30px;
        }


        /* Title styling */
        h1 {
            color: #365486;
            font-size: 28px;
            font-weight: 600;
            text-align: center;
        }

        .drag-drop-zone.dragover {
            background-color: #f0f7ff;
            border-color: #007bff;
        }

        /* Title styling */

        h3 {
            color: #365486;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 15px;
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

        /* Learning Styles Container */
        .learning-styles-container {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #ddd;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Learning Styles Grid */
        .learning-styles-grid {
            display: grid;
            gap: 8px;
        }

        /* Checkbox Labels */
        .checkbox-label {
            display: flex;
            align-items: center;
            margin: 0;
        }

        .checkbox-label input[type="checkbox"] {
            margin-right: 8px;
        }

        .checkbox-label span {
            font-size: 15px;
            color: #444;
        }

        /* Section Title */
        .section-title {
            color: #365486;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 8px;
        }
    </style>

    <div id="successModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Success!</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <i class="fas fa-check-circle success-icon"></i>
                <p>Your post has been created successfully!</p>
            </div>
            <div class="modal-footer">
                <button onclick="redirectToMyPosts()" class="modal-btn explore-btn">View My Posts</button>
                <button onclick="createNewPost()" class="modal-btn create-btn">Create Another Post</button>
            </div>
        </div>
    </div>

    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 0;
            border-radius: 12px;
            width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s;
        }

        .modal-header {
            padding: 20px;
            background-color: #365486;
            color: white;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .close {
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .close:hover {
            opacity: 0.8;
        }

        .modal-body {
            padding: 30px 20px;
            text-align: center;
        }

        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 15px;
        }

        .modal-body p {
            font-size: 1.1rem;
            color: #444;
            margin: 0;
        }

        .modal-footer {
            padding: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            border-top: 1px solid #eee;
        }

        .modal-btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .cancel-btn {
            background-color: #f8f9fa;
            color: #365486;
            border: 1px solid #365486;
        }

        .cancel-btn:hover {
            background-color: #e9ecef;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Loading spinner styles */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #365486;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        function showSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.style.display = 'block';
        }

        // Close modal when clicking the X
        document.querySelector('.close').onclick = function() {
            document.getElementById('successModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('successModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function redirectToMyPosts() {
            window.location.href = 'my-posts.php';
        }

        function createNewPost() {
            document.getElementById('successModal').style.display = 'none';
            // Reset form
            document.querySelector('form').reset();
            document.getElementById('file-preview').innerHTML = '';
            document.getElementById('drag-drop-zone').style.display = 'flex';
        }

        function confirmLogout() {
            // Show loading state
            const logoutBtn = document.querySelector('.logout-btn');
            logoutBtn.innerHTML = '<div class="loading-spinner"></div>Logging out...';
            logoutBtn.disabled = true;
            document.querySelector('.cancel-btn').disabled = true;

            // Redirect to logout page after a brief delay
            setTimeout(() => {
                window.location.href = 'auth/logout.php';
            }, 1000);
        }

        function cancelLogout() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function showInfo(event) {
            const popup = document.getElementById('infoPopup');
            const info = event.target.getAttribute('data-info');

            popup.querySelector('.popup-content').textContent = info;
            popup.style.display = 'block';

            // Position the popup
            const iconRect = event.target.getBoundingClientRect();
            popup.style.top = `${iconRect.top + window.scrollY - 5}px`;
            popup.style.left = `${iconRect.left + window.scrollX - 290}px`; // Position to the left
        }

        function hideInfo() {
            document.getElementById('infoPopup').style.display = 'none';
        }
    </script>

    <!-- Info Popup -->
    <div id="infoPopup" class="info-popup" style="display: none;">
        <div class="popup-content"></div>
    </div>

    <style>
        .info-popup {
            position: absolute;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            padding: 12px 16px;
            max-width: 280px;
            z-index: 1000;
            font-size: 13px;
            color: #5f6368;
            line-height: 1.5;
            border: 1px solid #dadce0;
        }

        .info-icon {
            font-size: 16px;
            transition: opacity 0.2s;
        }

        .info-icon:hover {
            opacity: 0.8;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border: 2px solid #5f6368;
            border-radius: 2px;
            cursor: pointer;
        }

        input[type="checkbox"]:checked {
            background-color: #1a73e8;
            border-color: #1a73e8;
        }
    </style>

    <!-- Add this right after your form opening tag -->
    <div id="error-messages" style="color: #dc3545; margin-bottom: 15px; display: none;"></div>

    <!-- Add this new validation modal HTML after your success modal -->
    <div id="validationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #dc3545;">
                <h2>Error</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <i class="fas fa-exclamation-circle" style="color: #dc3545; font-size: 48px; margin-bottom: 15px;"></i>
                <div id="validation-messages" style="color: #dc3545;"></div>
            </div>
            <div class="modal-footer">
                <button onclick="closeValidationModal()" class="modal-btn" style="background-color: #6c757d; color: white;">Close</button>
            </div>
        </div>
    </div>

    <style>
        /* Add these styles to your existing modal styles */
        #validation-messages div {
            margin: 5px 0;
            font-size: 1.1rem;
            color: #dc3545;
        }

        .modal-header.validation {
            background-color: #dc3545;
        }

        .fa-exclamation-circle {
            color: #dc3545;
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>



    <style>
        .file-size-info {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .file-size-info i {
            font-size: 16px;
        }

        /* Premium user indicator */
        .premium-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background-color: #fff3dc;
            color: #b38600;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .premium-badge i {
            color: #FFD700;
        }
    </style>

</body>
</head>

</html>