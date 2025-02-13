<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to update your information.');
            window.location.href = 'login.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information from the database
$query = "SELECT full_name, profile_picture, username, about, location, birthday, website, skills FROM users WHERE id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $full_name = htmlspecialchars($user['full_name']);
    $username = htmlspecialchars($user['username']);
    $about = htmlspecialchars($user['about']);
    $location = htmlspecialchars($user['location']);
    $birthday = htmlspecialchars($user['birthday']);
    $website = htmlspecialchars($user['website']);
    $skills = htmlspecialchars($user['skills']);
    $profile_picture = htmlspecialchars($user['profile_picture']);
} else {
    echo "<script>
            alert('User not found.');
            window.location.href = 'login.php';
          </script>";
    exit();
}
$name_parts = explode(' ', $full_name);
$first_initial = strtoupper(substr($name_parts[0], 0, 1)); // First letter of first 
$last_name = strtoupper(substr($name_parts[1], 0, 1));
// $last_name = isset($name_parts[1]) ? strtoupper($name_parts[1]) : ''; // Full last name (if available)
$avatar_text = $first_initial . $last_name;
$conn->close();
?>
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
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <img src="logo.png" alt="Kulturifiko Logo">
            <h1>Kulturabase</h1>
        </div>
        <div>
            <a href="home.php">Home</a>
            <a href="create-post.php">+ Create</a>
            <a href="explore.php">Explore</a>
            <a href="notification.php">Notification</a>
            <div class="dropdown">
                <a href="#" class="dropdown-btn active" onclick="toggleDropdown()">Menu</a>
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="settings.php">Settings</a>
                </div>
            </div>
            <a href="generate_report.php">Generate Report</a>
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

<div class="profile-page">
    <!-- Cover Photo -->
    <!-- <div class="cover-photo">
      <img src="https://via.placeholder.com/800x300" alt="Cover Photo">
    </div> -->

    <!-- Profile Header -->
    <div class="profile-header" style="margin-top: 100px; text-align: center;">
          <div class="profile-picture" style="margin-bottom: 20px; display: flex; justify-content: center; align-items: center;">
            <div class="profile-img" style="display: inline-block;">
              <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" id="profile-img" class="profile-img-preview" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
            </div>
        </div>
        <div class="user-info">
            <h2><?php echo $full_name; ?></h2>
            <p>@<?php echo $username; ?></p>
            <div class="user-stats" style="margin: 10px 0;">
                <!-- <span><strong>1,234</strong> Followers</span>
                <span style="margin-left: 15px;"><strong>567</strong> Following</span> -->
            </div>
            <a href="edit-profile.php">
                <button class="edit-profile-btn" style="
                    padding: 10px 20px; 
                    font-size: 16px; 
                    color: white; 
                    background-color: #007bff; 
                    border: none; 
                    border-radius: 4px; 
                    cursor: pointer;">Edit Profile
                </button>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="profile-nav">
      <button id="posts-tab" class="active-tab">Posts</button>
      <button id="about-tab">About</button>
      <!-- <button id="friends-tab">Friends</button> -->
    </div>

    <!-- Content Sections -->
    <div class="content-sections">
      <!-- Posts Section -->
      <div id="posts-section" class="content-section active-section">
        <div id="posts-container">
        </div>
      </div>

      <!-- About Section -->
      <div id="about-section" class="content-section" style="margin: 20px auto; max-width: 600px; text-align: left;">
          <h3>About</h3>
          <p class="about-description" style="margin: 10px 0;"><?php echo $about ? $about : "No description provided."; ?></p>

          <div class="about-details" style="margin-top: 15px;">
              <div class="detail" style="margin-bottom: 10px;">
                  <strong>Location:</strong> <span><?php echo $location ? $location : "Not specified."; ?></span>
              </div>
              <div class="detail" style="margin-bottom: 10px;">
                  <strong>Birthday:</strong> <span><?php echo $birthday ? $birthday : "Not specified."; ?></span>
              </div>
              <div class="detail" style="margin-bottom: 10px;">
                  <strong>Website:</strong> <span>
                      <?php 
                          echo $website ? "<a href='$website' target='_blank'>$website</a>" : "Not provided.";
                      ?>
                  </span>
              </div>
              <div class="detail" style="margin-bottom: 10px;">
                  <strong>Skills:</strong> <span><?php echo $skills ? $skills : "No skills listed."; ?></span>
              </div>
          </div>
      </div>

  <div class="social-links">
    <a href="https://facebook.com" class="social-icon">Facebook</a>
    <a href="https://linkedin.com" class="social-icon">LinkedIn</a>
    <a href="https://github.com" class="social-icon">GitHub</a>
  </div>
</div>

      <!-- Friends Section -->
      <div id="friends-section" class="content-section">
        <h3>Friends</h3>
        <div class="friends-list">
          <div class="friend">
            <img src="https://via.placeholder.com/100" alt="Friend 1">
            <p>Friend 1</p>
          </div>
          <div class="friend">
            <img src="https://via.placeholder.com/100" alt="Friend 2">
            <p>Friend 2</p>
          </div>
          <div class="friend">
            <img src="https://via.placeholder.com/100" alt="Friend 3">
            <p>Friend 3</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Fetch and display posts
        function loadPosts() {
            $.ajax({
                url: 'posts.php',
                method: 'GET',
                data: {
                    action: 'fetch',
                    // post_id: postId
                },
                dataType: 'json',
                success: function(posts) {
                    console.log(posts)
                    const postsContainer = $('#posts-container');
                    postsContainer.empty();

                    if (posts.length === 0) {
                        postsContainer.append('<p>No posts yet.</p>');
                        return;
                    }

                    posts.forEach(function(post) {
                        const postHtml = `
                            <div class="post" data-post-id="${post.id}">
                                <div class="post-header">
                                    <div class="post-profile">
                                        <img class="post-profile-pic" src="${post.profile_picture || 'default-avatar.png'}" alt="User">
                                        <div class="post-user-info">
                                            <h4>${post.full_name}</h4>
                                            <p>@${post.username}</p>
                                            <p class="post-time">${post.formatted_date}</p>
                                        </div>
                                    </div>
                                    <div class="post-options">
                                        <span class="three-dots">&#x22EE;</span>
                                        <div class="post-options-menu">
                                            <ul>
                                          
                                                <li class="delete-post">Delete Post</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <h4>${post.title}</h4>
                                    <p>${post.description}</p>
                                    ${post.file_path ? `<img src="${post.file_path}" alt="Post Image">` : ''}
                                    <div class="post-stats">
                                        <span>${post.like_count} Likes</span>
                                        <span>${post.comment_count} Comments</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        postsContainer.append(postHtml);
                    });

                    // Attach delete post event
                    $('.delete-post').on('click', function() {
                        const postElement = $(this).closest('.post');
                        const postId = postElement.data('post-id');

                        if (confirm('Are you sure you want to delete this post?')) {
                            $.ajax({
                                url: 'posts.php',
                                method: 'POST',
                                data: {
                                    action: 'delete',
                                    post_id: postId
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        postElement.remove();
                                        alert('Post deleted successfully');
                                    } else {
                                        alert(response.error);
                                    }
                                },
                                error: function() {
                                    alert('Error deleting post');
                                }
                            });
                        }
                    });
                },
                error: function() {
                    $('#posts-container').html('<p>Error loading posts.</p>');
                }
            });
        }

        // Initial load
        loadPosts();
    });
    </script>
  <script>
    // JavaScript for Tab Navigation
    const postsTab = document.getElementById('posts-tab');
    const aboutTab = document.getElementById('about-tab');
    const friendsTab = document.getElementById('friends-tab');
    const postsSection = document.getElementById('posts-section');
    const aboutSection = document.getElementById('about-section');
    const friendsSection = document.getElementById('friends-section');

    postsTab.addEventListener('click', () => {
      postsTab.classList.add('active-tab');
      aboutTab.classList.remove('active-tab');
      friendsTab.classList.remove('active-tab');
      postsSection.classList.add('active-section');
      aboutSection.classList.remove('active-section');
      friendsSection.classList.remove('active-section');
    });

    aboutTab.addEventListener('click', () => {
      aboutTab.classList.add('active-tab');
      postsTab.classList.remove('active-tab');
      friendsTab.classList.remove('active-tab');
      aboutSection.classList.add('active-section');
      postsSection.classList.remove('active-section');
      friendsSection.classList.remove('active-section');
    });

    friendsTab.addEventListener('click', () => {
      friendsTab.classList.add('active-tab');
      postsTab.classList.remove('active-tab');
      aboutTab.classList.remove('active-tab');
      friendsSection.classList.add('active-section');
      postsSection.classList.remove('active-section');
      aboutSection.classList.remove('active-section');
    });
  </script>

  <style>
.profile-page {
  max-width: 800px;
  margin: 0 auto;
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
}

/* Cover Photo */
.cover-photo img {
  width: 100%;
  height: 400px;
  object-fit: cover;
  display: block;
}

/* Profile Header */
.profile-header {
  text-align: center;
  position: relative;
  padding: 20px;
  background-color: #fff;
  margin-top: -75px;
}

.profile-picture {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto;
  border-radius: 50%;
  border: 4px solid #fff;
  overflow: hidden;
}

.profile-picture img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-info h2 {
  margin: 10px 0 5px;
  font-size: 24px;
  font-weight: bold;
}

.user-info p {
  margin: 0;
  color: #666;
  font-size: 16px;
}

.user-stats {
  margin: 10px 0;
  font-size: 16px;
  color: #444;
}

.user-stats span {
  margin-right: 20px;
}

/* Edit Profile Button */
.edit-profile-btn {
  background-color: #1877f2;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s;
}

.edit-profile-btn:hover {
  background-color: #145dbf;
}

/* Navigation Tabs */
.profile-nav {
  display: flex;
  justify-content: space-around;
  padding: 10px 0;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  background-color: #f9f9f9;
}

.profile-nav button {
  background: none;
  border: none;
  font-size: 16px;
  font-weight: bold;
  color: #1877f2;
  cursor: pointer;
  padding: 10px 20px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.profile-nav button:hover {
  background-color: #e4e6eb;
}

.active-tab {
  color: #fff;
  background-color: #1877f2;
}

/* Content Sections */
.content-sections {
  padding: 20px;
}

.content-section {
  display: none;
}

.active-section {
  display: block;
}

.post {
  background-color: #f9f9f9;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 15px;
}

.post-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.post-profile {
  display: flex;
  align-items: center;
}

.post-profile-pic {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  margin-right: 10px;
}

.post-user-info h4 {
  margin: 0;
  font-size: 16px;
  font-weight: bold;
}

.post-time {
  font-size: 12px;
  color: #777;
}

/* Post Options Menu */
.post-options {
  position: relative;
}

.three-dots {
  font-size: 24px;
  cursor: pointer;
  transition: transform 0.2s ease-in-out;
}

.three-dots:hover {
  transform: rotate(90deg); /* Add a subtle animation on hover */
}

.post-options-menu {
  display: none;
  position: absolute;
  top: 30px;
  right: 0;
  background-color: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  min-width: 150px;
  z-index: 999;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s ease-in-out, visibility 0s linear 0.2s;
}

.post-options-menu.show {
  display: block;
  opacity: 1;
  visibility: visible;
  transition: opacity 0.2s ease-in-out;
}

.post-options-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.post-options-menu ul li {
  padding: 12px 16px;
  font-size: 14px;
  color: #333;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.post-options-menu ul li:hover {
  background-color: #f5f5f5;
}

.post-options-menu ul li:active {
  background-color: #e6e6e6;
}

/* Hover effect on the dots */
.three-dots:hover + .post-options-menu,
.post-options-menu:hover {
  display: block;
  opacity: 1;
  visibility: visible;
  transition: opacity 0.2s ease-in-out;
}

/* Arrow for the menu */
.post-options-menu::before {
  content: '';
  position: absolute;
  top: -8px;
  right: 12px;
  border-width: 6px;
  border-style: solid;
  border-color: transparent transparent #fff transparent;
}

.post-options-menu ul li {
  font-weight: 500;
}


.post-content {
  margin-top: 10px;
}

.post-content img {
  width: 100%;
  max-height: 400px;
  object-fit: cover;
  margin-top: 10px;
}

/* About Section */
.about-section {
  background-color: #fff;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  max-width: 800px;
  margin: 20px auto;
  font-size: 16px;
}

.about-section h2 {
  font-size: 28px;
  color: #333;
  margin-bottom: 15px;
  font-weight: 600;
  letter-spacing: 1px;
  text-align: center;
}

.about-description {
  font-size: 16px;
  line-height: 1.7;
  color: #555;
  margin-bottom: 30px;
  text-align: center;
}

.about-details {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 30px;
}

.detail {
  display: flex;
  justify-content: space-between;
  font-size: 16px;
  color: #333;
}

.detail strong {
  font-weight: 600;
}

.detail span {
  color: #777;
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 15px;
}

.social-icon {
  font-size: 16px;
  color: #007bff;
  text-decoration: none;
  transition: color 0.3s ease;
}

.social-icon:hover {
  color: #0056b3;
  text-decoration: underline;
}

@media screen and (max-width: 768px) {
  .about-section {
    padding: 30px;
  }

  .about-section h2 {
    font-size: 24px;
  }

  .about-description {
    font-size: 14px;
  }

  .detail {
    font-size: 14px;
  }

  .social-icon {
    font-size: 14px;
  }
}

/* About and Friends Sections */
.about-info h4,
.friends-list {
  margin-top: 20px;
}

.friend {
  display: inline-block;
  margin-right: 15px;
  text-align: center;
}

.friend img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
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