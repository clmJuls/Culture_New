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
    <title>KulturaBase</title>
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

        /* Modal Styles */
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
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.2rem;
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
            text-align: center;
        }

        .warning-text {
            color: #666;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #e5e5e5;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .cancel-btn, .confirm-delete-btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .cancel-btn {
            background-color: #e5e5e5;
            color: #333;
        }

        .cancel-btn:hover {
            background-color: #d5d5d5;
        }

        .confirm-delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .confirm-delete-btn:hover {
            background-color: #c82333;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

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
            margin-bottom: 15px;
        }

        .post-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .post-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
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

        /* Learning Styles Section Redesign */
        .learning-styles-section {
            margin: 15px 0;
        }

        .learning-styles-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-top: 10px;
        }

        .style-option {
            position: relative;
        }

        .style-option input[type="checkbox"] {
            display: none;
        }

        .style-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            gap: 6px;
        }

        .style-option label:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .style-option input[type="checkbox"]:checked + label {
            background: #7FC7D9;
        }

        .style-option input[type="checkbox"]:checked + label i,
        .style-option input[type="checkbox"]:checked + label span {
            color: #0F1035;
        }

        .style-option i {
            font-size: 1.2rem;
            color: #DCF2F1;
        }

        .style-option span {
            font-size: 0.75rem;
            color: #DCF2F1;
            text-align: center;
        }

        /* Following Section Redesign */
        .following-section {
            margin: 15px 0;
        }

        .following-option {
            position: relative;
            margin-top: 10px;
        }

        .following-option input[type="checkbox"] {
            display: none;
        }

        .following-button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .button-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .following-icon {
            color: #DCF2F1;
            font-size: 0.9rem;
        }

        .toggle-indicator {
            width: 18px;
            height: 18px;
            background: #DCF2F1;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .following-text {
            color: #DCF2F1;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .following-button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .following-option input[type="checkbox"]:checked + .following-button {
            background: #7FC7D9;
        }

        .following-option input[type="checkbox"]:checked + .following-button .following-text,
        .following-option input[type="checkbox"]:checked + .following-button .following-icon {
            color: #0F1035;
        }

        .following-option input[type="checkbox"]:checked + .following-button .toggle-indicator {
            background: #0F1035;
            transform: translateX(4px);
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
            max-height: 500px;
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

        /* Search Bar Styles */
        .search-bar-container {
            margin-bottom: 20px;
            width: 100%;
        }

        .search-input-wrapper {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }

        #post-search {
            width: 100%;
            padding: 12px 40px 12px 20px;
            border: 2px solid #365486;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: white;
            color: #333;
        }

        #post-search:focus {
            outline: none;
            border-color: #7FC7D9;
            box-shadow: 0 0 10px rgba(127, 199, 217, 0.2);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #365486;
            font-size: 18px;
            pointer-events: none;
        }

        #post-search::placeholder {
            color: #999;
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
        <div class="search-bar-container">
            <div class="search-input-wrapper">
                <input type="text" id="post-search" placeholder="Search posts by title...">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>
        <div id="post-display"></div>
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
            padding: 15px;
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

        /* Learning Styles Section Redesign */
        .learning-styles-section {
            margin: 15px 0;
        }

        .learning-styles-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-top: 10px;
        }

        .style-option {
            position: relative;
        }

        .style-option input[type="checkbox"] {
            display: none;
        }

        .style-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            gap: 6px;
        }

        .style-option label:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .style-option input[type="checkbox"]:checked + label {
            background: #7FC7D9;
        }

        .style-option input[type="checkbox"]:checked + label i,
        .style-option input[type="checkbox"]:checked + label span {
            color: #0F1035;
        }

        .style-option i {
            font-size: 1.2rem;
            color: #DCF2F1;
        }

        .style-option span {
            font-size: 0.75rem;
            color: #DCF2F1;
            text-align: center;
        }

        /* Following Section Redesign */
        .following-section {
            margin: 15px 0;
        }

        .following-option {
            position: relative;
            margin-top: 10px;
        }

        .following-option input[type="checkbox"] {
            display: none;
        }

        .following-button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .button-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .following-icon {
            color: #DCF2F1;
            font-size: 0.9rem;
        }

        .toggle-indicator {
            width: 18px;
            height: 18px;
            background: #DCF2F1;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .following-text {
            color: #DCF2F1;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .following-button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .following-option input[type="checkbox"]:checked + .following-button {
            background: #7FC7D9;
        }

        .following-option input[type="checkbox"]:checked + .following-button .following-text,
        .following-option input[type="checkbox"]:checked + .following-button .following-icon {
            color: #0F1035;
        }

        .following-option input[type="checkbox"]:checked + .following-button .toggle-indicator {
            background: #0F1035;
            transform: translateX(4px);
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
            max-height: 500px;
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

        /* Audio player styling */
        .audio-player-container {
            margin: 15px 0;
            border-radius: 8px;
            overflow: visible !important;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            min-height: 80px;
            width: 100%;
            display: block !important;
        }

        .audio-player {
            padding: 20px;
            display: flex !important;
            align-items: center;
            gap: 15px;
            background: #f8f9fa;
            width: 100%;
            min-height: 60px;
        }

        .audio-player i {
            font-size: 24px;
            color: #365486;
            flex-shrink: 0;
        }

        .audio-player audio {
            flex-grow: 1;
            min-width: 200px;
        }

        /* Ensure media containers don't hide audio players */
        .media-container:has(.audio-player-container) {
            height: auto !important;
            min-height: 80px !important;
            overflow: visible !important;
        }

        /* Force audio players to be visible with high specificity */
        .post .media-container .audio-player-container,
        .post-content .media-container .audio-player-container {
            display: block !important;
            visibility: visible !important;
            height: auto !important;
            min-height: 80px !important;
            overflow: visible !important;
            background: #f8f9fa !important;
            border: 2px solid #365486 !important;
            margin: 10px 0 !important;
        }

        .post .audio-player,
        .post-content .audio-player {
            display: flex !important;
            visibility: visible !important;
            padding: 15px !important;
            align-items: center !important;
            gap: 15px !important;
            background: #f8f9fa !important;
        }

        .post .audio-player audio,
        .post-content .audio-player audio {
            display: block !important;
            visibility: visible !important;
            width: 100% !important;
            min-width: 200px !important;
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

            <div class="menu-section learning-styles-section">
                <h3>Learning Styles</h3>
                <div class="learning-styles-grid" id="learning-styles-filter">
                    <div class="style-option">
                        <input type="checkbox" id="visual-filter" value="visual">
                        <label for="visual-filter">
                            <i class="fas fa-eye"></i>
                            <span>Visual</span>
                        </label>
                    </div>
                    <div class="style-option">
                        <input type="checkbox" id="auditory-filter" value="auditory">
                        <label for="auditory-filter">
                            <i class="fas fa-headphones"></i>
                            <span>Auditory & Oral</span>
                        </label>
                    </div>
                    <div class="style-option">
                        <input type="checkbox" id="read-write-filter" value="Read & Write">
                        <label for="read-write-filter">
                            <i class="fas fa-book-reader"></i>
                            <span>Read & Write</span>
                        </label>
                    </div>
                    <div class="style-option">
                        <input type="checkbox" id="kinesthetic-filter" value="kinesthetic">
                        <label for="kinesthetic-filter">
                            <i class="fas fa-running"></i>
                            <span>Kinesthetic</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="menu-section following-section">
                <h3>Following</h3>
                <div class="following-option">
                    <input type="checkbox" id="following-checkbox" value="following">
                    <label for="following-checkbox" class="following-button">
                        <div class="button-content">
                            <i class="fas fa-users following-icon"></i>
                            <span class="following-text">All Posts</span>
                        </div>
                        <div class="toggle-indicator"></div>
                    </label>
                </div>
            </div>

            <div class="menu-section">
                <h3>Resources</h3>
                <div class="menu-item">
                    <span>🔗</span>
                    <a href="about.php">About Kulturifiko</a>
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

        <!-- Add these before the closing </head> tag -->
        <link rel="stylesheet" href="modal/post-modal.css">
        <script src="modal/post-modal.js"></script>

        </head>

</html>

<!-- Add this just before the closing </body> tag -->
<div id="postViewModal" class="post-modal">
    <div class="post-modal-content">
        <span class="close-post-modal" style="display: none;">&times;</span>
        <div id="expanded-post-content"></div>
    </div>
</div>

<!-- Add this before the closing </body> tag, after the post modal -->
<div id="commentDeleteModal" class="comment-modal">
    <div class="comment-modal-content">
        <div class="modal-header">
            <h3>Delete Comment</h3>
            <span class="close-comment-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this comment?</p>
            <div class="comment-preview">
                <div class="comment-text-preview"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="cancel-delete">Cancel</button>
            <button class="confirm-delete">Delete</button>
        </div>
    </div>
</div>

<!-- Add this before the closing </body> tag -->
<div id="deleteConfirmModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Delete Post</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this post?</p>
            <p class="warning-text">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button class="cancel-btn">Cancel</button>
            <button class="confirm-delete-btn">Delete</button>
        </div>
    </div>
</div>

<script>
    // Add this to your existing JavaScript
    document.getElementById('following-checkbox').addEventListener('change', function() {
        const textSpan = this.nextElementSibling.querySelector('.following-text');
        textSpan.textContent = this.checked ? 'Following Posts' : 'All Posts';
    });
</script>