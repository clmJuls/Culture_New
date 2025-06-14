<?php
session_start();
require_once 'db_conn.php';

// Include navbar based on user role
if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
    include 'components/layout/admin/navbar.php';
} else {
    include 'components/layout/guest/navbar.php';
}

// Fetch culture posts from database
$query = "SELECT cp.*, u.username 
          FROM culture_posts cp 
          LEFT JOIN users u ON cp.user_id = u.id 
          ORDER BY cp.created_at DESC";
      
$result = mysqli_query($conn, $query);
$posts = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
    mysqli_free_result($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
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

        .card-image-container {
            position: relative;
            width: 100%;
            height: 200px;
        }

        .card-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .delete-btn:hover {
            background-color: #ff4444;
            color: white;
        }

        .delete-btn i {
            font-size: 16px;
        }
    </style>
    
    <!-- Geography Section -->
    <section class="geography-hero">
        <div class="geography-content">
            <br><br><br><br><br><br><br><br><br>
            <h1 class="geography-title">The Role of Culture in Society</h1>
            <p class="geography-description">Culture plays a pivotal role in shaping societies. From language, traditions, art, and cuisine to the values and beliefs of a community, culture helps define the identity of a group of people.</p>
            <br>
            <div class="geography-image">
                <img src="https://i.pinimg.com/736x/c6/08/54/c60854651e7d7062fde8e1393741cdfa.jpg" alt="Culture and Society" />
            </div>
        </div>
    </section>

    <section class="journals">
        <div class="container">
            <br><br><br><br>
            <h2>Culture Journals</h2>
            <p>Culture shapes the way we live, interact, and perceive the world. Delve into journals exploring the diverse elements of culture, including art, tradition, language, and the practices that define societies across the globe.</p>

            <!-- Admin Create Button -->
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                <div class="cta-section">
                    <button onclick="openModal()" class="cta-button">Create New Post</button>
                </div>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search journals...">
                <button id="searchButton" class="search-button">Search</button>
            </div>

            <div class="journal-grid">
                <?php foreach ($posts as $post): ?>
                    <div class="journal-card">
                        <div class="card-image-container">
                            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>">
                            
                            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                                <button class="delete-btn" onclick="deletePost(<?php echo $post['id']; ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="journal-card-content">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p><?php echo htmlspecialchars($post['description']); ?></p>
                            <a href="#" class="read-more" onclick="viewPost(<?php echo $post['id']; ?>)">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>
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
        background-image: url('https://i.pinimg.com/736x/2e/ef/7d/2eef7d91a358f4f01d276697acc38b86.jpg'); 
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

    /* Search Container Styles */
    .search-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        width: 100%;
        max-width: 600px;
        margin: 20px auto;
        padding: 0 20px;
    }

    .search-container input {
        flex: 1;
        padding: 12px 15px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
        outline: none;
    }

    .search-container input:focus {
        border-color: #022597;
    }

    .search-button {
        padding: 12px 25px;
        font-size: 16px;
        background-color: #022597;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .search-button:hover {
        background-color: #0052b1;
    }

    .no-results {
        text-align: center;
        padding: 40px;
        color: #666;
        font-size: 1.1em;
        grid-column: 1 / -1;
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
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 20px;
        padding: 20px;
    }

    .journal-card {
        background: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
        height: 400px;
        width: 100%;
    }

    .journal-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .journal-card-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .journal-card-content h3 {
        font-size: 1.1rem;
        color: #333;
        margin: 0 0 10px;
        font-weight: bold;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .journal-card-content p {
        font-size: 0.9rem;
        color: #666;
        margin: 0 0 15px;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .journal-card .read-more {
        text-decoration: none;
        color: #ffffff;
        background-color: #007bff;
        padding: 8px 12px;
        border-radius: 5px;
        font-size: 0.875rem;
        text-align: center;
        display: block;
        transition: background-color 0.3s;
        margin-top: auto;
    }

    .journal-card .read-more:hover {
        background-color: #0056b3;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .journal-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .journal-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 15px;
        }
        
        .journal-card {
            height: 380px;
        }
        
        .card-image-container {
            height: 180px;
        }

        .search-container {
            flex-direction: column;
            gap: 15px;
        }

        .search-container input,
        .search-button {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .journal-grid {
            grid-template-columns: 1fr;
            padding: 10px;
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
    </style>

    <!-- Include modal for creating posts -->
    <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
        <?php include 'components/modals/create-culture-post-modal.php'; ?>
    <?php endif; ?>

    <!-- Include sidebar and chat components -->
    <?php include 'components/layout/guest/sidebar.php'; ?>
    <?php include 'components/widgets/chat.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Pass PHP variables to JavaScript
        const isAdminUser = <?php echo isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1 ? 'true' : 'false'; ?>;
    </script>
    <script src="scripts/culture-post.js"></script>

    <!-- Post View Modal -->
    <div id="postModal" class="modal">
        <div class="modal-content post-modal">
            <span class="close">&times;</span>
            <div class="modal-body">
                <div class="modal-image">
                    <img id="modalImage" src="" alt="Post Image">
                </div>
                <div class="post-details">
                    <h2 id="modalTitle"></h2>
                    <div class="post-meta">
                        <span class="category" id="modalCategory"></span>
                        <span class="date" id="modalDate"></span>
                    </div>
                    <div class="description">
                        <p id="modalDescription"></p>
                    </div>
                    <div class="content">
                        <p id="modalContent"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        overflow-y: auto;
    }

    .post-modal {
        background-color: #fff;
        margin: 5% auto;
        padding: 0;
        width: 90%;
        max-width: 800px;
        border-radius: 8px;
        position: relative;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .modal-body {
        max-height: 85vh;
        overflow-y: auto;
    }

    .modal-image {
        width: 100%;
        height: 300px;
        position: relative;
        overflow: hidden;
    }

    #modalImage {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .post-details {
        padding: 30px;
    }

    #modalTitle {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .post-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    #modalCategory {
        background-color: #f0f0f0;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 14px;
        color: #666;
    }

    #modalDate {
        font-size: 14px;
        color: #888;
    }

    #modalDescription {
        font-size: 16px;
        line-height: 1.6;
        color: #666;
        margin-bottom: 25px;
    }

    #modalContent {
        font-size: 15px;
        line-height: 1.8;
        color: #333;
        white-space: pre-wrap;
    }

    .close {
        position: absolute;
        right: 15px;
        top: 15px;
        width: 30px;
        height: 30px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #333;
        cursor: pointer;
        z-index: 1001;
        transition: all 0.3s ease;
    }

    .close:hover {
        background-color: #fff;
        transform: rotate(90deg);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .post-modal {
            margin: 0;
            width: 100%;
            height: 100%;
            border-radius: 0;
        }

        .modal-body {
            max-height: 100vh;
        }

        .modal-image {
            height: 200px;
        }

        .post-details {
            padding: 20px;
        }
    }
    </style>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content delete-modal">
            <div class="delete-modal-header">
                <h3>Confirm Deletion</h3>
                <span class="close delete-close">&times;</span>
            </div>
            <div class="delete-modal-body">
                <p>Are you sure you want to delete this post?</p>
                <p class="warning-text">This action cannot be undone.</p>
            </div>
            <div class="delete-modal-footer">
                <button id="cancelDelete" class="btn-cancel">Cancel</button>
                <button id="confirmDelete" class="btn-delete">Delete</button>
            </div>
        </div>
    </div>

    <style>
    /* Delete Modal Styles */
    .delete-modal {
        background-color: #fff;
        margin: 15% auto;
        padding: 0;
        width: 90%;
        max-width: 400px;
        border-radius: 8px;
        position: relative;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .delete-modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .delete-modal-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.2rem;
    }

    .delete-close {
        font-size: 24px;
        cursor: pointer;
        color: #666;
        transition: color 0.3s ease;
    }

    .delete-close:hover {
        color: #333;
    }

    .delete-modal-body {
        padding: 20px;
        text-align: center;
    }

    .warning-text {
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: 10px;
    }

    .delete-modal-footer {
        padding: 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-cancel, .btn-delete {
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-cancel {
        background-color: #f8f9fa;
        color: #333;
        border: 1px solid #ddd;
    }

    .btn-cancel:hover {
        background-color: #e9ecef;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .delete-modal {
            margin: 30% auto;
            width: 95%;
        }
    }
    </style>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content success-modal">
            <div class="success-modal-body">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Success!</h3>
                <p id="successMessage">Post deleted successfully!</p>
                <button class="btn-ok" onclick="closeSuccessModal()">OK</button>
            </div>
        </div>
    </div>

    <style>
    /* Success Modal Styles */
    .success-modal {
        background-color: #fff;
        margin: 15% auto;
        padding: 30px;
        width: 90%;
        max-width: 400px;
        border-radius: 8px;
        position: relative;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .success-modal-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .success-icon {
        color: #28a745;
        font-size: 48px;
    }

    .success-modal-body h3 {
        color: #333;
        margin: 0;
        font-size: 24px;
    }

    .success-modal-body p {
        color: #666;
        margin: 0;
    }

    .btn-ok {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 30px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
        margin-top: 10px;
    }

    .btn-ok:hover {
        background-color: #218838;
    }

    @media (max-width: 576px) {
        .success-modal {
            margin: 30% auto;
            padding: 20px;
        }
    }
    </style>
</body>
</head>
</html>