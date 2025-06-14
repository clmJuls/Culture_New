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

// if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
//     echo "<script>
//             alert('Access denied. Admins only.');
//             window.location.href = 'home.php'; // Redirect to the homepage or another appropriate page
//           </script>";
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cultural Database</title>
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
        
        .explore-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Stats Cards */
        .stats-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 30px;
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            flex: 1;
            min-width: 250px;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #365486;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 16px;
        }
        
        /* Rankings */
        .rankings-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .ranking-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 25px;
            height: 100%;
        }

        .ranking-title {
            color: #365486;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ranking-table th,
        .ranking-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .ranking-table th {
            background-color: #f8f9fa;
            color: #365486;
            font-weight: bold;
        }

        .ranking-table tr:hover {
            background-color: #f5f5f5;
        }

        .rank {
            font-weight: bold;
            color: #365486;
            width: 60px;
            text-align: center;
        }

        .usage-bar {
            background: #e9ecef;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .usage-fill {
            background: linear-gradient(90deg, #365486, #7FC7D9);
            height: 100%;
            transition: width 0.5s ease;
        }

        .percentage {
            color: #666;
            font-size: 0.9em;
            display: block;
            text-align: right;
        }
        
        /* Charts */
        .charts-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 25px;
            flex: 1;
            min-width: 500px;
            margin-bottom: 25px;
        }
        
        canvas {
            max-width: 100%;
        }
        
        @media (max-width: 1024px) {
            .rankings-container {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                min-width: 100%;
            }
        }
    </style>
    
    <!-- Navigation Bar -->
    <?php include 'components/layout/admin/navbar.php'; ?>
    <?php

        // Get posts by month for trend data
        $posts_by_month_query = "SELECT 
                                DATE_FORMAT(created_at, '%Y-%m') as month,
                                COUNT(*) as count
                            FROM posts
                            GROUP BY month
                            ORDER BY month ASC";

        $posts_by_month_result = $conn->query($posts_by_month_query);
        $month_labels = [];
        $month_data = [];

        while ($row = $posts_by_month_result->fetch_assoc()) {
            $month_labels[] = $row['month'];
            $month_data[] = $row['count'];
        }

        // Get most liked posts
        $popular_posts_query = "SELECT p.title, COUNT(l.id) as like_count
                            FROM posts p
                            JOIN likes l ON p.id = l.post_id AND l.is_active = 1
                            GROUP BY p.id
                            ORDER BY like_count DESC
                            LIMIT 10";

        $popular_posts_result = $conn->query($popular_posts_query);
        $post_titles = [];
        $post_likes = [];

        while ($row = $popular_posts_result->fetch_assoc()) {
            $post_titles[] = $row['title'];
            $post_likes[] = $row['like_count'];
        }

        // Get learning styles data
        $learning_styles_query = "SELECT 
                                learning_styles as style,
                                COUNT(*) as count
                            FROM posts
                            WHERE status = 'approved'
                            GROUP BY style
                            ORDER BY count DESC";
                            
        $learning_styles_result = $conn->query($learning_styles_query);
        $style_labels = [];
        $style_data = [];
        $style_names = [];
        $style_counts = [];

        while ($row = $learning_styles_result->fetch_assoc()) {
            $style_labels[] = $row['style'];
            $style_data[] = $row['count'];
            $style_names[] = $row['style'];
            $style_counts[] = $row['count'];
        }

        // Get total posts
        $total_posts = $conn->query("SELECT COUNT(*) as total FROM posts")->fetch_assoc()['total'];

        // Get most liked post
        $most_liked_post = $conn->query("SELECT p.title, COUNT(l.id) as like_count 
                                        FROM posts p 
                                        JOIN likes l ON p.id = l.post_id AND l.is_active = 1 
                                        GROUP BY p.id 
                                        ORDER BY like_count DESC 
                                        LIMIT 1")->fetch_assoc();

        // Get most active user with approved posts only
        $most_active_user = $conn->query("SELECT u.username, COUNT(p.id) as post_count 
                                        FROM users u 
                                        JOIN posts p ON u.id = p.user_id 
                                        WHERE p.status = 'approved'
                                        GROUP BY u.id 
                                        ORDER BY post_count DESC 
                                        LIMIT 1")->fetch_assoc();
    ?>

    <div class="explore-container">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_posts; ?></div>
                <div class="stat-label">Total Posts</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $most_liked_post ? htmlspecialchars($most_liked_post['title']) : 'N/A'; ?></div>
                <div class="stat-label">Most Liked Post</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $most_active_user ? htmlspecialchars($most_active_user['username']) : 'N/A'; ?></div>
                <div class="stat-label">Most Active User</div>
            </div>
        </div>

        <div class="rankings-container">
            <!-- Popular Posts Rankings -->
            <div class="ranking-box">
                <h2 class="ranking-title">Most Popular Posts</h2>
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Post Title</th>
                            <th>Likes</th>
                            <th>Distribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_likes = array_sum($post_likes);
                        $rank = 1;
                        foreach($post_likes as $index => $count) {
                            if (isset($post_titles[$index])) {
                                $title = htmlspecialchars($post_titles[$index]);
                                $percentage = $total_likes > 0 ? round(($count / $total_likes) * 100, 1) : 0;
                                echo "<tr>
                                        <td class='rank'>#{$rank}</td>
                                        <td>{$title}</td>
                                        <td>{$count} likes</td>
                                        <td>
                                            <div class='usage-bar'>
                                                <div class='usage-fill' style='width: {$percentage}%'></div>
                                            </div>
                                            <span class='percentage'>{$percentage}%</span>
                                        </td>
                                      </tr>";
                                $rank++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- User Activity Rankings -->
            <div class="ranking-box">
                <h2 class="ranking-title">Most Active Users</h2>
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Username</th>
                            <th>Posts</th>
                            <th>Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $active_users_query = "SELECT u.username, COUNT(p.id) as post_count 
                                            FROM users u 
                                            JOIN posts p ON u.id = p.user_id 
                                            WHERE p.status = 'approved'
                                            GROUP BY u.id 
                                            ORDER BY post_count DESC 
                                            LIMIT 10";
                        $active_users_result = $conn->query($active_users_query);
                        $total_user_posts = 0;
                        $usernames = [];
                        $post_counts = [];
                        
                        while ($row = $active_users_result->fetch_assoc()) {
                            $usernames[] = $row['username'];
                            $post_counts[] = $row['post_count'];
                            $total_user_posts += $row['post_count'];
                        }
                        
                        $rank = 1;
                        foreach($post_counts as $index => $count) {
                            if (isset($usernames[$index])) {
                                $username = htmlspecialchars($usernames[$index]);
                                $percentage = $total_user_posts > 0 ? round(($count / $total_user_posts) * 100, 1) : 0;
                                echo "<tr>
                                        <td class='rank'>#{$rank}</td>
                                        <td>{$username}</td>
                                        <td>{$count} posts</td>
                                        <td>
                                            <div class='usage-bar'>
                                                <div class='usage-fill' style='width: {$percentage}%'></div>
                                            </div>
                                            <span class='percentage'>{$percentage}%</span>
                                        </td>
                                      </tr>";
                                $rank++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Learning Styles Rankings -->
            <div class="ranking-box">
                <h2 class="ranking-title">Learning Styles Distribution</h2>
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Learning Style</th>
                            <th>Posts</th>
                            <th>Distribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_style_posts = array_sum($style_counts);
                        $rank = 1;
                        foreach($style_counts as $index => $count) {
                            if (isset($style_names[$index])) {
                                $style = htmlspecialchars($style_names[$index]);
                                $percentage = $total_style_posts > 0 ? round(($count / $total_style_posts) * 100, 1) : 0;
                                echo "<tr>
                                        <td class='rank'>#{$rank}</td>
                                        <td>{$style}</td>
                                        <td>{$count} posts</td>
                                        <td>
                                            <div class='usage-bar'>
                                                <div class='usage-fill' style='width: {$percentage}%'></div>
                                            </div>
                                            <span class='percentage'>{$percentage}%</span>
                                        </td>
                                      </tr>";
                                $rank++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="charts-wrapper">
            <div class="chart-container">
                <canvas id="postsTimelineChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="popularPostsChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="learningStylesChart"></canvas>
            </div>
        </div>
    </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
        // Posts Timeline Chart
        new Chart(document.getElementById('postsTimelineChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($month_labels); ?>,
                datasets: [{
                    label: 'Posts by Month',
                    data: <?php echo json_encode($month_data); ?>,
                    backgroundColor: 'rgba(54, 84, 134, 0.2)',
                    borderColor: '#365486',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Posts Timeline',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });

        // Popular Posts Chart
        new Chart(document.getElementById('popularPostsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($post_titles); ?>,
                datasets: [{
                    label: 'Likes per Post',
                    data: <?php echo json_encode($post_likes); ?>,
                    backgroundColor: '#365486',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    title: {
                        display: true,
                        text: 'Most Popular Posts',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // Learning Styles Chart
        new Chart(document.getElementById('learningStylesChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($style_labels); ?>,
                datasets: [{
                    label: 'Learning Styles',
                    data: <?php echo json_encode($style_data); ?>,
                    backgroundColor: [
                        '#365486',
                        '#7FC7D9',
                        '#DCF2F1',
                        '#0F1035',
                        '#5D8AA8',
                        '#9BC4E2'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Learning Styles Distribution',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
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

<!-- Include Chat Widget -->
<?php include 'components/widgets/chat.php'; ?>

</body>
</head>
</html>