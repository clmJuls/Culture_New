<?php
require 'db_conn.php';
session_start();

// Remove the forced redirect if not logged in
$currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        /* Add the new comment styles here */
        .comments-section {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .comment {
            display: flex;
            align-items: start;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .comment-profile-pic {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-content {
            flex: 1;
        }

        .comment-content strong {
            display: block;
            margin-bottom: 5px;
        }

        .comment-input {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .comment-text {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-comment {
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-comment:hover {
            background: #0056b3;
        }

        /* Comment styles */
        .comments-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 20px;
        }

        .comment {
            padding: 12px;
            border-radius: 12px;
            background-color: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .comment-user-info {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            flex: 1;
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .comment-time {
            color: #666;
            font-size: 12px;
        }

        .delete-comment {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 4px;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .delete-comment:hover {
            opacity: 1;
        }

        .comment-profile-pic {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-content {
            flex: 1;
        }

        .comment-username {
            font-size: 14px;
            color: #333;
            margin-bottom: 4px;
        }

        .comment-text {
            font-size: 14px;
            color: #4a4a4a;
            margin: 0;
            line-height: 1.4;
        }

        .no-comments {
            color: #666;
            text-align: center;
            padding: 20px;
            font-style: italic;
        }
    </style>

    <!-- Navigation Bar -->
    <?php
    if (isset($_SESSION['user_id'])) {
        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
            include 'components/layout/admin/navbar.php';
        } else {
            include 'components/layout/guest/navbar.php';
        }
    } else {
        include 'components/layout/auth/navbar.php';
    }
    ?>

    <script>
        function toggleDropdown() {
            var dropdownContent = document.querySelector(".dropdown-content");
            dropdownContent.classList.toggle("show");
        }

        function handleUnauthorizedAction(action) {
            <?php if (!isset($_SESSION['user_id'])): ?>
                if (confirm('Please log in to ' + action + '. Click OK to go to login page.')) {
                    window.location.href = 'auth/login.php';
                }
                return false;
            <?php endif; ?>
            return true;
        }

        function handleLikeClick(postId) {
            if (!handleUnauthorizedAction('like posts')) {
                return;
            }
            // Your existing like functionality
        }

        function handleCommentClick(postId) {
            if (!handleUnauthorizedAction('comment on posts')) {
                return;
            }
            // Your existing comment functionality
        }

        function handleLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'auth/logout.php';
            }
        }
    </script>

    <div class="explore-container">
        <div id="post-display"></div>
        <div class="view-more-container">
            <button id="view-more-btn" class="view-more-btn">View More</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Pass PHP variables to JavaScript
        const currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>;
        const isAdmin = <?php echo isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ? 'true' : 'false' ?>;
        const currentUsername = <?php echo isset($_SESSION['username']) ? "'" . $_SESSION['username'] . "'" : 'null' ?>;
        const currentUserProfilePic = <?php echo isset($_SESSION['profile_picture']) ? "'" . $_SESSION['profile_picture'] . "'" : 'null' ?>;
    </script>
    <script src="scripts/explore.js"></script>
    <style>
        /* Post container */
        .post {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin: 0 0 5px 0;
            padding: 5px;
            width: 100%;
            display: flex;
            flex-direction: column;
            min-height: 350px;
            /* Set a minimum height for consistency */
            position: relative;
            /* For absolute positioning of interactions */
        }

        .post:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .post-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .post-header span {
            font-weight: bold;
            font-size: 16px;
        }

        .delete-post {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            opacity: 0.7;
            transition: opacity 0.2s ease;
            width: 24px;
            height: 24px;
        }

        .delete-post img {
            width: 100%;
            height: 100%;
        }

        .delete-post:hover {
            opacity: 1;
        }

        .post-content {
            flex: 1;
            overflow: hidden;
            margin-bottom: 60px;
            /* Space for the interaction buttons */
        }

        .post-title {
            font-size: 18px;
            color: #000;
        }

        .post-content h3 {
            margin: 10px 0;
            font-size: 18px;
            color: #000;
        }

        .post-content p {
            margin-bottom: 15px;
            font-size: 14px;
            color: #000;
        }

        .post-content img {
            width: 100%;
            border-radius: 8px;
            margin: 10px 0;
        }

        .post-interactions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
            background-color: #fff;
            border-top: 1px solid #eee;
            border-radius: 0 0 12px 12px;
        }

        .like-btn,
        .comment-toggle {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            flex: 1;
            margin: 0 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .like-btn.liked {
            background: #28a745;
        }

        .comments-section {
            position: absolute;
            bottom: 60px;
            /* Height of interaction buttons */
            left: 0;
            right: 0;
            background: white;
            padding: 15px 20px;
            border-top: 1px solid #eee;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            z-index: 1;
        }

        .comment {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 10px;
            background: #f7f7f7;
            border-radius: 8px;
        }

        .comment-profile-pic {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .comment-content {
            display: flex;
            flex-direction: column;
        }

        .comment-content strong {
            font-weight: bold;
            font-size: 14px;
        }

        .comment-content p {
            margin: 5px 0;
            font-size: 13px;
            color: #666;
        }

        .delete-comment {
            background: transparent;
            border: none;
            font-size: 12px;
            color: #dc3545;
            cursor: pointer;
            align-self: flex-start;
        }

        .comment-input {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 10px 0;
            border-top: 1px solid #eee;
        }

        .comment-text {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin-right: 10px;
        }

        .submit-comment {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 8px 12px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Add the new styles here */
        .like-btn-disabled,
        .comment-toggle-disabled {
            background: #ccc;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            opacity: 0.7;
            flex: 1;
            margin: 0 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .like-btn-disabled:hover,
        .comment-toggle-disabled:hover {
            opacity: 1;
        }

        .explore-container {
            max-width: 1200px;
            margin: 20px auto;
            margin-left: 260px;
            margin-right: 400px;
            padding: 20px;
            width: calc(100% - 680px);
        }

        #post-display {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 0;
            width: 100%;
        }

        .post-container {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .post-header div {
            font-size: 14px;
        }

        .post-header strong {
            font-size: 16px;
            color: #333;
        }

        .post-body {
            margin-top: 10px;
            font-size: 16px;
            line-height: 1.6;
        }

        .post-body img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            margin-top: 15px;
            border-radius: 5px;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .post-footer button {
            background: none;
            border: none;
            cursor: pointer;
            color: #555;
            font-size: 16px;
            transition: color 0.3s;
        }

        .post-footer button:hover {
            color: #007bff;
        }

        .post-footer .like-btn,
        .post-footer .comment-btn,
        .post-footer .share-btn {
            padding: 5px 10px;
        }

        /* Tag Style for Elements */
        .tags-container {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tag {
            background-color: #e7f1ff;
            color: #007bff;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 14px;
            border: 1px solid #007bff;
            transition: all 0.3s ease;
        }

        .tag:hover {
            background-color: #007bff;
            color: #fff;
        }

        /* Add responsive media queries */
        @media screen and (max-width: 1200px) {
            #post-display {
                grid-template-columns: repeat(2, 1fr);

            }

            .post {
                min-height: 350px;
            }
        }

        @media screen and (max-width: 768px) {
            #post-display {
                grid-template-columns: repeat(1, 1fr);
            }

            .post {
                min-height: 300px;
            }

            .post-interactions {
                padding: 10px 15px;
            }

            .like-btn,
            .comment-toggle {
                padding: 6px 12px;
                font-size: 13px;
            }
        }

        /* Learning Styles Section */
        .learning-styles {
            margin: 15px 0;
            padding: 0;
        }

        .learning-styles h4 {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .learning-styles ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .learning-styles li {
            display: inline-block;
            background-color: #f0f0f0;
            padding: 6px 12px;
            border-radius: 15px;
            margin: 0 8px 8px 0;
            font-size: 14px;
            color: #555;
        }

        /* Like Button Styling */
        .like-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            transition: background-color 0.2s ease;
        }

        .like-button:hover {
            background-color: #218838;
        }

        .like-button i {
            font-size: 16px;
        }

        .like-count {
            font-weight: 500;
        }

        /* Post Elements Styling */
        .post {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0 0 20px 0;
            padding: 20px;
            width: 100%;
            display: inline-block;
            break-inside: avoid;
            word-wrap: break-word;
        }

        /* Culture Elements & Learning Styles Sections */
        .culture-elements,
        .learning-styles {
            margin: 15px 0;
        }

        .culture-elements h4,
        .learning-styles h4 {
            font-size: 16px;
            color: #333;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .elements-list,
        .styles-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .elements-list li,
        .styles-list li {
            display: inline-block;
            background-color: #f0f0f0;
            padding: 6px 12px;
            border-radius: 15px;
            margin: 0 8px 8px 0;
            font-size: 14px;
            color: #555;
            transition: all 0.2s ease;
        }

        .elements-list li:hover,
        .styles-list li:hover {
            background-color: #e0e0e0;
            transform: translateY(-1px);
        }

        /* Like Button Styling */
        .like-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 15px;
            transition: background-color 0.2s ease;
        }

        .like-button:hover {
            background-color: #0056b3;
        }

        .like-button i {
            font-size: 16px;
        }

        .like-count {
            font-weight: 500;
        }

        /* Image container */
        .post-image {
            width: 100%;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .post-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Category label */
        .category-label {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 14px;
        }

        /* Hover effects */
        .post {
            cursor: pointer;
        }

        .post:hover .post-image img {
            transform: scale(1.02);
            transition: transform 0.3s ease;
        }

        /* Like Button Animation */
        .like-btn {
            transition: all 0.2s ease-in-out;
        }

        .like-animation {
            transform: scale(1.2);
        }

        .liked {
            background-color: #28a745;
            transform: scale(1);
        }

        .like-btn:active {
            transform: scale(0.95);
        }

        /* Smooth transition for all button states */
        .like-btn,
        .comment-toggle {
            transition: all 0.2s ease-in-out;
        }

        /* Add this to your existing CSS */
        .post-media {
            width: 100%;
            max-height: 150px;
            /* Limit media height */
            object-fit: cover;
            border-radius: 8px;
            margin: 10px 0;
        }

        video.post-media {
            background-color: #000;
        }

        /* Optional: Add a custom video player style */
        video.post-media::-webkit-media-controls {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 0 0 8px 8px;
        }

        /* Ensure proper video container sizing */
        .post-content {
            width: 100%;
            overflow: hidden;
        }

        .view-more-container {
            text-align: center;
            margin: 20px 0;
        }

        .view-more-btn {
            background: #365486;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .view-more-btn:hover {
            background: #7FC7D9;
            transform: translateY(-2px);
        }

        .view-more-btn.loading {
            opacity: 0.7;
            cursor: wait;
        }
    </style>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-section">
        </div>

        <div class="menu-section">
            <h3>Elements of Culture</h3>
            <div class="menu-item">
                <ul>
                    <li><a href="geography.php">Geography</a></li>
                    <li><a href="history.php">History</a></li>
                    <li><a href="demographics.php">Demographics</a></li>
                    <li><a href="culture.php">Culture</a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3>Learning Styles</h3>
                <div class="menu-item">
                    <ul>
                        <li><input type="checkbox">Visual</li>
                        <li><input type="checkbox">Auditory & Oral</li>
                        <li><input type="checkbox">Read & Write</li>
                        <li><input type="checkbox">Kinesthetic</li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3>Location</h3>
                    <div class="menu-item">
                        <a href="choose-loc.php"><span>+</span> Choose a location</a>
                    </div>
                </div>

                <div class="menu-section">
                    <h3>Resources</h3>
                    <div class="menu-item">
                        <span>🔗</span>
                        <a href="#">About Kulturifiko</a>
                    </div>
                </div>
            </div>

            <style>
                /* Sidebar */
                .sidebar {
                    position: fixed;
                    top: 60px;
                    left: 0;
                    width: 240px;
                    height: 100vh;
                    background-color: #365486;
                    padding-top: 30px;
                    z-index: 999;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    overflow-y: auto;
                    flex-grow: 1;
                    box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
                    border-radius: 0 5px 5px 0;
                }

                /* Logo Section */
                .logo-section {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-top: 15px;
                    margin-bottom: 15px;
                }

                .logo-section img {
                    max-width: 100px;
                    border-radius: 5px;
                }

                /* Section Menus */
                .menu-section {
                    margin-bottom: 10px;
                }

                .menu-section h3 {
                    font-size: 18px;
                    margin-bottom: 8px;
                    color: #DCF2F1
                }

                /* Menu Items */
                .menu-item {
                    display: inline-block;
                    align-items: center;
                    justify-content: flex-start;
                    margin: 3px 0;
                    cursor: pointer;
                    transition: background 0.2s ease;
                    padding: 5px 5px;
                    border-radius: 4px;
                    color: #ffffff;
                }

                .menu-item a {
                    color: #ffffff;
                    text-decoration: none;
                    font-size: .8rem;
                    font-weight: 500;
                    padding: 5px 10px;
                    border-radius: 30px;
                }

                .menu-item a:hover {
                    background-color: #7FC7D9;
                    color: #0F1035;
                }

                .menu-item a.active {
                    background-color: #1e3c72;
                    color: #fff;
                }

                .menu-item ul {
                    list-style: none;
                    padding: 0;
                }

                .menu-item li {
                    margin-bottom: 10px;
                    font-size: .8rem;
                }

                input[type="checkbox"] {
                    margin-right: 5px;
                }

                #chosen-location-container {
                    margin-top: 20px;
                    display: block;
                }

                #chosen-location-container label {
                    font-size: 12px;
                    color: #ffffff;
                }
            </style>

            <!-- Trending Posts -->
            <?php include 'components/explore/trend.php'; ?>
            <?php include 'components/widgets/chat.php'; ?>

            <!-- Add this modal HTML just before the closing </body> tag -->
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Delete Post</h2>
                        <span class="close-modal">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this post? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="cancel-btn">Cancel</button>
                        <button class="delete-confirm-btn">Delete</button>
                    </div>
                </div>
            </div>

            <!-- Add this before the closing </body> tag, after the delete modal -->
            <div id="postViewModal" class="post-modal">
                <div class="post-modal-content">
                    <span class="close-post-modal" style="display: none;">&times;</span>
                    <div id="expanded-post-content"></div>
                </div>
            </div>

            <!-- Add these styles to your existing CSS -->
            <style>
                .modal {
                    display: none;
                    position: fixed;
                    z-index: 1000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    animation: fadeIn 0.3s ease;
                }

                .modal-content {
                    background-color: #fefefe;
                    margin: 15% auto;
                    padding: 0;
                    border-radius: 8px;
                    width: 400px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    animation: slideIn 0.3s ease;
                }

                .modal-header {
                    padding: 15px 20px;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .modal-header h2 {
                    margin: 0;
                    font-size: 1.2rem;
                    color: #333;
                }

                .close-modal {
                    color: #aaa;
                    font-size: 24px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: color 0.2s ease;
                }

                .close-modal:hover {
                    color: #333;
                }

                .modal-body {
                    padding: 20px;
                    color: #666;
                }

                .modal-footer {
                    padding: 15px 20px;
                    border-top: 1px solid #eee;
                    display: flex;
                    justify-content: flex-end;
                    gap: 10px;
                }

                .cancel-btn,
                .delete-confirm-btn {
                    padding: 8px 16px;
                    border-radius: 4px;
                    border: none;
                    cursor: pointer;
                    font-size: 14px;
                    transition: all 0.2s ease;
                }

                .cancel-btn {
                    background-color: #e0e0e0;
                    color: #333;
                }

                .cancel-btn:hover {
                    background-color: #d0d0d0;
                }

                .delete-confirm-btn {
                    background-color: #dc3545;
                    color: white;
                }

                .delete-confirm-btn:hover {
                    background-color: #c82333;
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

                .post-modal {
                    display: none;
                    /* Initial state is hidden */
                    position: fixed;
                    z-index: 1001;
                    left: 0;
                    top: 50px;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.8);
                    overflow-y: auto;
                }

                /* When active, use these styles */
                .post-modal.active {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .post-modal-content {
                    background-color: #fff;
                    padding: 30px;
                    width: 90%;
                    max-width: 800px;
                    /* Reduced from 900px for better readability */
                    border-radius: 12px;
                    position: relative;
                    animation: zoomIn 0.3s ease;
                    max-height: 90vh;
                    overflow-y: auto;
                    /* Allow scrolling if content is too long */
                    margin: 20px auto;
                    /* Center the modal and add some vertical spacing */
                }

                /* Update post content styles inside modal */
                #expanded-post-content {
                    padding: 0;
                    /* Remove default padding */
                }

                #expanded-post-content .post-header {
                    margin-bottom: 20px;
                    padding: 0;
                }

                #expanded-post-content .post-content {
                    margin: 20px 0;
                    padding: 0;
                    position: relative;
                    /* Reset position */
                    margin-bottom: 20px;
                    /* Add space between content and interactions */
                }

                #expanded-post-content .post-title {
                    font-size: 24px;
                    font-weight: 600;
                    color: #333;
                    margin-bottom: 15px;
                    display: block;
                }

                #expanded-post-content p {
                    font-size: 16px;
                    line-height: 1.6;
                    color: #4a4a4a;
                    margin-bottom: 20px;
                }

                #expanded-post-content .post-media {
                    max-height: 500px;
                    width: auto;
                    /* Change from 100% to auto */
                    max-width: 100%;
                    /* Ensure image doesn't overflow */
                    object-fit: contain;
                    margin: 20px auto;
                    /* Center media content */
                    display: block;
                    /* Helps with centering */
                }

                #expanded-post-content .post-interactions {
                    position: relative;
                    margin-top: 20px;
                    padding-top: 20px;
                    border-top: 1px solid #eee;
                    display: flex;
                    gap: 10px;
                }

                /* Responsive adjustments */
                @media screen and (max-width: 768px) {
                    .post-modal-content {
                        width: 95%;
                        padding: 20px;
                        margin: 10px auto;
                    }

                    #expanded-post-content .post-title {
                        font-size: 20px;
                    }

                    #expanded-post-content p {
                        font-size: 14px;
                    }
                }

                @keyframes zoomIn {
                    from {
                        transform: scale(0.95);
                        opacity: 0;
                    }

                    to {
                        transform: scale(1);
                        opacity: 1;
                    }
                }

                /* Modal comment styles */
                .modal-comments-container {
                    display: flex;
                    flex-direction: column;
                    max-height: 400px;
                    margin-top: 20px;
                }

                .comments-section {
                    flex: 1;
                    overflow-y: auto;
                    padding: 20px;
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                }

                .comment {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    padding: 12px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }

                .comment-user-info {
                    display: flex;
                    gap: 12px;
                    align-items: flex-start;
                    flex: 1;
                }

                .comment-input-wrapper {
                    display: flex;
                    flex: 1;
                    gap: 8px;
                    align-items: center;
                }

                .modal-comment-input {
                    position: sticky;
                    bottom: 0;
                    background: white;
                    padding: 16px;
                    border-top: 1px solid #eee;
                    margin-top: auto;
                }

                .comments-section:empty::before {
                    content: 'No comments yet';
                    text-align: center;
                    color: #666;
                    padding: 20px;
                    font-style: italic;
                }
            </style>

            </body>
</head>

</html>