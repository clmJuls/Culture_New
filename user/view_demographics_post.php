<?php
session_start();
require_once 'db_conn.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: demographics.php');
    exit;
}

$post_id = (int)$_GET['id'];

// First, increment the view count
$update_views = "UPDATE demographics_posts SET views = views + 1 WHERE id = ?";
$stmt = mysqli_prepare($conn, $update_views);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Then fetch the post details
$query = "SELECT dp.*, u.username 
          FROM demographics_posts dp 
          LEFT JOIN users u ON dp.user_id = u.id 
          WHERE dp.id = ? AND dp.status = 'published'";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$post) {
    header('Location: demographics.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .post-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .post-header {
            margin-bottom: 30px;
        }

        .post-title {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .post-meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 20px;
        }

        .post-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .post-content {
            font-size: 1.1em;
            color: #444;
            line-height: 1.8;
        }

        .category-tag {
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            color: #495057;
            text-decoration: none;
        }

        .tags {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .tag {
            background-color: #f8f9fa;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            color: #666;
            margin-right: 5px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .views-count {
            color: #666;
            font-size: 0.9em;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'components/layout/guest/navbar.php'; ?>

    <div class="post-container">
        <a href="demographics.php" class="back-button">← Back to Demographics</a>

        <article class="post-header">
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="post-meta">
                <span>By <?php echo htmlspecialchars($post['username']); ?></span> • 
                <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span> • 
                <span class="category-tag"><?php echo htmlspecialchars($post['category']); ?></span>
            </div>

            <?php if ($post['image_url']): ?>
                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                     class="post-image">
            <?php endif; ?>
        </article>

        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <?php if ($post['tags']): ?>
            <div class="tags">
                <?php
                $tags = explode(',', $post['tags']);
                foreach ($tags as $tag):
                    $tag = trim($tag);
                    if (!empty($tag)):
                ?>
                    <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>

        <div class="views-count">
            <?php echo number_format($post['views']); ?> views
        </div>
    </div>

    <?php include 'components/layout/guest/sidebar.php'; ?>
    <?php include 'components/widgets/chat.php'; ?>
</body>
</html> 