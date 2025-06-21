<?php
function getTrendingPosts($conn, $limit = 3) {
    $query = "
        SELECT p.*, u.username, u.profile_picture,
        COUNT(l.id) as like_count
        FROM posts p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN likes l ON p.id = l.post_id AND l.is_active = 1
        WHERE p.status = 'approved'
        GROUP BY p.id
        ORDER BY like_count DESC, p.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Check if user is logged in
$show_premium_section = !isset($_SESSION['user_id']);
if (isset($_SESSION['user_id'])) {
    // Check if user is premium or admin
    if ((isset($_SESSION['isPremium']) && $_SESSION['isPremium'] == 1) || 
        (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1)) {
        $show_premium_section = false;
    } else {
        $show_premium_section = true;
    }
}
?>

<div class="right-sidebar">
    <div class="trending-section">
        <h3>🔥 Top 3 Trending Posts</h3>
        <div class="trending-posts">
            <?php
            $trending_posts = getTrendingPosts($conn);
            $rank = 1;
            while ($post = $trending_posts->fetch_assoc()) {
                $profile_picture = $post['profile_picture'] ? htmlspecialchars($post['profile_picture']) : 'assets/hero/v07_20@Shanks.png';
                $description = strlen($post['description']) > 100 ? substr($post['description'], 0, 100) . '...' : $post['description'];
                
                echo "
                <div class='trending-post rank-{$rank}'>
                    <div class='trending-rank'>#$rank</div>
                    <div class='trending-header'>
                        <div class='user-info'>
                            <img src='{$profile_picture}' alt='Profile' class='trending-profile-pic'>
                            <span class='username'>{$post['username']}</span>
                        </div>
                        <div class='trending-stats'>
                            <span class='like-count'><i class='fas fa-heart'></i> {$post['like_count']}</span>
                        </div>
                    </div>
                    <div class='trending-content'>
                        <div class='trending-media'>
                            " . ($post['file_path'] ? "<img src='{$post['file_path']}' alt='Post media' class='trending-image'>" : "") . "
                        </div>
                        <div class='trending-text'>
                            <h4>{$post['title']}</h4>
                            <p class='trending-description'>{$description}</p>
                        </div>
                    </div>
                </div>
                ";
                $rank++;
            }
            ?>
        </div>
    </div>
    
    <?php if ($show_premium_section): ?>
        <div class="premium-section">
            <div class="premium-header">
                <i class="fas fa-crown"></i>
                <h3>Upgrade to Premium</h3>
            </div>
            <div class="premium-content">
                <p class="premium-description">Get access to exclusive features:</p>
                <ul class="premium-features">
                    <li class="feature-item">
                        <i class="fas fa-palette"></i>
                        <span>Additional Design</span>
                    </li>
                    <li class="feature-item">
                        <i class="fas fa-chart-bar"></i>
                        <span>Advanced analytics</span>
                    </li>
                    <li class="feature-item">
                        <i class="fas fa-star"></i>
                        <span>Premium content</span>
                    </li>
                </ul>
                <button class="upgrade-button" onclick="window.location.href='premium.php'">
                    <i class="fas fa-crown"></i>
                    Upgrade Now
                </button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$show_premium_section): ?>
        <div class="analytics-section">
            <h3><i class="fas fa-chart-line"></i> Quick Analytics</h3>
            <div class="analytics-content">
                <?php
                // Get culture elements data
                $culture_query = "SELECT 
                                    SUBSTRING_INDEX(SUBSTRING_INDEX(culture_elements, ',', 1), ',', -1) as element,
                                    COUNT(*) as count
                                FROM posts
                                WHERE status = 'approved'
                                GROUP BY element
                                ORDER BY count DESC
                                LIMIT 1";
                $culture_result = $conn->query($culture_query);
                $top_element = $culture_result->fetch_assoc();
                
                // Get most active user
                $active_user_query = "SELECT u.username, COUNT(p.id) as post_count 
                                    FROM users u 
                                    JOIN posts p ON u.id = p.user_id 
                                    WHERE p.status = 'approved'
                                    GROUP BY u.id 
                                    ORDER BY post_count DESC 
                                    LIMIT 1";
                $active_user_result = $conn->query($active_user_query);
                $most_active_user = $active_user_result->fetch_assoc();
                
                // Get learning styles data
                $styles_query = "SELECT 
                                    SUBSTRING_INDEX(SUBSTRING_INDEX(learning_styles, ',', 1), ',', -1) as style,
                                    COUNT(*) as count
                                FROM posts
                                WHERE status = 'approved'
                                GROUP BY style
                                ORDER BY count DESC
                                LIMIT 1";
                $styles_result = $conn->query($styles_query);
                $top_style = $styles_result->fetch_assoc();
                
                // Get total posts
                $total_posts_result = $conn->query("SELECT COUNT(*) as total FROM posts WHERE status = 'approved'");
                $total_posts = $total_posts_result->fetch_assoc()['total'] ?? 0;
                ?>
                
                <div class="analytics-grid">
                    <div class="stat-card total-posts">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo $total_posts; ?></span>
                            <span class="stat-label">Total Posts</span>
                        </div>
                    </div>
                    
                    <div class="stat-card active-user">
                        <div class="stat-icon">
                            <img src="<?php echo $profile_picture; ?>" alt="Most Active User" class="user-avatar">
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo $most_active_user['username'] ?? 'N/A'; ?></span>
                            <span class="stat-label">Most Active User</span>
                        </div>
                    </div>
                    
                    <div class="stat-card learning-style">
                        <div class="stat-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?php echo $top_style['style'] ?? 'N/A'; ?></span>
                            <span class="stat-label">Top Learning Style</span>
                        </div>
                    </div>
                </div>
                
                <a href="generate_report.php" class="view-full-report">
                    View Full Analytics Report <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
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

/* Updated trending section styles */
.trending-section {
    margin-bottom: 30px;
}

.trending-section h3 {
    font-size: 20px;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.trending-posts {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.trending-post {
    padding: 15px;
    border-radius: 12px;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
    cursor: pointer;
    position: relative;
    border: 1px solid #eee;
}

.trending-post:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.trending-rank {
    position: absolute;
    top: -10px;
    left: -10px;
    background: #365486;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.trending-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.trending-profile-pic {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.username {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.trending-stats {
    display: flex;
    align-items: center;
    gap: 12px;
}

.like-count {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #dc3545;
    font-weight: 500;
    font-size: 14px;
}

.like-count i {
    font-size: 16px;
}

.trending-content {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.trending-media {
    width: 100%;
    height: 150px;
    border-radius: 8px;
    overflow: hidden;
}

.trending-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.trending-text h4 {
    font-size: 16px;
    color: #333;
    margin-bottom: 8px;
    line-height: 1.3;
}

.trending-description {
    font-size: 14px;
    color: #666;
    line-height: 1.4;
}

/* Rank-specific styles */
.rank-1 {
    border-color: #ffd700;
    background: linear-gradient(to bottom right, #fff, #fff8e7);
}

.rank-1 .trending-rank {
    background: #ffd700;
    color: #333;
}

.rank-2 {
    border-color: #c0c0c0;
    background: linear-gradient(to bottom right, #fff, #f8f8f8);
}

.rank-2 .trending-rank {
    background: #c0c0c0;
}

.rank-3 {
    border-color: #cd7f32;
    background: linear-gradient(to bottom right, #fff, #fff4f0);
}

.rank-3 .trending-rank {
    background: #cd7f32;
}

/* Updated Analytics Section Styles */
.analytics-section {
    background-color: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.analytics-section h3 {
    font-size: 20px;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.analytics-section h3 i {
    color: #365486;
}

.analytics-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.analytics-grid {
    display: grid;
    gap: 15px;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
    border: 1px solid #eee;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(54, 84, 134, 0.05), rgba(127, 199, 217, 0.05));
    z-index: 0;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    position: relative;
    z-index: 1;
}

.total-posts .stat-icon {
    background: linear-gradient(135deg, #365486, #7FC7D9);
    color: white;
}

.active-user .stat-icon {
    background: linear-gradient(135deg, #FF6B6B, #FFD93D);
    color: white;
}

.learning-style .stat-icon {
    background: linear-gradient(135deg, #4CAF50, #8BC34A);
    color: white;
}

.stat-info {
    flex: 1;
    position: relative;
    z-index: 1;
}

.stat-value {
    display: block;
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.stat-label {
    display: block;
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.view-full-report {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    background: linear-gradient(135deg, #365486, #7FC7D9);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-align: center;
    margin-top: 10px;
}

.view-full-report:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background: linear-gradient(135deg, #2a4170, #6ea5b5);
}

.view-full-report i {
    transition: transform 0.3s ease;
}

.view-full-report:hover i {
    transform: translateX(4px);
}

/* Premium Section Styles */
.premium-section {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(54, 84, 134, 0.1);
    position: relative;
    overflow: hidden;
}

.premium-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, rgba(54, 84, 134, 0.1), rgba(127, 199, 217, 0.1));
    border-radius: 50%;
    transform: translate(50%, -50%);
    z-index: 0;
}

.premium-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    position: relative;
}

.premium-header i {
    font-size: 24px;
    color: #FFD700;
    animation: float 3s ease-in-out infinite;
}

.premium-header h3 {
    font-size: 22px;
    color: #365486;
    font-weight: 600;
    margin: 0;
    padding: 0;
    border: none;
}

.premium-description {
    font-size: 16px;
    color: #555;
    margin-bottom: 20px;
    font-weight: 500;
}

.premium-features {
    list-style: none;
    padding: 0;
    margin: 0 0 24px 0;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: white;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid rgba(54, 84, 134, 0.1);
}

.feature-item:hover {
    transform: translateX(8px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    border-color: rgba(54, 84, 134, 0.2);
}

.feature-item i {
    font-size: 20px;
    color: #365486;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(54, 84, 134, 0.1);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.feature-item:hover i {
    background: #365486;
    color: white;
    transform: scale(1.1);
}

.feature-item span {
    font-size: 16px;
    color: #333;
    font-weight: 500;
}

.upgrade-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 16px;
    background: linear-gradient(135deg, #365486, #7FC7D9);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.upgrade-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    transition: 0.5s;
}

.upgrade-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(54, 84, 134, 0.3);
    background: linear-gradient(135deg, #2a4170, #6ea5b5);
}

.upgrade-button:hover::before {
    left: 100%;
}

.upgrade-button i {
    font-size: 18px;
}

@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(0px);
    }
}

/* Responsive adjustments */
@media screen and (max-width: 1400px) {
    .analytics-grid {
        grid-template-columns: 1fr;
    }
}

@media screen and (max-width: 992px) {
    .right-sidebar {
        display: none;
    }
}
</style>
