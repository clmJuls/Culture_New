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

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    echo "<script>
            alert('Access denied. Admins only.');
            window.location.href = 'home.php'; // Redirect to the homepage or another appropriate page
          </script>";
    exit();
}
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
        .rankings-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 20px;
        }

        .ranking-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .ranking-title {
            color: #365486;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ranking-table th,
        .ranking-table td {
            padding: 12px;
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
            width: 50px;
            text-align: center;
        }

        .usage-bar {
            background: #e9ecef;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .usage-fill {
            background: #365486;
            height: 100%;
            transition: width 0.3s ease;
        }

        .percentage {
            color: #666;
            font-size: 0.9em;
        }
        .chart-container {
            width: 45%;
            margin: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: inline-block;
            vertical-align: top;
        }
        .stats-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 20px;
            gap: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            flex: 1;
            min-width: 200px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #365486;
        }
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
    </style>
    
    <!-- Navigation Bar -->
    <?php include 'components/layout/admin/navbar.php'; ?>
    <?php

        $culture_query = "SELECT 
                            SUBSTRING_INDEX(SUBSTRING_INDEX(culture_elements, ',', n.n), ',', -1) as element,
                            COUNT(*) as count
                        FROM posts
                        JOIN (
                            SELECT 1 + units.i + tens.i * 10 n
                            FROM (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) units
                            CROSS JOIN (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
                        ) n
                        WHERE n.n <= 1 + (LENGTH(culture_elements) - LENGTH(REPLACE(culture_elements, ',', '')))
                        GROUP BY element";

        $culture_result = $conn->query($culture_query);
        $culture_labels = [];
        $culture_data = [];

        while ($row = $culture_result->fetch_assoc()) {
            if (!empty($row['element'])) {
                $culture_labels[] = $row['element'];
                $culture_data[] = $row['count'];
            }
        }

        // Get learning styles data
        $styles_query = "SELECT 
                            SUBSTRING_INDEX(SUBSTRING_INDEX(learning_styles, ',', n.n), ',', -1) as style,
                            COUNT(*) as count
                        FROM posts
                        JOIN (
                            SELECT 1 + units.i + tens.i * 10 n
                            FROM (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) units
                            CROSS JOIN (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
                        ) n
                        WHERE n.n <= 1 + (LENGTH(learning_styles) - LENGTH(REPLACE(learning_styles, ',', '')))
                        GROUP BY style";

        $styles_result = $conn->query($styles_query);
        $styles_labels = [];
        $styles_data = [];

        while ($row = $styles_result->fetch_assoc()) {
            if (!empty($row['style'])) {
                $styles_labels[] = $row['style'];
                $styles_data[] = $row['count'];
            }
        }

        // Get total posts
        $total_posts = $conn->query("SELECT COUNT(*) as total FROM posts")->fetch_assoc()['total'];

        // Get most popular element
        $popular_element = array_combine($culture_labels, $culture_data);
        arsort($popular_element);
        $top_element = key($popular_element);

        // Get most used learning style
        $popular_style = array_combine($styles_labels, $styles_data);
        arsort($popular_style);
        $top_style = key($popular_style);

        $total_element_uses = array_sum($culture_data);
        $element_percentages = array_map(function($count) use ($total_element_uses) {
            return round(($count / $total_element_uses) * 100, 1);
        }, $culture_data);

        // Calculate percentages for styles
        $total_style_uses = array_sum($styles_data);
        $style_percentages = array_map(function($count) use ($total_style_uses) {
            return round(($count / $total_style_uses) * 100, 1);
        }, $styles_data);
    ?>

    <div class="explore-container">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_posts; ?></div>
                <div class="stat-label">Total Posts</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $top_element; ?></div>
                <div class="stat-label">Most Popular Element</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $top_style; ?></div>
                <div class="stat-label">Most Used Learning Style</div>
            </div>
        </div>

        <div class="rankings-container">
        <!-- Culture Elements Rankings -->
        <div class="ranking-box">
            <h2 class="ranking-title">Culture Elements Rankings</h2>
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Element</th>
                        <th>Usage</th>
                        <th>Distribution</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Sort elements by count while maintaining index association
                    arsort($culture_data);
                    $rank = 1;
                    foreach($culture_data as $index => $count) {
                        $element = $culture_labels[$index];
                        $percentage = $element_percentages[$index];
                        echo "<tr>
                                <td class='rank'>#{$rank}</td>
                                <td>{$element}</td>
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
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Learning Styles Rankings -->
        <div class="ranking-box">
            <h2 class="ranking-title">Learning Styles Rankings</h2>
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Style</th>
                        <th>Usage</th>
                        <th>Distribution</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Sort styles by count while maintaining index association
                    arsort($styles_data);
                    $rank = 1;
                    foreach($styles_data as $index => $count) {
                        $style = $styles_labels[$index];
                        $percentage = $style_percentages[$index];
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
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
        <div class="chart-container">
            <canvas id="cultureChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="stylesChart"></canvas>
        </div>

    </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
        // Culture Elements Chart
        new Chart(document.getElementById('cultureChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($culture_labels); ?>,
                datasets: [{
                    label: 'Posts by Culture Elements',
                    data: <?php echo json_encode($culture_data); ?>,
                    backgroundColor: '#365486',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Learning Styles Chart
        new Chart(document.getElementById('stylesChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($styles_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($styles_data); ?>,
                    backgroundColor: ['#365486', '#7FC7D9', '#DCF2F1', '#0F1035']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
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

        <!-- <div class="menu-section">
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
        </div> -->
        
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