<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
            window.location.href = '../user/auth/login.php';
          </script>";
    exit();
}
$user_id = $_SESSION['user_id'];

$isAdmin = $_SESSION['isAdmin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

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
        .notification-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .notification-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background-color: #f5f5f5;
        }

        .notification-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #365486;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-weight: bold;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: bold;
            color: #365486;
            margin-bottom: 5px;
        }

        .notification-time {
            font-size: 0.8em;
            color: #666;
        }

        .no-notifications {
            text-align: center;
            padding: 20px;
            color: #666;
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
    
<div class="notification-container">
    <?php
    // Check if the user is an admin or not and adjust the query accordingly
    if ($isAdmin) {
        // Admin: Fetch all posts, comments, and likes
        $query = "SELECT 
                    p.title AS post_title,
                    p.created_at AS post_created_at,
                    u.username AS post_creator,
                    u.profile_picture AS user_avatar,
                    c.comment_text,
                    c.created_at AS comment_created_at,
                    c.user_id AS comment_user_id,
                    cu.username AS comment_user,
                    l.created_at AS like_created_at,
                    l.user_id AS like_user_id,
                    lu.username AS like_user
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN comments c ON p.id = c.post_id
                LEFT JOIN users cu ON c.user_id = cu.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN users lu ON l.user_id = lu.id
                ORDER BY GREATEST(
                    IFNULL(p.created_at, 0),
                    IFNULL(c.created_at, 0),
                    IFNULL(l.created_at, 0)
                ) DESC
                LIMIT 20";
        $stmt = $conn->prepare($query);
    } else {
        // Non-admin: Fetch posts, comments, and likes by the current user
        $query = "SELECT 
                    p.title AS post_title,
                    p.created_at AS post_created_at,
                    u.username AS post_creator,
                    u.profile_picture AS user_avatar,
                    c.comment_text,
                    c.created_at AS comment_created_at,
                    c.user_id AS comment_user_id,
                    cu.username AS comment_user,
                    l.created_at AS like_created_at,
                    l.user_id AS like_user_id,
                    lu.username AS like_user
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN comments c ON p.id = c.post_id
                LEFT JOIN users cu ON c.user_id = cu.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN users lu ON l.user_id = lu.id
                WHERE p.user_id = ?
                ORDER BY GREATEST(
                    IFNULL(p.created_at, 0),
                    IFNULL(c.created_at, 0),
                    IFNULL(l.created_at, 0)
                ) DESC
                LIMIT 20";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $postTimeAgo = getTimeAgo(strtotime($row['post_created_at']));
            $commentTimeAgo = isset($row['comment_created_at']) ? getTimeAgo(strtotime($row['comment_created_at'])) : null;
            $likeTimeAgo = isset($row['like_created_at']) ? getTimeAgo(strtotime($row['like_created_at'])) : null;

            echo '<div class="notification-item">
                    <div class="notification-avatar">';
            if (!empty($row['user_avatar'])) {
                echo '<img src="' . htmlspecialchars($row['user_avatar']) . '" alt="User avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">';
            } else {
                echo htmlspecialchars($row['avatar_letter']);
            }
            echo '</div>
                    <div class="notification-content">';

            // Post notification
            echo '<div class="notification-title">
                    ' . htmlspecialchars($row['post_creator']) . ' created a new post
                  </div>
                  <div>' . htmlspecialchars($row['post_title']) . '</div>
                  <div class="notification-time">' . $postTimeAgo . '</div>';

            // Comment notification
            if (!empty($row['comment_text'])) {
                echo '<div class="notification-comment">
                        <strong>' . htmlspecialchars($row['comment_user']) . '</strong> commented: "' . htmlspecialchars($row['comment_text']) . '"
                      </div>
                      <div class="notification-time">' . $commentTimeAgo . '</div>';
            }

            // Like notification
            if (!empty($row['like_user'])) {
                echo '<div class="notification-like">
                        <strong>' . htmlspecialchars($row['like_user']) . '</strong> liked this post
                      </div>
                      <div class="notification-time">' . $likeTimeAgo . '</div>';
            }

            echo '</div></div>';
        }
    } else {
        echo '<div class="no-notifications">No recent activity</div>';
    }

    $stmt->close();

    // Helper function to convert timestamp to "time ago" format
    function getTimeAgo($timestamp) {
        $time_difference = time() - $timestamp;

        if ($time_difference < 60) {
            return "Just now";
        } elseif ($time_difference < 3600) {
            $minutes = round($time_difference / 60);
            return $minutes . " minute" . ($minutes != 1 ? "s" : "") . " ago";
        } elseif ($time_difference < 86400) {
            $hours = round($time_difference / 3600);
            return $hours . " hour" . ($hours != 1 ? "s" : "") . " ago";
        } elseif ($time_difference < 604800) {
            $days = round($time_difference / 86400);
            return $days . " day" . ($days != 1 ? "s" : "") . " ago";
        } else {
            return date("M j, Y", $timestamp);
        }
    }
    ?>
</div>



  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
        setInterval(function() {
            location.reload();
        }, 60000);
    </script>
  <style>
    /* Post container */
.post {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin: 20px 0;
    padding: 15px;
    /* max-width: 600px; */
    width: 100%;
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
    font-size: 18px;
    cursor: pointer;
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
    max-width: 100%;
    border-radius: 8px;
    margin-top: 10px;
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

   .explore-container {
      max-width: 1000px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .post-container {
      border: 1px solid #ccc;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
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
  </style>

<!-- Sidebar -->
<?php include 'components/layout/guest/sidebar.php'; ?>
<?php include 'components/widgets/chat.php'; ?>

</body>
</head>
</html>