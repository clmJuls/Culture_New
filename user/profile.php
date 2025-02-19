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
$first_initial = strtoupper(substr($name_parts[0], 0, 1)); // First letter of first name
$last_name = isset($name_parts[1]) ? strtoupper(substr($name_parts[1], 0, 1)) : ''; // First letter of last name (if exists)
$avatar_text = $first_initial . $last_name;
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
    <div class="profile-header" style="margin-top: 100px;">
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