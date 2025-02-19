<?php
require 'db_conn.php';
require 'dialog.php';
session_start();

// Remove the forced redirect if not logged in
$currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

function checkLoginStatus() {
    if (!isset($_SESSION['user_id'])) {
        showLoginDialog();
        return false;
    }
    return true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <img src="assets/logo/logo.png" alt="Kulturifiko Logo">
            <h1>Kulturabase</h1>
        </div>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Show full navigation for logged in users -->
                <a href="home.php">Home</a>
                <a href="create-post.php">+ Create</a>
                <a href="explore.php" class="active">Explore</a>
                <a href="notification.php">Notification</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-btn" onclick="toggleDropdown()">Menu</a>
                    <div class="dropdown-content">
                        <a href="profile.php">Profile</a>
                        <a href="settings.php">Settings</a>
                    </div>
                </div>
                <a href="#" onclick="handleLogout()">Log Out</a>
            <?php else: ?>
                <!-- Show limited navigation for non-logged in users -->
                <a href="explore.php" class="active">Explore</a>
                <a href="auth/login.php">Log In</a>
            <?php endif; ?>
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
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="posts.js"></script>
  <style>
    /* Post container */
.post {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin: 0 0 20px 0;
    padding: 20px;
    width: 100%;
    display: inline-block;
    break-inside: avoid;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.post:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

.post-content h3 {
    margin: 10px 0;
    font-size: 18px;
}

.post-content p {
    margin-bottom: 15px;
    font-size: 14px;
    color: #555;
}

.post-content img {
    width: 100%;
    border-radius: 8px;
    margin: 10px 0;
}

.post-interactions {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.like-btn, .comment-toggle {
    background: #007bff;
    color: #fff;
    border: none;
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 5px;
}

.like-btn.liked {
    background: #28a745;
}

.comments-section {
    margin-top: 20px;
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
    display: flex;
    align-items: center;
    margin-top: 15px;
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
.like-btn-disabled, .comment-toggle-disabled {
    background: #ccc;
    color: #fff;
    border: none;
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 5px;
    opacity: 0.7;
}

.like-btn-disabled:hover, .comment-toggle-disabled:hover {
    opacity: 1;
}

   .explore-container {
      max-width: 1600px;
      margin: 20px auto;
      margin-left: 260px;
      padding: 20px;
      width: calc(100% - 280px);
    }

    #post-display {
        columns: 4;
        column-gap: 20px;
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
    @media screen and (min-width: 1801px) {
        .explore-container {
            margin-left: calc((100% - 1600px) / 2 + 260px);
        }
    }

    @media screen and (max-width: 1800px) {
        #post-display {
            columns: 3;
        }
    }

    @media screen and (max-width: 1400px) {
        #post-display {
            columns: 3;
        }
    }

    @media screen and (max-width: 1200px) {
        #post-display {
            columns: 2;
        }
    }

    @media screen and (max-width: 992px) {
        .explore-container {
            margin-left: 260px;
            width: calc(100% - 280px);
            padding: 15px;
        }
        #post-display {
            columns: 2;
        }
    }

    @media screen and (max-width: 768px) {
        .sidebar {
            width: 200px;
        }
        
        .explore-container {
            margin-left: 210px;
            width: calc(100% - 220px);
            padding: 10px;
        }
        #post-display {
            columns: 1;
        }
    }

    @media screen and (max-width: 576px) {
        .sidebar {
            display: none;
        }
        
        .explore-container {
            width: 100%;
            margin: 10px auto;
            padding: 10px;
        }
        
        #post-display {
            columns: 1;
            padding: 0;
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
    .like-btn, .comment-toggle {
        transition: all 0.2s ease-in-out;
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
  font-size: 15px;
  margin-bottom: 8px;
  color: #DCF2F1;
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

</body>
</head>
</html>