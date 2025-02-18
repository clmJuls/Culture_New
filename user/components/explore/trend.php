<?php
function getTrendingPosts($conn, $limit = 5) {
    $query = "
        SELECT p.*, u.username, u.profile_picture,
        COUNT(l.id) as like_count
        FROM posts p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN likes l ON p.id = l.post_id AND l.is_active = 1
        GROUP BY p.id
        ORDER BY like_count DESC, p.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<div class="right-sidebar">
    <div class="trending-section">
        <h3>Trending Posts</h3>
        <div class="trending-posts">
            <?php
            $trending_posts = getTrendingPosts($conn);
            while ($post = $trending_posts->fetch_assoc()) {
                echo "
                <div class='trending-post'>
                    <div class='trending-header'>
                        <img src='{$post['profile_picture']}' alt='Profile' class='trending-profile-pic'>
                        <span>{$post['username']}</span>
                    </div>
                    <div class='trending-content'>
                        <h4>{$post['title']}</h4>
                        <p class='like-count'><i class='fas fa-heart'></i> {$post['like_count']} likes</p>
                    </div>
                </div>
                ";
            }
            ?>
        </div>
    </div>
    
    <div class="premium-section">
        <h3>Upgrade to Premium</h3>
        <div class="premium-content">
            <p>Get access to exclusive features:</p>
            <ul>
                <li><i class="fas fa-check"></i> Additional Design</li>
                <li><i class="fas fa-check"></i> Advanced analytics</li>
                <li><i class="fas fa-check"></i> Premium content</li>
            </ul>
            <button class="upgrade-button" onclick="window.location.href='premium.php'">
                Upgrade Now
            </button>
        </div>
    </div>
</div>

<style>
.right-sidebar {
    position: fixed;
    top: 120px;
    right: 20px;
    width: 380px;
    height: calc(100vh - 140px);
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    padding: 20px;
    z-index: 999;
}

.right-sidebar::-webkit-scrollbar {
    width: 8px;
}

.right-sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.right-sidebar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.right-sidebar::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.trending-section, .premium-section {
    margin-bottom: 30px;
}

.trending-section h3, .premium-section h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.trending-post {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
    transition: transform 0.2s ease;
    cursor: pointer;
}

.trending-post:hover {
    transform: translateY(-2px);
    background-color: #f0f2f5;
}

.trending-header {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.trending-profile-pic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 10px;
    object-fit: cover;
}

.trending-content h4 {
    font-size: 14px;
    margin-bottom: 5px;
    color: #333;
}

.like-count {
    font-size: 12px;
    color: #666;
}

.like-count i {
    color: #dc3545;
    margin-right: 5px;
}

.premium-content {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.premium-content p {
    font-size: 14px;
    margin-bottom: 10px;
    color: #333;
}

.premium-content ul {
    list-style: none;
    padding: 0;
    margin-bottom: 15px;
}

.premium-content li {
    font-size: 13px;
    margin-bottom: 8px;
    color: #555;
}

.premium-content li i {
    color: #28a745;
    margin-right: 8px;
}

.upgrade-button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.upgrade-button:hover {
    background-color: #0056b3;
}

@media screen and (max-width: 1400px) {
    .right-sidebar {
        width: 320px;
    }
}

@media screen and (max-width: 992px) {
    .right-sidebar {
        display: none;
    }
}
</style>
