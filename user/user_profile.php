<?php
require 'db_conn.php';
session_start();

// Check if a user ID was provided in the URL
if (!isset($_GET['id'])) {
    echo "<script>
            alert('User ID not provided.');
            window.location.href = 'explore.php';
          </script>";
    exit();
}

$profile_user_id = $_GET['id'];
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if the current user is following this profile
$is_following = false;
if ($current_user_id) {
    $follow_query = "SELECT * FROM user_follows WHERE follower_id = ? AND following_id = ?";
    $stmt = $conn->prepare($follow_query);
    $stmt->bind_param("ii", $current_user_id, $profile_user_id);
    $stmt->execute();
    $follow_result = $stmt->get_result();
    $is_following = $follow_result->num_rows > 0;
}

// Fetch user information including follower counts
$query = "SELECT full_name, profile_picture, username, about, location, birthday, website, skills, isPremium, profile_background, followers_count, following_count 
          FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$result = $stmt->get_result();

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
    $followers_count = $user['followers_count'];
    $following_count = $user['following_count'];
} else {
    echo "<script>
            alert('User not found.');
            window.location.href = 'explore.php';
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
    <title><?php echo $full_name; ?> - Kulturabase</title>
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
    <!-- Profile Header -->
    <div class="profile-header <?php echo $premium_class; ?>" 
         style="margin-top: 100px; <?php echo ($is_premium && $profile_background) ? "background-image: url('" . $profile_background . "'); background-size: cover; background-position: center;" : ''; ?>">
        <div class="profile-overlay"></div>
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
                    <span class="stat-item">
                        <strong><?php echo $followers_count; ?></strong> Followers
                    </span>
                    <span class="stat-item">
                        <strong><?php echo $following_count; ?></strong> Following
                    </span>
                </div>
                <?php if ($current_user_id && $current_user_id != $profile_user_id): ?>
                    <button id="followBtn" class="follow-btn <?php echo $is_following ? 'following' : ''; ?>" 
                            data-user-id="<?php echo $profile_user_id; ?>"
                            data-following="<?php echo $is_following ? 'true' : 'false'; ?>">
                        <?php echo $is_following ? 'Following' : 'Follow'; ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="profile-nav">
      <button id="posts-tab" class="active-tab">Posts</button>
      <button id="about-tab">About</button>
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
                  <strong>Hobbies:</strong> <span><?php echo $skills ? $skills : "No hobbies listed."; ?></span>
              </div>
          </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
        function loadUserPosts() {
            $.ajax({
                url: 'posts_management.php',
                method: 'POST',
                data: { 
                    action: 'fetch_user_posts',
                    user_id: <?php echo $profile_user_id; ?>
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response);
                    
                    if (response.error) {
                        console.error('Server error:', response.error);
                        $('#posts-container').html(`<p class="error-message">Error: ${response.error}</p>`);
                        return;
                    }

                    const postsContainer = $('#posts-container');
                    postsContainer.empty();

                    if (!Array.isArray(response.posts) || response.posts.length === 0) {
                        postsContainer.append('<p>No posts yet.</p>');
                        return;
                    }

                    response.posts.forEach(function(post) {
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

        function getFilePreviewHtml(post) {
            if (!post.file_path) return '';

            const fileExtension = post.file_path.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                return `<img src="${post.file_path}" alt="Post Image" class="post-media">`;
            }
          
            if (['mp4', 'webm', 'mov'].includes(fileExtension)) {
                return `<video controls class="post-media">
                    <source src="${post.file_path}" type="video/${fileExtension}">
                    Your browser does not support the video tag.
                </video>`;
            }
            
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

            return `<div class="document-preview">
                <i class="fas fa-file"></i>
                <a href="${post.file_path}" target="_blank" class="document-link">View File</a>
            </div>`;
        }

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

        loadUserPosts();

        $('#posts-tab').click(function() {
            $('#posts-tab').addClass('active-tab');
            $('#about-tab').removeClass('active-tab');
            $('#posts-section').addClass('active-section');
            $('#about-section').removeClass('active-section');
        });

        $('#about-tab').click(function() {
            $('#about-tab').addClass('active-tab');
            $('#posts-tab').removeClass('active-tab');
            $('#about-section').addClass('active-section');
            $('#posts-section').removeClass('active-section');
        });

        // Follow button click handler
        $('.follow-btn').on('click', function() {
            const userId = $(this).data('user-id');
            const isFollowing = $(this).hasClass('following');
            const action = isFollowing ? 'unfollow' : 'follow';
            const $button = $(this);
            const $followerCount = $('.follower-count');

            $.ajax({
                url: 'follow_management.php',
                type: 'POST',
                data: {
                    user_id: userId,
                    action: action
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if (action === 'follow') {
                            $button.addClass('following').text('Following');
                            $followerCount.text(parseInt($followerCount.text()) + 1);
                        } else {
                            $button.removeClass('following').text('Follow');
                            $followerCount.text(Math.max(0, parseInt($followerCount.text()) - 1));
                        }
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Hover effect for following button
        $('.follow-btn.following').hover(
            function() {
                $(this).text('Unfollow');
            },
            function() {
                $(this).text('Following');
            }
        );
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
  display: flex;
  gap: 20px;
  margin: 15px 0;
}

.stat-item {
  color: #4a4a4a;
  font-size: 14px;
}

.stat-item strong {
  color: #000;
  font-weight: 600;
}

.follow-btn {
  background-color: #1877f2;
  color: white;
  border: none;
  padding: 8px 24px;
  border-radius: 20px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.follow-btn:hover {
  background-color: #166fe5;
}

.follow-btn.following {
  background-color: #e4e6eb;
  color: #1c1e21;
}

.follow-btn.following:hover {
  background-color: #d8dadf;
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
    background: rgba(255, 255, 255, 0.3);
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
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

/* Adjust text colors for better contrast */
.premium-user .user-info h2 {
    color: #fff;
}

.premium-user .user-info .username {
    color: #f0f0f0;
}

/* Post media styles */
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