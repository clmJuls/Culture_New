<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Requests</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body>
    <?php
    require 'db_conn.php';
    session_start();

    date_default_timezone_set('Asia/Manila'); // Set this to your timezone

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    include 'components/layout/admin/navbar.php'; 

    // Pagination settings
    $posts_per_page = 6;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($current_page - 1) * $posts_per_page;

    // Get the status filter from URL parameter
    $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

    // Modify the count query to exclude approved posts
    $count_query = "SELECT COUNT(*) as total FROM posts p WHERE p.status != 'approved'";
    if ($status_filter !== 'all') {
        $count_query .= " AND p.status = '$status_filter'";
    }

    $count_result = $conn->query($count_query);
    $total_posts = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_posts / $posts_per_page);

    // Modify the main query to exclude approved posts
    $query = "SELECT p.*, u.username, u.profile_picture 
             FROM posts p
             LEFT JOIN users u ON p.user_id = u.id
             WHERE p.status != 'approved'";

    if ($status_filter !== 'all') {
        $query .= " AND p.status = ?";
    }

    $query .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";

    // Prepare and execute the query with appropriate parameters
    $stmt = $conn->prepare($query);
    if ($status_filter !== 'all') {
        $stmt->bind_param("sii", $status_filter, $posts_per_page, $offset);
    } else {
        $stmt->bind_param("ii", $posts_per_page, $offset);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Add this helper function at the top of your PHP section
    function isImageFile($path) {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, $imageExtensions);
    }

    // Add these helper functions at the top of your PHP section
    function getFileType($path) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videoExtensions = ['mp4', 'webm', 'ogg'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        
        if (in_array($extension, $imageExtensions)) return 'image';
        if (in_array($extension, $videoExtensions)) return 'video';
        if (in_array($extension, $documentExtensions)) return 'document';
        
        return 'unknown';
    }

    ?>

    <div class="post-requests-container">
        <div class="page-header">
            <h2>Post Requests</h2>
            <div class="filter-section">
                <select id="statusFilter">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Requests</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <!-- <div class="search-box">
                    <input type="text" placeholder="Search requests...">
                    <i class="fas fa-search"></i>
                </div> -->
            </div>
        </div>

        <div class="requests-grid">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($post = $result->fetch_assoc()) {
                    // Convert learning styles string to array
                    $learning_styles = explode(',', $post['learning_styles']);
                    
                    // Generate learning style tags HTML
                    $learning_style_tags = '';
                    foreach ($learning_styles as $style) {
                        $style = trim($style);
                        $icon = '';
                        $class = '';
                        
                        switch (strtolower($style)) {
                            case 'visual':
                                $icon = 'fa-eye';
                                $class = 'visual';
                                break;
                            case 'kinesthetic':
                                $icon = 'fa-running';
                                $class = 'kinesthetic';
                                break;
                            case 'auditory & oral':
                                $icon = 'fa-headphones';
                                $class = 'auditory';
                                break;
                            case 'read & write':
                                $icon = 'fa-book-reader';
                                $class = 'read-write';
                                break;
                        }
                        
                        $learning_style_tags .= sprintf(
                            '<span class="tag learning-style %s"><i class="fas %s"></i> %s</span>',
                            $class, $icon, $style
                        );
                    }
                    
                    // Calculate time ago from created_at with proper timezone handling
                    $created_at = new DateTime($post['created_at']);
                    $now = new DateTime();
                    $interval = $created_at->diff($now);
                    
                    $time_ago = '';
                    if ($interval->y > 0) {
                        $time_ago = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                    } elseif ($interval->m > 0) {
                        $time_ago = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                    } elseif ($interval->d > 0) {
                        $time_ago = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                    } elseif ($interval->h > 0) {
                        $time_ago = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                    } elseif ($interval->i > 0) {
                        $time_ago = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                    } else {
                        $time_ago = 'Just now';
                    }
                    
                    ?>
                    <div class="request-card">
                        <div class="request-header">
                            <?php
                            $profile_picture = $post['profile_picture'] ? htmlspecialchars($post['profile_picture']) : 'assets/hero/v07_20@Shanks.png';
                            ?>
                            <img src="<?php echo $profile_picture; ?>" 
                                 alt="User Avatar" class="user-avatar">
                            <div class="user-info">
                                <h3><?php echo htmlspecialchars($post['username']); ?></h3>
                            </div>
                            <span class="status <?php echo $post['status']; ?>">
                                <?php echo ucfirst($post['status']); ?>
                            </span>
                        </div>
                        
                        <div class="post-content">
                            <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                            <p><?php echo htmlspecialchars($post['description']); ?></p>
                            <?php if ($post['file_path']): ?>
                                <div class="post-media">
                                    <?php
                                    $fileType = getFileType($post['file_path']);
                                    switch($fileType) {
                                        case 'image':
                                            ?>
                                            <img src="<?php echo htmlspecialchars($post['file_path']); ?>" alt="Post Image">
                                            <?php
                                            break;
                                        case 'video':
                                            ?>
                                            <video controls width="100%">
                                                <source src="<?php echo htmlspecialchars($post['file_path']); ?>" type="video/<?php echo pathinfo($post['file_path'], PATHINFO_EXTENSION); ?>">
                                                Your browser does not support the video tag.
                                            </video>
                                            <?php
                                            break;
                                        case 'document':
                                            $fileName = basename($post['file_path']);
                                            $extension = strtoupper(pathinfo($post['file_path'], PATHINFO_EXTENSION));
                                            ?>
                                            <div class="document-preview">
                                                <i class="fas fa-file-<?php echo $extension === 'PDF' ? 'pdf' : 'document'; ?>"></i>
                                                <span class="document-name"><?php echo htmlspecialchars($fileName); ?></span>
                                                <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="download-btn" download>
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                            <?php
                                            break;
                                        default:
                                            ?>
                                            <div class="document-preview">
                                                <i class="fas fa-file"></i>
                                                <span class="document-name"><?php echo htmlspecialchars(basename($post['file_path'])); ?></span>
                                                <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="download-btn" download>
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                            <?php
                                            break;
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            <div class="post-tags">
                                <?php echo $learning_style_tags; ?>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <?php if ($post['status'] !== 'approved'): ?>
                                <button class="approve-btn" data-post-id="<?php echo $post['id']; ?>">✓ Approve</button>
                            <?php endif; ?>
                            <?php if ($post['status'] === 'pending'): ?>
                                <button class="reject-btn" data-post-id="<?php echo $post['id']; ?>">✕ Reject</button>
                            <?php endif; ?>
                            <p class="timestamp"><?php echo $time_ago; ?></p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="no-requests">No post requests found.</p>';
            }
            ?>
        </div>

        <div class="pagination">
            <?php if ($total_pages > 1): ?>
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&status=<?php echo $status_filter; ?>" 
                       class="page-link">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>" 
                       class="page-link <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&status=<?php echo $status_filter; ?>" 
                       class="page-link">Next &raquo;</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Post Details Modal -->
    <div id="postDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-header">
                <h2>Post Details</h2>
            </div>
            <div class="modal-body">
                <!-- Post details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button class="approve-btn"><i class="fas fa-check"></i> Approve</button>
                <button class="reject-btn"><i class="fas fa-times"></i> Reject</button>
                <button class="close-btn">Close</button>
            </div>
        </div>
    </div>

    <!-- Add this HTML for the confirmation modal at the bottom of your file -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Action</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
                <div id="rejectReasonContainer" style="display: none;">
                    <div class="form-group">
                        <label for="rejectReason">Rejection Reason</label>
                        <textarea id="rejectReason" 
                                 placeholder="Please provide a detailed reason for rejection..."
                                 rows="4"></textarea>
                        <p class="error-message" id="reasonError">
                            Please provide a reason for rejection
                        </p>
                        <div class="textarea-footer">
                            <span class="char-count">0/500</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="cancel-btn">Cancel</button>
                <button id="confirmButton" class="confirm-btn">Confirm</button>
            </div>
        </div>
    </div>

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

    <style>

        .post-requests-container {
            position: relative;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            width: 100%;
            max-width: 1200px;
            padding: 40px;
            margin: 120px auto 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            color: #365486;
            font-size: 24px;
        }

        .filter-section {
            display: flex;
            gap: 15px;
        }

        #statusFilter {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            font-size: 14px;
            color: #365486;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #statusFilter:hover {
            border-color: #365486;
        }

        #statusFilter:focus {
            border-color: #365486;
            box-shadow: 0 0 0 2px rgba(54, 84, 134, 0.1);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 8px 15px;
            padding-right: 35px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
        }

        .search-box i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .requests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
            min-height: 400px; /* Minimum height to prevent layout shift */
        }

        .request-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 400px; /* Adjust this value based on your needs */
        }

        .request-header {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            position: relative;
        }

        .request-header .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-info {
            flex-grow: 1;
        }

        .user-info h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .timestamp {
            color: #666;
            font-size: 0.8rem;
            margin-left: auto;
        }

        .status {
            margin-left: auto;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status.approved {
            background: #d4edda;
            color: #155724;
        }

        .status:hover {
            opacity: 0.9;
        }

        .post-content {
            flex-grow: 1;
            padding: 15px;
        }

        .post-content h4 {
            margin: 0 0 10px 0;
            color: #365486;
        }

        .post-content p {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 14px;
        }

        .post-media {
            margin: 15px 0;
            border-radius: 5px;
            overflow: hidden;
        }

        .post-media img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }

        .post-media video {
            width: 100%;
            max-height: 200px;
            border-radius: 5px;
            background: #000;
        }

        .document-preview {
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .document-preview i {
            font-size: 24px;
            color: #365486;
        }

        .document-preview .document-name {
            flex-grow: 1;
            font-size: 14px;
            color: #495057;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .download-btn {
            padding: 6px 12px;
            background: #365486;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .download-btn:hover {
            background: #2a4268;
            transform: translateY(-1px);
        }

        .download-btn i {
            font-size: 14px;
            color: white;
        }

        .post-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .tag {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            color: #495057;
        }

        .action-buttons {
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto; /* This pushes the buttons to the bottom */
            border-top: 1px solid #eee;
        }

        .approve-btn, .reject-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .approve-btn {
            background: #28a745;
            color: white;
            margin-right: 10px;
        }

        .reject-btn {
            background: #dc3545;
            color: white;
        }

        .approve-btn:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .reject-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1001;
        }

        .modal-content {
            position: relative;
            background: white;
            margin: 50px auto;
            padding: 20px;
            width: 90%;
            max-width: 800px;
            border-radius: 10px;
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Add hover effects to buttons */
        .action-buttons button:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .post-tags .learning-style {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .learning-style.visual {
            background: #e3f2fd;
            color: #1976d2;
        }

        .learning-style.kinesthetic {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .learning-style.auditory {
            background: #e8f5e9;
            color: #388e3c;
        }

        .learning-style i {
            font-size: 12px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 30px 0;
        }

        .page-link {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #365486;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background-color: #f8f9fa;
        }

        .page-link.active {
            background-color: #365486;
            color: white;
            border-color: #365486;
        }

        .no-requests {
            grid-column: 1 / -1;
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>

    <style>
    /* Updated Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1001;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 0;
        border-radius: 12px;
        width: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s;
        max-width: 95%;
    }

    .modal-header {
        padding: 20px;
        background-color: #365486;
        color: white;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 500;
    }

    .close-modal {
        color: white;
        font-size: 24px;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .close-modal:hover {
        opacity: 0.8;
    }

    .modal-body {
        padding: 30px;
    }

    .form-group {
        margin-top: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #365486;
        font-weight: 500;
    }

    #rejectReason {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        resize: vertical;
        min-height: 120px;
        font-family: inherit;
        font-size: 14px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    #rejectReason:focus {
        outline: none;
        border-color: #365486;
        box-shadow: 0 0 0 3px rgba(54, 84, 134, 0.1);
    }

    .textarea-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .char-count {
        color: #666;
        font-size: 12px;
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: none;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s, transform 0.3s;
    }

    .error-message.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .modal-footer {
        padding: 20px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        border-top: 1px solid #eee;
    }

    .cancel-btn, .confirm-btn {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .cancel-btn {
        background-color: #f8f9fa;
        color: #365486;
        border: 1px solid #365486;
    }

    .confirm-btn {
        background-color: #365486;
        color: white;
        min-width: 100px;
    }

    .confirm-btn.reject-btn {
        background-color: #dc3545;
    }

    .cancel-btn:hover {
        background-color: #e9ecef;
    }

    .confirm-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .confirm-btn:active {
        transform: translateY(0);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { 
            opacity: 0;
            transform: translateY(-30px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .shake {
        animation: shake 0.4s ease-in-out;
    }
    </style>

    <style>
    .file-notice {
        padding: 10px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        color: #6c757d;
        text-align: center;
        font-size: 14px;
    }
    </style>

    <script>
        // Toggle post details modal
        function toggleModal(show = true) {
            const modal = document.getElementById('postDetailsModal');
            modal.style.display = show ? 'block' : 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('postDetailsModal');
            if (event.target == modal) {
                toggleModal(false);
            }
        }

        // Close modal when clicking close button
        document.querySelector('.close-modal').onclick = function() {
            toggleModal(false);
        }

        document.querySelector('.close-btn').onclick = function() {
            toggleModal(false);
        }

        // View details button click handler
        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.onclick = function() {
                toggleModal(true);
            }
        });

        // Filter change handler
        document.getElementById('statusFilter').onchange = function() {
            // Implement filtering logic here
            console.log('Filter changed to:', this.value);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('confirmModal');
            const closeModal = document.querySelector('.close-modal');
            const cancelBtn = modal.querySelector('.cancel-btn');
            const confirmBtn = document.getElementById('confirmButton');
            const rejectReasonContainer = document.getElementById('rejectReasonContainer');
            const rejectReason = document.getElementById('rejectReason');
            const reasonError = document.getElementById('reasonError');
            const charCount = document.querySelector('.char-count');
            let currentAction = null;
            let currentPostId = null;

            // Character count update
            rejectReason.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = `${length}/500`;
                
                if (length > 500) {
                    this.value = this.value.substring(0, 500);
                    charCount.style.color = '#dc3545';
                } else {
                    charCount.style.color = '#666';
                }
                
                // Hide error message when user starts typing
                reasonError.classList.remove('show');
            });

            // Handle approve button clicks
            document.querySelectorAll('.approve-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.dataset.postId;
                    showConfirmModal('approve', postId);
                });
            });

            // Handle reject button clicks
            document.querySelectorAll('.reject-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.dataset.postId;
                    showConfirmModal('reject', postId);
                });
            });

            function showConfirmModal(action, postId) {
                currentAction = action;
                currentPostId = postId;
                const message = `Are you sure you want to ${action} this post?`;
                document.getElementById('confirmMessage').textContent = message;
                
                // Show/hide reject reason based on action
                rejectReasonContainer.style.display = action === 'reject' ? 'block' : 'none';
                if (action === 'reject') {
                    rejectReason.value = ''; // Clear previous reason
                    reasonError.classList.remove('show');
                    charCount.textContent = '0/500';
                }
                
                // Update confirm button style based on action
                confirmBtn.className = `confirm-btn ${action}-btn`;
                confirmBtn.textContent = action.charAt(0).toUpperCase() + action.slice(1);
                
                modal.style.display = 'block';
                if (action === 'reject') {
                    rejectReason.focus();
                }
            }

            function closeConfirmModal() {
                modal.style.display = 'none';
                currentAction = null;
                currentPostId = null;
                rejectReason.value = ''; // Clear reason on close
                reasonError.classList.remove('show');
            }

            // Close modal events
            closeModal.onclick = closeConfirmModal;
            cancelBtn.onclick = closeConfirmModal;
            window.onclick = function(event) {
                if (event.target === modal) {
                    closeConfirmModal();
                }
            }

            // Handle confirm button click
            confirmBtn.onclick = function() {
                if (currentAction && currentPostId) {
                    if (currentAction === 'reject') {
                        const reason = rejectReason.value.trim();
                        if (!reason) {
                            reasonError.classList.add('show');
                            rejectReason.classList.add('shake');
                            setTimeout(() => rejectReason.classList.remove('shake'), 400);
                            return;
                        }
                        if (reason.length > 500) {
                            return;
                        }
                        reasonError.classList.remove('show');
                    }
                    updatePostStatus(currentPostId, currentAction);
                }
            }

            function updatePostStatus(postId, action) {
                // Convert action to correct status value
                const status = action === 'approve' ? 'approved' : 'rejected';
                const reason = action === 'reject' ? rejectReason.value.trim() : '';

                // Disable confirm button and show loading state
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                fetch('posts_management.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&post_id=${postId}&status=${status}&reason=${encodeURIComponent(reason)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal and reload page
                        closeConfirmModal();
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Error updating post status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('confirmMessage').textContent = 
                        'An error occurred: ' + error.message;
                    confirmBtn.style.display = 'none';
                })
                .finally(() => {
                    // Re-enable confirm button and restore text
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = action.charAt(0).toUpperCase() + action.slice(1);
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            
            statusFilter.addEventListener('change', function() {
                const selectedStatus = this.value;
                // Update URL with new status filter
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('status', selectedStatus);
                // Reset to page 1 when filter changes
                currentUrl.searchParams.set('page', '1');
                window.location.href = currentUrl.toString();
            });
        });
    </script>
</body>
</html>