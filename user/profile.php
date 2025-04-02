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

// Fetch user information from the database
$query = "SELECT full_name, profile_picture, username, about, location, birthday, website, skills, isPremium, profile_background FROM users WHERE id = '$user_id'";
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
    $profile_picture = $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : 'assets/hero/v07_20@Shanks.png';
    $is_premium = $user['isPremium'];
    $profile_background = $user['profile_background'] ? htmlspecialchars($user['profile_background']) : '';
} else {
    echo "<script>
            alert('User not found.');
            window.location.href = 'login.php';
          </script>";
    exit();
}

$name_parts = explode(' ', $full_name);
$first_initial = strtoupper(substr($name_parts[0], 0, 1)); // First letter of first name
$last_name = isset($name_parts[1]) ? strtoupper(substr($name_parts[1], 0, 1)) : ''; // First letter of last name (if exists)
$avatar_text = $first_initial . $last_name;

// Add a class based on premium status
$premium_class = $is_premium ? 'premium-user' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <?php if ($is_premium): ?>
    <link rel="stylesheet" href="assets/css/premium-styles.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
        include 'components/layout/admin/navbar.php';
    } else {
        include 'components/layout/guest/navbar.php';
    }
    ?>

<div class="profile-page">
    <!-- Cover Photo -->
    <!-- <div class="cover-photo">
      <img src="https://via.placeholder.com/800x300" alt="Cover Photo">
    </div> -->

    <!-- Profile Header -->
    <div class="profile-header <?php echo $premium_class; ?>" 
         style="margin-top: 100px; <?php echo ($is_premium && $profile_background) ? "background-image: url('" . $profile_background . "'); background-size: cover; background-position: center;" : ''; ?>">
        <div class="profile-overlay"></div>
        <?php if ($is_premium): ?>
        <button class="change-background-btn" id="changeBackgroundBtn" title="Change Background">
            <i class="fas fa-image"></i>
        </button>
        <?php endif; ?>
        <div class="profile-content">
            <div class="profile-picture">
                <div class="profile-img">
                    <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" id="profile-img" class="profile-img-preview">
                </div>
            </div>
            <div class="user-info">
                <h2><?php echo $full_name; ?></h2>
                <p class="username">@<?php echo $username; ?></p>
                <div class="user-stats">
                    <!-- Stats content -->
                </div>
                <a href="edit-profile.php" class="edit-profile-link">
                    <button class="edit-profile-btn">Edit Profile</button>
                </a>
            </div>
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
              <!-- <div class="detail" style="margin-bottom: 10px;">
                  <strong>Website:</strong> <span>
                      <?php 
                          echo $website ? "<a href='$website' target='_blank'>$website</a>" : "Not provided.";
                      ?>
                  </span>
              </div> -->
              <div class="detail" style="margin-bottom: 10px;">
                  <strong>Hobbies:</strong> <span><?php echo $skills ? $skills : "No hobbies listed."; ?></span>
              </div>
          </div>
      </div>
</div>

      <!-- Friends Section -->
      <div id="friends-section" class="content-section">
        <h3>Friends</h3>
        <div class="friends-list">
          <!-- Remove placeholder images or update with actual image paths -->
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        function loadPosts() {
            $.ajax({
                url: 'posts.php',
                method: 'GET',
                data: { action: 'fetch' },
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response); // Debug log
                    
                    if (response.error) {
                        console.error('Server error:', response.error);
                        $('#posts-container').html(`<p class="error-message">Error: ${response.error}</p>`);
                        return;
                    }

                    const postsContainer = $('#posts-container');
                    postsContainer.empty();

                    if (!Array.isArray(response) || response.length === 0) {
                        postsContainer.append('<p>No posts yet.</p>');
                        return;
                    }

                    response.forEach(function(post) {
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
                                    ${getFilePreviewHtml(post)}
                                    <div class="post-stats">
                                        <span>${post.like_count} Likes</span>
                                        <span>${post.comment_count} Comments</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        postsContainer.append(postHtml);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    console.error('Response text:', xhr.responseText);
                    $('#posts-container').html(`
                        <p class="error-message">
                            Error loading posts. Please try again later.
                        </p>
                    `);
                }
            });
        }

        // Helper function to determine file type and return appropriate HTML
        function getFilePreviewHtml(post) {
            if (!post.file_path) return '';

            // Try to determine file type from file extension if mime type is not available
            const fileExtension = post.file_path.split('.').pop().toLowerCase();
            
            // Image extensions
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                return `<img src="${post.file_path}" alt="Post Image" class="post-media">`;
            }
            
            // Video extensions
            if (['mp4', 'webm', 'mov'].includes(fileExtension)) {
                return `<video controls class="post-media">
                    <source src="${post.file_path}" type="video/${fileExtension}">
                    Your browser does not support the video tag.
                </video>`;
            }
            
            // Document extensions
            const documentExtensions = {
                'pdf': 'fas fa-file-pdf',
                'doc': 'fas fa-file-word',
                'docx': 'fas fa-file-word',
                'xls': 'fas fa-file-excel',
                'xlsx': 'fas fa-file-excel',
                'txt': 'fas fa-file-alt'
            };

            if (documentExtensions[fileExtension]) {
                return `<div class="document-preview">
                    <i class="${documentExtensions[fileExtension]}"></i>
                    <a href="${post.file_path}" target="_blank" class="document-link">View Document</a>
                </div>`;
            }

            // Default case: just show a link to the file
            return `<div class="document-preview">
                <i class="fas fa-file"></i>
                <a href="${post.file_path}" target="_blank" class="document-link">View File</a>
            </div>`;
        }

        // Add some CSS for error messages
        $('<style>')
            .text(`
                .error-message {
                    color: #dc3545;
                    padding: 15px;
                    background-color: #f8d7da;
                    border-radius: 4px;
                    margin: 10px 0;
                    text-align: center;
                }
            `)
            .appendTo('head');

        // Initial load
        loadPosts();
    });
    </script>
  <script>
    // JavaScript for Tab Navigation
    const postsTab = document.getElementById('posts-tab');
    const aboutTab = document.getElementById('about-tab');
    // const friendsTab = document.getElementById('friends-tab'); // Comment out or remove
    const postsSection = document.getElementById('posts-section');
    const aboutSection = document.getElementById('about-section');
    // const friendsSection = document.getElementById('friends-section'); // Comment out or remove

    postsTab.addEventListener('click', () => {
      postsTab.classList.add('active-tab');
      aboutTab.classList.remove('active-tab');
      // friendsTab.classList.remove('active-tab'); // Remove this line
      postsSection.classList.add('active-section');
      aboutSection.classList.remove('active-section');
      // friendsSection.classList.remove('active-section'); // Remove this line
    });

    aboutTab.addEventListener('click', () => {
      aboutTab.classList.add('active-tab');
      postsTab.classList.remove('active-tab');
      // friendsTab.classList.remove('active-tab'); // Remove this line
      aboutSection.classList.add('active-section');
      postsSection.classList.remove('active-section');
      // friendsSection.classList.remove('active-section'); // Remove this line
    });

    // Remove the entire friendsTab click event listener since the element doesn't exist
    /* 
    friendsTab.addEventListener('click', () => {
      // ... removed code ...
    });
    */
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
  max-width: 600px;
  margin: 100px auto 0;
}

.profile-picture {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.profile-img {
  width: 150px;
  height: 150px;
}

.profile-img-preview {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  padding: 15px 0;
  background: none;
  border: none;
  cursor: default;
}

.user-info:hover {
  background: none;
  border: none;
}

.user-info h2 {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  margin-bottom: 5px;
}

.user-info .username {
  font-size: 16px;
  color: #666;
  margin-bottom: 15px;
}

.user-stats {
  margin: 10px 0;
}

.edit-profile-link {
  text-decoration: none;
}

.edit-profile-btn {
  background-color: transparent;
  color: #1a1a1a;
  border: 1px solid #dadde1;
  padding: 8px 20px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
}

/* Navigation Tabs */
.profile-nav {
  display: flex;
  justify-content: center;
  gap: 20px;
  padding: 15px 0;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  background-color: #fff;
  margin-top: 20px;
}

.profile-nav button {
  background: none;
  border: none;
  font-size: 16px;
  font-weight: 500;
  color: #666;
  cursor: pointer;
  padding: 8px 24px;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.profile-nav button:hover {
  background-color: #f0f2f5;
  color: #1877f2;
}

.profile-nav button.active-tab {
  color: #1877f2;
  background-color: #e7f3ff;
  font-weight: 600;
}

/* Content Sections */
.content-sections {
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
}

.content-section {
  display: none;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.content-section.active-section {
  display: block;
  opacity: 1;
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
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

/* Premium User Styling with Flowery Design */
.premium-user {
    border: 2px solid gold;
    position: relative;
    overflow: visible;
    background-color: transparent;
}

.premium-user::before,
.premium-user::after {
    content: '';
    position: absolute;
    width: 60px;
    height: 60px;
    background: 
        radial-gradient(circle at 30% 30%, gold 2px, transparent 4px) 0 0,
        radial-gradient(circle at 70% 30%, gold 2px, transparent 4px) 0 0,
        radial-gradient(circle at 30% 70%, gold 2px, transparent 4px) 0 0,
        radial-gradient(circle at 70% 70%, gold 2px, transparent 4px) 0 0;
    background-size: 30px 30px;
    z-index: 1;
}

.premium-user::before {
    top: -20px;
    left: -20px;
    transform: rotate(-45deg);
}

.premium-user::after {
    bottom: -20px;
    right: -20px;
    transform: rotate(135deg);
}

.premium-user .user-info::before {
    content: '⭐ Premium Member ⭐';
    display: block;
    color: gold;
    font-size: 14px;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    margin-bottom: 10px;
}

/* Add subtle animation */
@keyframes floralSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.premium-user::before,
.premium-user::after {
    animation: floralSpin 20s linear infinite;
}

/* Adjust the profile-overlay opacity */
.profile-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.3); /* Changed from 0.85 to 0.3 for more visibility */
    z-index: 1;
}

/* Add text shadow to ensure readability */
.profile-content {
    position: relative;
    z-index: 2;
}

.profile-content h2,
.profile-content .username,
.profile-content .user-info::before {
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5); /* Add shadow to make text readable */
}

/* Adjust text colors for better contrast */
.premium-user .user-info h2 {
    color: #fff;
}

.premium-user .user-info .username {
    color: #f0f0f0;
}

/* Add these new styles */
.change-background-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.7);  /* White with 70% opacity */
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: all 0.3s ease;
}

.change-background-btn:hover {
    background: rgba(255, 255, 255, 0.9);  /* Increase opacity on hover */
}

.change-background-btn i {
    font-size: 18px;
    color: rgba(0, 0, 0, 0.7);  /* Slightly transparent black for the icon */
}

/* Remove the premium-specific gradient styles */
.premium-user .change-background-btn {
    background: rgba(255, 255, 255, 0.7);
}

.premium-user .change-background-btn:hover {
    background: rgba(255, 255, 255, 0.9);
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 30px;
    border: none;
    border-radius: 15px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
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

#backgroundForm {
    margin-top: 20px;
}

/* Add these styles */
.upload-info {
    background-color: #F7F9FC;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 25px;
    border: 1px dashed #CBD5E0;
}

.upload-info p {
    margin: 8px 0;
    color: #4A5568;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.upload-info p::before {
    content: '•';
    color: #4299E1;
}

.file-upload-wrapper {
    position: relative;
    margin-bottom: 20px;
    text-align: center;
}

.file-upload-input {
    position: absolute;
    left: -9999px;
}

.file-upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background-color: #F7FAFC;
    border: 2px dashed #CBD5E0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload-label:hover {
    background-color: #EDF2F7;
    border-color: #4299E1;
}

.upload-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.upload-text {
    color: #4A5568;
    font-size: 16px;
    font-weight: 500;
}

.selected-file-name {
    display: block;
    margin-top: 10px;
    color: #2D3748;
    font-size: 14px;
    font-weight: 500;
}

.upload-submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #4299E1 0%, #667EEA 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    gap: 8px;
}

.upload-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
}

.upload-submit-btn:active {
    transform: translateY(0);
}

.btn-icon {
    font-size: 20px;
}

.btn-text {
    margin-left: 8px;
}

/* Loading state */
.upload-submit-btn.loading {
    background: #A0AEC0;
    cursor: not-allowed;
}

@media (max-width: 480px) {
    .modal-content {
        margin: 10% auto;
        padding: 20px;
    }

    .upload-text {
        font-size: 14px;
    }
}

/* Add these styles to your existing CSS */
.post-media {
    max-width: 100%;
    border-radius: 8px;
    margin: 10px 0;
}

.document-preview {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    margin: 10px 0;
}

.document-preview i {
    font-size: 24px;
    color: #365486;
}

.document-link {
    color: #365486;
    text-decoration: none;
    font-weight: 500;
}

.document-link:hover {
    text-decoration: underline;
}
  </style>

<!-- Sidebar -->
<?php include 'components/layout/guest/sidebar.php'; ?>
<?php include 'components/widgets/chat.php'; ?>

</body>
</head>
</html>

<?php 
// Move database connection close to the end of the file
$conn->close(); 
?>

<?php if ($is_premium): ?>
<div id="backgroundModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Change Profile Background</h2>
        <form id="backgroundForm" enctype="multipart/form-data">
            <div class="upload-info">
                <p>Accepted file types: JPG, PNG, GIF</p>
                <p>Maximum file size: 10MB</p>
            </div>
            <div class="file-upload-wrapper">
                <input type="file" name="background" id="file-upload" class="file-upload-input" accept="image/jpeg,image/png,image/gif" required>
                <label for="file-upload" class="file-upload-label">
                    <span class="upload-icon">📷</span>
                    <span class="upload-text">Choose your artistic background</span>
                </label>
                <span class="selected-file-name"></span>
            </div>
            <button type="submit" class="upload-submit-btn">
                <span class="btn-text">Submit</span>
            </button>
        </form>
    </div>
</div>

<script>
// Define the function globally
function openBackgroundModal() {
    const modal = document.getElementById('backgroundModal');
    if (modal) {
        modal.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Add click event listener to the button
    const changeBackgroundBtn = document.getElementById('changeBackgroundBtn');
    if (changeBackgroundBtn) {
        changeBackgroundBtn.addEventListener('click', openBackgroundModal);
    }

    // Close modal when clicking the X
    const closeBtn = document.querySelector('.close');
    if (closeBtn) {
        closeBtn.onclick = function() {
            const modal = document.getElementById('backgroundModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('backgroundModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Handle file selection display
    const fileInput = document.getElementById('file-upload');
    const fileNameDisplay = document.querySelector('.selected-file-name');
    const uploadLabel = document.querySelector('.upload-text');
    const originalLabelText = uploadLabel.textContent;

    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            fileNameDisplay.textContent = fileName;
            uploadLabel.textContent = 'Change selection';
        } else {
            fileNameDisplay.textContent = '';
            uploadLabel.textContent = originalLabelText;
        }
    });

    // Handle form submission
    const backgroundForm = document.getElementById('backgroundForm');
    if (backgroundForm) {
        backgroundForm.onsubmit = function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('.upload-submit-btn');
            const originalText = submitButton.querySelector('.btn-text').textContent;
            
            // Show loading state
            submitButton.classList.add('loading');
            submitButton.querySelector('.btn-text').textContent = 'Uploading...';
            submitButton.disabled = true;

            fetch('update_profile_background.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert('Background updated successfully!');
                        location.reload();
                    } else {
                        throw new Error(data.error || 'Unknown error occurred');
                    }
                } catch (e) {
                    throw new Error('Invalid JSON response from server');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                submitButton.classList.remove('loading');
                submitButton.querySelector('.btn-text').textContent = originalText;
                submitButton.disabled = false;
            });
        };
    }
});
</script>
<?php endif; ?>