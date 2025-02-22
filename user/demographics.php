<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
            background-color: #f7f7f7;
            color: #4A4947;
            line-height: 1.6;
            padding-top: 80px;
        }
    </style>
    
    <!-- Navigation Bar -->
    <?php
    session_start();
    require_once 'db_conn.php';
    
    // Include navbar
    include 'components/layout/guest/navbar.php';

    // Fetch demographics posts from database
    $query = "SELECT dp.*, u.username 
              FROM demographics_posts dp 
              LEFT JOIN users u ON dp.user_id = u.id 
              WHERE dp.status = 'published' 
              ORDER BY dp.created_at DESC";
          
    $result = mysqli_query($conn, $query);
    $posts = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $posts[] = $row;
        }
        mysqli_free_result($result);
    }
    ?>

<!-- Geography Section -->
<section class="geography-hero">
    <div class="geography-content">
        <br><br><br><br><br><br><br><br><br>
        <h1 class="geography-title">The Role of Demographics in Culture</h1>
        <p class="geography-description">Demographics play a key role in shaping cultural, social, and economic structures. Understanding population trends, migration, and age distribution helps us understand the evolution of cultures across the world.</p>
        <br>
        <div class="geography-image">
            <img src="https://i.pinimg.com/736x/41/72/e7/4172e7a64cbec1b71766bb97025cc534.jpg" alt="Demographics and Culture" />
        </div>
    </div>
</section>

<section class="journals">
    <div class="container">
        <br><br><br><br><br>
        <h2>Demographics Journals</h2>
        <p>Demographics studies the structure of populations, including factors like age, gender, race, migration, and population density. Dive into journals that explore how these demographic factors influence culture and society.</p>

        <!-- Admin Create Button -->
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
            <div class="cta-section">
                <button onclick="openModal()" class="cta-button">Create New Post</button>
            </div>

            <!-- Create Post Modal -->
            <div id="createPostModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Create Demographics Post</h2>
                        <button class="close-btn" onclick="closeModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="createPostForm" method="POST" enctype="multipart/form-data">
                        <div class="form-container">
                            <div class="form-section">
                                <div class="input-group">
                                    <label for="title">Title</label>
                                    <input 
                                        type="text" 
                                        id="title" 
                                        name="title" 
                                        placeholder="Enter an engaging title for your post"
                                        required
                                    >
                                </div>

                                <div class="input-group">
                                    <label for="category">Category</label>
                                    <div class="select-wrapper">
                                        <select id="category" name="category" required>
                                            <option value="" disabled selected>Select a category</option>
                                            <option value="population">Population Growth</option>
                                            <option value="migration">Migration Patterns</option>
                                            <option value="age">Age Distribution</option>
                                            <option value="urbanization">Urbanization</option>
                                            <option value="gender">Gender Demographics</option>
                                            <option value="ethnic">Ethnic and Racial Demographics</option>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <label for="content">Content</label>
                                    <textarea 
                                        id="content" 
                                        name="content" 
                                        placeholder="Write your full post content here"
                                        required
                                    ></textarea>
                                </div>

                                <div class="input-group">
                                    <label for="tags">Tags</label>
                                    <input 
                                        type="text" 
                                        id="tags" 
                                        name="tags"
                                        placeholder="Enter tags separated by commas"
                                    >
                                </div>

                                <div class="input-group">
                                    <label>Featured Image</label>
                                    <div class="drop-zone">
                                        <div class="drop-zone__prompt">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>Drag & drop your file here</p>
                                            <span>or</span>
                                            <button type="button" class="browse-btn">Browse Files</button>
                                        </div>
                                        <input type="file" name="image" class="drop-zone__input" accept="image/*">
                                        <div class="drop-zone__thumb" style="display: none;"></div>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <label for="status">Status</label>
                                    <div class="select-wrapper">
                                        <select id="status" name="status" required>
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                            <option value="archived">Archived</option>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Create Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search journals...">
            <button onclick="searchPosts()">Search</button>
        </div>

        <div class="journal-grid">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="journal-card">
                        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                            <button onclick="deletePost(<?php echo $post['id']; ?>)" class="delete-btn-card">✕ Delete</button>
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($post['image_url'] ?: 'assets/default-post-image.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <div class="journal-card-content">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($post['content'], 0, 150)) . '...'; ?></p>
                            <div class="post-meta">
                                <span class="category"><?php echo htmlspecialchars($post['category']); ?></span>
                                <span class="views"><?php echo number_format($post['views']); ?> views</span>
                            </div>
                            <button onclick="openPostModal(<?php echo $post['id']; ?>)" class="read-more">Read More</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Your existing placeholder cards as fallback -->
                <!-- Journal Card 1 -->
                <div class="journal-card">
                    <img src="population.jpg" alt="Population Growth">
                    <div class="journal-card-content">
                        <h3>Population Growth</h3>
                        <p>Explore how rapid population growth impacts urbanization, resources, and cultural evolution.</p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>

                <!-- Journal Card 2 -->
                <div class="journal-card">
                    <img src="migration.jpg" alt="Migration Patterns">
                    <div class="journal-card-content">
                        <h3>Migration Patterns</h3>
                        <p>Understand how migration, both voluntary and forced, shapes demographic changes and cultural exchange.</p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>

                <!-- Journal Card 3 -->
                <div class="journal-card">
                    <img src="age.jpg" alt="Age Distribution">
                    <div class="journal-card-content">
                        <h3>Age Distribution</h3>
                        <p>Learn about the effect of age distribution on economic and social structures, from the elderly to the youth.</p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>

                <!-- Journal Card 4 -->
                <div class="journal-card">
                    <img src="urbanization.jpg" alt="Urbanization and Demographics">
                    <div class="journal-card-content">
                        <h3>Urbanization and Demographics</h3>
                        <p>Explore how urbanization influences demographic patterns, such as population density and migration.</p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>

                <!-- Journal Card 5 -->
                <div class="journal-card">
                    <img src="gender.jpg" alt="Gender Demographics">
                    <div class="journal-card-content">
                        <h3>Gender Demographics</h3>
                        <p>Examine how gender distribution affects economic participation, political influence, and social roles.</p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>

                <!-- Journal Card 6 -->
                <div class="journal-card">
                    <img src="urban.jpg" alt="Rural vs Urban Populations">
                    <div class="journal-card-content">
                        <h3>Rural vs Urban Populations</h3>
                        <p>Study the demographic differences between rural and urban populations and their cultural implications.</p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>

                <!-- Journal Card 7 -->
                <div class="journal-card">
                    <img src="ethnic.jpg" alt="Ethnic and Racial Demographics">
                    <div class="journal-card-content">
                        <h3>Ethnic and Racial Demographics</h3>
                        <p>Explore the relationship between ethnic and racial diversity and cultural practices in different societies.</p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>

                <!-- Journal Card 8 -->
                <div class="journal-card">
                    <img src="aging.jpg" alt="Population Aging">
                    <div class="journal-card-content">
                        <h3>Population Aging</h3>
                        <p>Investigate the effects of an aging population on social services, culture, and labor markets.</p>
                        <a href="#" class="read-more">Read More</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Add this modal structure after your existing journal-grid div -->
<div id="postModal" class="modal">
    <div class="modal-content post-modal">
        <span class="close" onclick="closePostModal()">&times;</span>
        <div id="postContent"></div>
    </div>
</div>

<style>
 /* Geography Hero Section */
.geography-hero {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 60vh;
    background-image: url('https://i.pinimg.com/736x/63/e7/08/63e708d10ecb834af8a193a4ec979f03.jpg'); 
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
    font-size: 2.5rem;
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
    position: relative;
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

.journal-card h3 {
    font-size: 1.1rem;
    color: #333;
    margin: 0 0 10px;
    font-weight: bold;
}

.journal-card p {
    font-size: 0.9rem;
    color: #666;
    margin: 0 0 15px;
}

.journal-card .read-more {
    text-decoration: none;
    color: #ffffff;
    background-color: #007bff;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.875rem;
    text-align: center;
    display: inline-block;
    transition: background-color 0.3s;
}

.journal-card .read-more:hover {
    background-color: #0056b3;
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

.post-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 0.8rem;
    color: #666;
}

.category {
    background-color: #f0f0f0;
    padding: 2px 8px;
    border-radius: 12px;
}

.views {
    color: #888;
}

/* Admin Create Button */
.cta-section {
    margin: 20px 0;
    text-align: center;
}

.cta-button {
    display: inline-block;
    padding: 12px 24px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 500;
    transition: background-color 0.3s;
}

.cta-button:hover {
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
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    background: #fff;
    margin: 2% auto;
    width: 98%;
    max-width: 1400px;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    max-height: 95vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 32px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    color: #111827;
}

.close-btn {
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
    color: #6B7280;
    font-size: 20px;
    transition: all 0.2s;
}

.close-btn:hover {
    color: #111827;
}

.form-container {
    padding: 24px 32px;
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.input-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.input-group input,
.input-group textarea,
.input-group select {
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
    background: #fff;
}

.input-group textarea {
    min-height: 150px;
    resize: vertical;
}

.select-wrapper {
    position: relative;
}

.select-wrapper select {
    width: 100%;
    appearance: none;
    padding-right: 40px;
}

.select-wrapper i {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6B7280;
    pointer-events: none;
}

.form-actions {
    padding: 24px 32px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-secondary {
    background: #fff;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-secondary:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.btn-primary {
    background: #2563eb;
    border: none;
    color: #fff;
}

.btn-primary:hover {
    background: #1d4ed8;
}

/* Drop Zone Styles */
.drop-zone {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    transition: all 0.2s;
    background: #f9fafb;
}

.drop-zone:hover {
    border-color: #2563eb;
    background: #f3f4f6;
}

.drop-zone__prompt {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.drop-zone__prompt i {
    font-size: 32px;
    color: #2563eb;
}

.browse-btn {
    background: #fff;
    border: 1px solid #d1d5db;
    padding: 8px 16px;
    border-radius: 6px;
    color: #374151;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.browse-btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

@media (max-width: 768px) {
    .modal-content {
        margin: 0;
        width: 100%;
        height: 100%;
        max-height: 100vh;
        border-radius: 0;
    }

    .form-container {
        padding: 20px;
    }

    .form-section {
        gap: 16px;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* Post Modal Specific Styles */
.post-modal {
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 30px;
}

.post-header {
    margin-bottom: 30px;
}

.post-title {
    font-size: 2em;
    color: #333;
    margin-bottom: 10px;
    margin-right: 20px;
}

.post-meta {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 20px;
}

.post-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 20px;
}

.post-content {
    font-size: 1.1em;
    color: #444;
    line-height: 1.8;
}

.modal-tags {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.tag {
    background-color: #f8f9fa;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    color: #666;
    margin-right: 5px;
    display: inline-block;
    margin-bottom: 5px;
}

.modal-views {
    color: #666;
    font-size: 0.9em;
    margin-top: 20px;
}

.delete-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background-color 0.2s;
}

.delete-btn:hover {
    background-color: #c82333;
}

.delete-btn-card {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(0, 0, 0, 0.7);
    color: #fff;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.8rem;
    z-index: 2;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    gap: 4px;
}

.delete-btn-card:hover {
    background-color: rgba(0, 0, 0, 0.9);
}
</style>

<script>
function searchPosts() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.journal-card');
    
    cards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const description = card.querySelector('p').textContent.toLowerCase();
        const category = card.querySelector('.category')?.textContent.toLowerCase() || '';
        
        if (title.includes(searchInput) || 
            description.includes(searchInput) || 
            category.includes(searchInput)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function openModal() {
    document.getElementById('createPostModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('createPostModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('createPostModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Handle form submission
document.getElementById('createPostForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Basic form validation
    const title = this.querySelector('#title').value.trim();
    const content = this.querySelector('#content').value.trim();
    
    if (!title || !content) {
        alert('Please fill in all required fields');
        return;
    }

    const formData = new FormData(this);
    
    // Show loading state
    const submitBtn = this.querySelector('.btn-primary');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
    submitBtn.disabled = true;
    
    fetch('create_demographics_post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Post created successfully!');
            closeModal();
            location.reload(); // Reload page to show new post
        } else {
            alert('Error creating post: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating post. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
});

function openPostModal(postId) {
    const modal = document.getElementById('postModal');
    const contentDiv = document.getElementById('postContent');
    
    // Show loading state
    contentDiv.innerHTML = '<div style="text-align: center;">Loading...</div>';
    modal.style.display = 'block';

    // Increment view and fetch post content
    fetch(`increment_view.php?id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return fetch(`get_demographics_post.php?id=${postId}`);
            }
        })
        .then(response => response.json())
        .then(post => {
            if (post) {
                contentDiv.innerHTML = `
                    <article class="post-header">
                        <h1 class="post-title">${escapeHtml(post.title)}</h1>
                        
                        <div class="post-meta">
                            <span>By ${escapeHtml(post.username)}</span> • 
                            <span>${formatDate(post.created_at)}</span> • 
                            <span class="category-tag">${escapeHtml(post.category)}</span>
                        </div>

                        ${post.image_url ? `
                            <img src="${escapeHtml(post.image_url)}" 
                                 alt="${escapeHtml(post.title)}" 
                                 class="post-image">
                        ` : ''}
                    </article>

                    <div class="post-content">
                        ${nl2br(escapeHtml(post.content))}
                    </div>

                    ${post.tags ? `
                        <div class="modal-tags">
                            ${post.tags.split(',').map(tag => 
                                `<span class="tag">${escapeHtml(tag.trim())}</span>`
                            ).join('')}
                        </div>
                    ` : ''}

                    <div class="modal-views">
                        ${numberFormat(post.views)} views
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div style="text-align: center;">Error loading post</div>';
        });
}

function closePostModal() {
    document.getElementById('postModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('postModal');
    if (event.target == modal) {
        closePostModal();
    }
}

// Helper functions
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function nl2br(str) {
    return str.replace(/\n/g, '<br>');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

function numberFormat(number) {
    return new Intl.NumberFormat().format(number);
}

// Add this new function for post deletion
function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        const deleteBtn = event.target;
        const originalText = deleteBtn.textContent;
        deleteBtn.textContent = 'Deleting...';
        deleteBtn.disabled = true;

        fetch('delete_demographics_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Post deleted successfully!');
                closePostModal();
                location.reload(); // Reload page to update the posts list
            } else {
                alert('Error deleting post: ' + (data.message || 'Unknown error'));
                deleteBtn.textContent = originalText;
                deleteBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting post. Please try again.');
            deleteBtn.textContent = originalText;
            deleteBtn.disabled = false;
        });
    }
}

// Add this code for handling file uploads
document.querySelectorAll('.drop-zone__input').forEach(inputElement => {
    const dropZoneElement = inputElement.closest('.drop-zone');

    dropZoneElement.addEventListener('click', e => {
        inputElement.click();
    });

    inputElement.addEventListener('change', e => {
        if (inputElement.files.length) {
            updateThumbnail(dropZoneElement, inputElement.files[0]);
        }
    });

    dropZoneElement.addEventListener('dragover', e => {
        e.preventDefault();
        dropZoneElement.classList.add('drop-zone--over');
    });

    ['dragleave', 'dragend'].forEach(type => {
        dropZoneElement.addEventListener(type, e => {
            dropZoneElement.classList.remove('drop-zone--over');
        });
    });

    dropZoneElement.addEventListener('drop', e => {
        e.preventDefault();

        if (e.dataTransfer.files.length) {
            inputElement.files = e.dataTransfer.files;
            updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
        }

        dropZoneElement.classList.remove('drop-zone--over');
    });
});

function updateThumbnail(dropZoneElement, file) {
    let thumbnailElement = dropZoneElement.querySelector('.drop-zone__thumb');

    // First time - remove the prompt
    if (!thumbnailElement) {
        thumbnailElement = document.createElement('div');
        thumbnailElement.classList.add('drop-zone__thumb');
        dropZoneElement.appendChild(thumbnailElement);
    }

    thumbnailElement.dataset.label = file.name;

    // Show thumbnail for image files
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();

        reader.readAsDataURL(file);
        reader.onload = () => {
            thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
            thumbnailElement.style.display = 'block';
            dropZoneElement.querySelector('.drop-zone__prompt').style.display = 'none';
        };
    } else {
        thumbnailElement.style.backgroundImage = null;
    }
}

// Add this CSS for the file upload thumbnail
const newStyle = document.createElement('style');
newStyle.textContent = `
    .drop-zone--over {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .drop-zone__thumb {
        width: 100%;
        height: 200px;
        border-radius: 10px;
        overflow: hidden;
        background-color: #cccccc;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .drop-zone__thumb::after {
        content: attr(data-label);
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 5px 0;
        color: #ffffff;
        background: rgba(0, 0, 0, 0.75);
        font-size: 14px;
        text-align: center;
    }
`;
document.head.appendChild(newStyle);
</script>

<!-- Sidebar -->
<?php include 'components/layout/guest/sidebar.php'; ?>
<?php include 'components/widgets/chat.php'; ?>

</body>
</head>
</html>