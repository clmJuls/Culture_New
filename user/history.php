<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
    /* General */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            color: #4A4947;
            line-height: 1.6;
            padding-top: 80px;
        }
    </style>
</head>
<body>
    <?php
    // Start the session at the beginning
    session_start();
    
    // Include database connection
    require_once 'db_conn.php';
    
    // Include navbar
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
        include 'components/layout/admin/navbar.php';
    } else {
        include 'components/layout/guest/navbar.php';
    }
    ?>

<!-- Geography Section -->
<section class="geography-hero">
    <div class="geography-content">
        <br><br><br><br><br><br><br><br><br>
        <h1 class="geography-title">The Role of History in Culture</h1>
        <p class="geography-description">History shapes cultural practices, beliefs, and the social structures that influence our world today. From ancient civilizations to modern history, explore the impact of past events on the present.</p>
        <br>
        <div class="geography-image">
            <img src="https://i.pinimg.com/736x/f7/fb/96/f7fb9635d8975aa88d0f18124f862e55.jpg" alt="History and Culture" />
        </div>
    </div>
</section>

<section class="journals">
    <div class="container">
        <br><br><br><br>
        <h2>History Journals</h2>
        <p>History explores the past and its influence on the present. Dive into journals that focus on how historical events, movements, and people have shaped the cultures we live in today.</p>

        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
            <button class="create-post-btn" onclick="openModal()">Create New Post</button>
        <?php endif; ?>
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search journals...">
            <button onclick="searchJournals()">Search</button>
        </div>

        <!-- Add Create Post Button for Admin -->
       

        <!-- Include the modal -->
        <?php include 'history/components/modals/create-post-modal.php'; ?>

        <div class="journal-grid">
            <?php
            // Fetch posts from history_posts table
            $sql = "SELECT hp.*, u.username 
                    FROM history_posts hp 
                    LEFT JOIN users u ON hp.user_id = u.id 
                    ORDER BY hp.created_at DESC";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo '<div class="journal-card">
                        <div class="card-image-container">
                            <img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['title']) . '">';
                
                // Show delete button only for admin
                if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
                    echo '<button class="delete-btn" onclick="deletePost(' . $row['id'] . ')">
                            <i class="fas fa-times"></i> Delete
                          </button>';
                }
                
                echo '</div>
                        <div class="journal-card-content">
                            <h3>' . htmlspecialchars($row['title']) . '</h3>
                            <p>' . htmlspecialchars($row['description']) . '</p>
                            <a href="#" class="read-more" onclick="viewPost(' . $row['id'] . ')">Read More</a>
                        </div>
                      </div>';
            }
            ?>
        </div>
    </div>
</section>

<style>
 /* Geography Hero Section */
.geography-hero {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 60vh;
    background-image: url('https://i.pinimg.com/736x/17/97/45/179745a3ef2508595c382f04fee5451a.jpg');
    background-size: cover;
    background-position: center;
    color: white;
    text-align: center;
    position: relative;
}

.geography-hero::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); 
}

.geography-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    padding: 20px;
}

.geography-title {
    font-size: 2.8rem;
    margin-bottom: 20px;
    font-weight: bold;
}

.geography-description p {
    font-size: 1.2rem;
    margin-bottom: 30px;
}

.geography-image img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin-top: 20px;
}

/* Search Bar Styles */
.search-bar {
    width: 50%;
    margin: 20px auto;
    text-align: center;
}

.search-bar input {
    width: 80%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-right: 10px;
}

.search-bar button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #00196d;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.search-bar button:hover {
    background-color: #187fd3;
}


/* Journals Section */
.journals {
    background-color: #f4f4f4;
    padding: 50px 20px;
    font-family: Arial, sans-serif;
}

.journals .container {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
}

.journals h2 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 10px;
}

.journals p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 30px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.journal-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 4 cards per row */
    gap: 24px;
    padding: 24px;
    max-width: 1200px;
    margin: 0 auto;
}

.journal-card {
    width: 100%;
    height: 320px; /* Fixed height */
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
    display: flex;
    flex-direction: column;
}

.journal-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.card-image-container {
    position: relative;
    width: 100%;
    height: 160px; /* Fixed height for images */
}

.card-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.journal-card-content {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.journal-card h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    /* Limit title to 2 lines */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.journal-card p {
    font-size: 14px;
    color: #666;
    margin-bottom: 16px;
    flex: 1;
    /* Limit description to 3 lines */
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.journal-card .read-more {
    text-decoration: none;
    color: #ffffff;
    background-color: #007bff;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    transition: background-color 0.2s;
    margin-top: auto; /* Push button to bottom */
}

.journal-card .read-more:hover {
    background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .journal-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 900px) {
    .journal-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .journal-grid {
        grid-template-columns: 1fr;
    }
}

/* Call to Action Section */
.cta-section {
    text-align: center;
    margin: 40px 0;
}

.cta-button {
    font-size: 1.1rem;
    padding: 15px 30px;
    background-color: #022597;
    color: white;
    text-decoration: none;
    border-radius: 30px;
    margin: 10px;
    transition: background-color 0.3s ease;
}

.cta-button:hover {
    background-color: #0052b1;
}

.create-post-section {
    text-align: right;
    margin: 20px auto;
    max-width: 1200px;
    padding: 0 20px;
}

.create-btn {
    display: inline-block;
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s;
}

.create-btn:hover {
    background-color: #0056b3;
}

.create-post-btn {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 20px 0;
    font-size: 16px;
}

.create-post-btn:hover {
    background-color: #0056b3;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 5px;
    border-radius: 8px;
    width: 70%;
    max-width: 1200px;
}

.close {
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.drop-zone {
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    margin: 10px 0;
}

/* Add these styles */
.card-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
}

.delete-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: background-color 0.2s;
    z-index: 2;
}

.delete-btn:hover {
    background-color: rgba(0, 0, 0, 0.9);
}
</style>

<!-- Sidebar -->
<?php include 'components/layout/guest/sidebar.php'; ?>
<?php include 'components/widgets/chat.php'; ?>

<!-- Add necessary JavaScript -->
<script>
function openModal() {
    document.getElementById('createPostModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('createPostModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById('createPostModal')) {
        closeModal();
    }
}

// Handle form submission
document.getElementById('createPostForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    
    fetch('process_history_post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Post created successfully!');
            location.reload();
        } else {
            alert('Error creating post: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the post');
    });
});

// Functions first
function viewPost(postId) {
    fetch('get_history_post.php?id=' + postId)
        .then(response => response.json())
        .then(data => {
            // Populate modal with post data
            document.getElementById('modalImage').src = data.image_url;
            document.getElementById('modalTitle').textContent = data.title;
            document.getElementById('modalCategory').textContent = data.category;
            document.getElementById('modalDate').textContent = data.created_at;
            document.getElementById('modalDescription').textContent = data.description;
            document.getElementById('modalContent').innerHTML = data.content;
            
            // Show modal
            const modal = document.getElementById('postDetailModal');
            modal.style.display = 'block';
            setTimeout(() => {
                modal.classList.add('active');
            }, 10);
            
            // Prevent body scrolling when modal is open
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load post details');
        });
}

function closePostModal() {
    const modal = document.getElementById('postDetailModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

// Wait for DOM to be fully loaded before adding event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners for modal close button
    const closeButton = document.querySelector('.close-post-modal');
    if (closeButton) {
        closeButton.addEventListener('click', function(e) {
            e.preventDefault();
            closePostModal();
        });
    }

    // Event listener for clicking outside modal
    const modal = document.getElementById('postDetailModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePostModal();
            }
        });
    }

    // Event listener for modal content (prevent propagation)
    const modalContent = document.querySelector('.post-modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});

// Global event listener for ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePostModal();
    }
});

// Add this function to your existing JavaScript
function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        fetch('delete_history_post.php?id=' + postId, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the page or remove the card
                location.reload();
            } else {
                alert('Error deleting post: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the post');
        });
    }
}
</script>

<!-- Post Detail Modal -->
<div id="postDetailModal" class="post-modal">
    <div class="post-modal-content">
        <span class="close-post-modal">&times;</span>
        <div class="post-detail-content">
            <img id="modalImage" src="" alt="Post Image" class="post-detail-image">
            <div class="post-detail-text">
                <h2 id="modalTitle"></h2>
                <div class="post-metadata">
                    <span id="modalCategory"></span>
                    <span id="modalDate"></span>
                </div>
                <p id="modalDescription"></p>
                <div id="modalContent" class="full-content"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Post Detail Modal Styles */
.post-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    overflow-y: auto;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.post-modal.active {
    opacity: 1;
    visibility: visible;
}

.post-modal-content {
    background-color: #fff;
    margin: 40px auto;
    padding: 0;
    width: 90%;
    max-width: 900px;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
    transform: translateY(20px);
    transition: transform 0.3s ease;
}

.post-modal.active .post-modal-content {
    transform: translateY(0);
}

.close-post-modal {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 28px;
    font-weight: bold;
    color: white;
    cursor: pointer;
    z-index: 10;
    text-shadow: 0 0 10px rgba(0,0,0,0.5);
    transition: transform 0.2s ease, opacity 0.2s ease;
}

.close-post-modal:hover {
    transform: rotate(90deg);
    opacity: 0.8;
}

.post-detail-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.post-detail-text {
    padding: 32px;
}

.post-detail-text h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 16px;
}

.post-metadata {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    color: #666;
    font-size: 14px;
}

.post-metadata span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.post-detail-text p {
    font-size: 16px;
    line-height: 1.8;
    color: #444;
    margin-bottom: 24px;
}

.full-content {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
}

/* Responsive Design */
@media (max-width: 768px) {
    .post-modal-content {
        margin: 20px;
        width: calc(100% - 40px);
    }

    .post-detail-image {
        height: 300px;
    }

    .post-detail-text {
        padding: 20px;
    }

    .post-detail-text h2 {
        font-size: 24px;
    }
}
</style>

<!-- Update the create post modal HTML -->
<div id="createPostModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New History Post</h2>
            <span class="close">&times;</span>
        </div>
        <form id="createPostForm" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required 
                           placeholder="Enter post title">
                </div>

                <div class="form-group full-width">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="" disabled selected>Select a category</option>
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

                <div class="form-group full-width">
                    <label for="description">Short Description</label>
                    <textarea id="description" name="description" required 
                              placeholder="Enter a brief description"></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="content">Full Content</label>
                    <textarea id="content" name="content" required 
                              placeholder="Enter the full content of your post"></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Featured Image</label>
                    <div class="drop-zone">
                        <div class="drop-zone__prompt">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & drop your file here</p>
                            <span>or</span>
                            <p>Click to select a file</p>
                        </div>
                        <input type="file" name="image" class="drop-zone__input" accept="image/*" required>
                        <div class="drop-zone__thumb" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Post</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Updated Drop Zone Styles */
.drop-zone {
    border: 2px dashed #6B7280;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background-color: #ffffff;
}

.drop-zone:hover {
    border-color: #007bff;
    background-color: #f8fafc;
}

.drop-zone--over {
    border-style: solid;
    background-color: #f0f9ff;
}

.drop-zone__prompt {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: #6B7280;
}

.drop-zone__prompt i {
    font-size: 24px;
    color: #6B7280;
    margin-bottom: 8px;
}

.drop-zone__prompt p {
    margin: 0;
    font-size: 15px;
    color: #6B7280;
}

.drop-zone__prompt span {
    font-size: 14px;
    color: #9CA3AF;
}

.drop-zone__input {
    display: none;
}

.drop-zone__thumb {
    width: 100%;
    height: 200px;
    border-radius: 8px;
    overflow: hidden;
    background-size: cover;
    background-position: center;
    position: relative;
}

/* Updated Modal Header Styles */
.modal-header {
    background-color: #ffffff;
    padding: 24px 30px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
    font-size: 24px;
    color: #111827;
    font-weight: 600;
}

/* Updated Form Action Buttons */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-secondary {
    background-color: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background-color: #e5e7eb;
}

.btn-submit {
    background-color: #007bff;
    color: white;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
}

.btn-submit:hover {
    background-color: #0056b3;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 123, 255, 0.2);
}

.btn-submit:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
}

.btn-submit i {
    font-size: 14px;
}

/* Form Input Styles */
.form-group input,
.form-group select,
.form-group textarea {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
    background-color: #fff;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 4px;
}

/* Modal Content Padding */
#createPostForm {
    padding: 24px 30px;
}

.form-grid {
    display: grid;
    gap: 24px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-content {
        margin: 16px;
        width: calc(100% - 32px);
    }

    #createPostForm {
        padding: 20px;
    }

    .drop-zone {
        padding: 30px 15px;
    }

    .form-actions {
        flex-direction: column-reverse;
        gap: 10px;
    }

    .btn {
        width: 100%;
        padding: 10px 20px;
    }
}
</style>

<script>
// Add this to your existing JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Handle drag and drop functionality
    const dropZone = document.querySelector('.drop-zone');
    const input = dropZone.querySelector('.drop-zone__input');
    const thumb = dropZone.querySelector('.drop-zone__thumb');
    const prompt = dropZone.querySelector('.drop-zone__prompt');

    dropZone.addEventListener('click', () => input.click());

    input.addEventListener('change', function() {
        if (this.files[0]) {
            updateThumbnail(this.files[0]);
        }
    });

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('drop-zone--over');
    });

    ['dragleave', 'dragend'].forEach(type => {
        dropZone.addEventListener(type, () => {
            dropZone.classList.remove('drop-zone--over');
        });
    });

    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            updateThumbnail(e.dataTransfer.files[0]);
        }
        
        dropZone.classList.remove('drop-zone--over');
    });

    function updateThumbnail(file) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = () => {
                thumb.style.backgroundImage = `url('${reader.result}')`;
                thumb.style.display = 'block';
                prompt.style.display = 'none';
            };
            
            reader.readAsDataURL(file);
        } else {
            thumb.style.display = 'none';
            prompt.style.display = 'flex';
            alert('Please upload an image file');
        }
    }
});
</script>

</body>
</html>