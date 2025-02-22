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
    <title>Kulturabase - Culture</title>
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
                <button onclick="searchPosts()" class="search-button">Search</button>
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
    </style>

    <!-- Include modal for creating posts -->
    <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
        <?php include 'components/modals/create-culture-post-modal.php'; ?>
    <?php endif; ?>

    <!-- Include sidebar and chat components -->
    <?php include 'components/layout/guest/sidebar.php'; ?>
    <?php include 'components/widgets/chat.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts/culture-post.js"></script>

    <!-- Add this before the closing </body> tag -->
    <div id="postModal" class="modal">
        <div class="modal-content post-modal">
            <span class="close">&times;</span>
            <div id="modalContent">
                <img id="modalImage" src="" alt="Post Image">
                <div class="post-details">
                    <h2 id="modalTitle"></h2>
                    <p id="modalDescription"></p>
                    <div class="post-meta">
                        <span id="modalCategory"></span>
                        <span id="modalDate"></span>
                    </div>
                    <p id="modalContent"></p>
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
        overflow: hidden;
    }

    .close {
        position: absolute;
        right: 20px;
        top: 20px;
        font-size: 28px;
        font-weight: bold;
        color: #fff;
        cursor: pointer;
        z-index: 1001;
        background-color: rgba(0, 0, 0, 0.5);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .close:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }

    #modalImage {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
    }

    .post-details {
        padding: 20px;
    }

    .post-details h2 {
        color: #333;
        margin-bottom: 15px;
    }

    .post-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        color: #666;
        font-size: 0.9em;
    }

    #modalDescription {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    #modalContent {
        color: #333;
        line-height: 1.8;
        margin-top: 20px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .post-modal {
            margin: 0;
            width: 100%;
            height: 100%;
            border-radius: 0;
        }
    }
    </style>
</body>
</head>
</html>