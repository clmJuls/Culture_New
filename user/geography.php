<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="geography/assets/css/journal-card.css">
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
        <h1 class="geography-title">The Role of Geography in Culture</h1>
        <p class="geography-description">Geography plays a critical role in shaping cultural practices, from climate and natural resources to landforms and regional boundaries.</p>
        <br>
        <div class="geography-image">
            <img src="https://i.pinimg.com/736x/19/cb/56/19cb560da70efd3d723bc93539de8cb7.jpg" alt="Geography and Culture" />
        </div>
    </div>
</section>

<section class="journals">
    <div class="container">
        <br><br><br><br>
        <h2>Geography Journals</h2>
        <p>Geography explores the Earth's landscapes, environments, and the relationships between people and their surroundings. Dive into journals that highlight the influence of physical and human geography on our world.</p>

        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
        <!-- Add New Post Button (Admin only) -->
        <button class="create-post-btn" onclick="openModal()">Create New Post</button>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search journals...">
            <button onclick="searchJournals()">Search</button>
        </div>

        <div class="journal-grid">
            <?php
            // Fetch posts from geography_posts table with user information
            $sql = "SELECT gp.*, u.username 
                    FROM geography_posts gp 
                    LEFT JOIN users u ON gp.user_id = u.id 
                    ORDER BY gp.created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="journal-card">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="journal-card-content">
                            <div class="card-header">
                                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                <h4 class="subtitle"><?php echo htmlspecialchars($row['description']); ?></h4>
                            </div>
                            <div class="card-body">
                                <p class="content-preview">
                                    <?php 
                                    echo htmlspecialchars(substr($row['content'], 0, 150)) . 
                                         (strlen($row['content']) > 150 ? '...' : ''); 
                                    ?>
                                </p>
                            </div>
                            <div class="card-footer">
                                <span class="author">By :  <?php echo $row['username'] ? htmlspecialchars($row['username']) : 'Anonymous'; ?></span>
                                <a href="#" class="read-more" onclick="viewPost(<?php echo $row['id']; ?>)">Read More</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Create Post Modal -->
<div id="createPostModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Create New Post</h2>
        <form id="createPostForm" method="POST" action="process_geography_post.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <div class="drop-zone">
                    <span class="drop-zone__prompt">Drag & drop your file here or click to select</span>
                    <input type="file" name="image" class="drop-zone__input" id="image" accept="image/*" required>
                </div>
            </div>
            <button type="submit" class="submit-btn">Create Post</button>
        </form>
    </div>
</div>

<!-- Add this modal for viewing full posts -->
<div id="viewPostModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeViewModal()">&times;</span>
        <div id="postContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Update the JavaScript -->
<script>
function openModal() {
    document.getElementById('createPostModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('createPostModal').style.display = 'none';
}

function viewPost(postId) {
    // Fetch post details using AJAX
    fetch(`get_post.php?id=${postId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('postContent').innerHTML = `
                <h2>${data.title}</h2>
                <h4 class="subtitle">${data.description}</h4>
                <img src="${data.image_url}" alt="${data.title}" class="full-post-image">
                <div class="post-metadata">
                    <span class="author">By ${data.username}</span>
                    <span class="date">${data.created_at}</span>
                </div>
                <div class="post-content">
                    ${data.content.split('\n').map(paragraph => 
                        paragraph ? `<p>${paragraph}</p>` : ''
                    ).join('')}
                </div>
            `;
            document.getElementById('viewPostModal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function closeViewModal() {
    document.getElementById('viewPostModal').style.display = 'none';
}

// Update window click handler to handle both modals
window.onclick = function(event) {
    if (event.target == document.getElementById('createPostModal')) {
        closeModal();
    }
    if (event.target == document.getElementById('viewPostModal')) {
        closeViewModal();
    }
}

document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
    const dropZoneElement = inputElement.closest(".drop-zone");

    dropZoneElement.addEventListener("click", (e) => {
        inputElement.click();
    });

    inputElement.addEventListener("change", (e) => {
        if (inputElement.files.length) {
            updateThumbnail(dropZoneElement, inputElement.files[0]);
        }
    });

    dropZoneElement.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropZoneElement.classList.add("drop-zone--over");
    });

    ["dragleave", "dragend"].forEach((type) => {
        dropZoneElement.addEventListener(type, (e) => {
            dropZoneElement.classList.remove("drop-zone--over");
        });
    });

    dropZoneElement.addEventListener("drop", (e) => {
        e.preventDefault();

        if (e.dataTransfer.files.length) {
            inputElement.files = e.dataTransfer.files;
            updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
        }

        dropZoneElement.classList.remove("drop-zone--over");
    });
});

function updateThumbnail(dropZoneElement, file) {
    let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");

    // First time - remove the prompt
    if (dropZoneElement.querySelector(".drop-zone__prompt")) {
        dropZoneElement.querySelector(".drop-zone__prompt").remove();
    }

    // First time - there is no thumbnail element, so lets create it
    if (!thumbnailElement) {
        thumbnailElement = document.createElement("div");
        thumbnailElement.classList.add("drop-zone__thumb");
        dropZoneElement.appendChild(thumbnailElement);
    }

    thumbnailElement.dataset.label = file.name;

    // Show thumbnail for image files
    if (file.type.startsWith("image")) {
        thumbnailElement.style.backgroundImage = `url(${URL.createObjectURL(file)})`;
    }
}

function searchJournals() {
    const searchInput = document.getElementById('searchInput');
    const filter = searchInput.value.toLowerCase().trim();
    const cards = document.getElementsByClassName('journal-card');

    if (filter === '') {
        for (let card of cards) {
            card.style.display = "";
        }
        return;
    }

    for (let card of cards) {
        const title = card.querySelector('h3').textContent.toLowerCase();
        if (title.includes(filter)) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }
    }
}

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchJournals();
    }
});

function clearSearch() {
    document.getElementById('searchInput').value = '';
    searchJournals();
}
</script>

<style>
 /* Geography Hero Section */
.geography-hero {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 60vh;
    background-image: url('https://i.pinimg.com/736x/e0/b2/51/e0b2510712ba5f57dde64939e923a837.jpg');
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
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.journal-card {
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
}

.journal-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.journal-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
}

.journal-card-content {
    padding: 15px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-header {
    margin-bottom: 15px;
}

.card-header h3 {
    font-size: 1.2rem;
    color: #333;
    margin-bottom: 8px;
    font-weight: bold;
}

.subtitle {
    font-size: 0.9rem;
    color: #666;
    font-weight: normal;
    margin-bottom: 12px;
}

.content-preview {
    font-size: 0.9rem;
    color: #444;
    line-height: 1.5;
    margin-bottom: 15px;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.author {
    font-size: 0.9rem;
    color: #666;
}

/* Full post view styles */
.full-post-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin: 20px 0;
}

.post-metadata {
    margin: 15px 0;
    color: #666;
    font-size: 0.9rem;
}

.post-content {
    line-height: 1.8;
    color: #333;
    margin-top: 20px;
}

/* Update modal styles for post viewing */
.modal-content {
    max-height: 90vh;
    overflow-y: auto;
}

.date {
    margin-left: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .journal-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
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

/* Add these styles to your existing CSS */
.create-post-btn {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 20px;
}

.create-post-btn:hover {
    background-color: #0056b3;
}

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

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group textarea {
    height: 100px;
}

.submit-btn {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.submit-btn:hover {
    background-color: #0056b3;
}
</style>

<!-- Sidebar -->
<?php include 'components/layout/guest/sidebar.php'; ?>
<?php include 'components/widgets/chat.php'; ?>

</body>
</html>