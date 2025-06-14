<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Post Requests</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    require 'db_conn.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    include 'components/layout/guest/navbar.php';

    // Get status filter from URL
    $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

    // Get user's posts with filter
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM posts WHERE user_id = ?";
    if ($status_filter !== 'all') {
        $query .= " AND status = ?";
    }
    $query .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);
    if ($status_filter !== 'all') {
        $stmt->bind_param("is", $user_id, $status_filter);
    } else {
        $stmt->bind_param("i", $user_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Count posts by status
    $count_query = "SELECT status, COUNT(*) as count FROM posts WHERE user_id = ? GROUP BY status";
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    
    $counts = [
        'all' => 0,
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0
    ];
    
    while ($row = $count_result->fetch_assoc()) {
        $counts[$row['status']] = $row['count'];
    }
    $counts['all'] = array_sum($counts);
    ?>

    <div id="main-content">
        <div class="posts-nav">
            <h1>My Post Requests</h1>
            <div class="nav-tabs">
                <a href="?status=all" class="tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                    All
                    <span class="count"><?php echo $counts['all']; ?></span>
                </a>
                <a href="?status=pending" class="tab <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                    Pending
                    <span class="count"><?php echo $counts['pending']; ?></span>
                </a>
                <a href="?status=approved" class="tab <?php echo $status_filter === 'approved' ? 'active' : ''; ?>">
                    Approved
                    <span class="count"><?php echo $counts['approved']; ?></span>
                </a>
                <a href="?status=rejected" class="tab <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">
                    Rejected
                    <span class="count"><?php echo $counts['rejected']; ?></span>
                </a>
            </div>
        </div>
        
        <div class="posts-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($post = $result->fetch_assoc()): ?>
                    <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                        <div class="post-header">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <span class="status <?php echo $post['status']; ?>">
                                <?php echo ucfirst($post['status']); ?>
                            </span>
                        </div>
                        <div class="post-content">
                            <p><?php echo htmlspecialchars($post['description']); ?></p>
                            <?php if ($post['file_path']): ?>
                                <div class="post-media">
                                    <?php
                                    $file_extension = strtolower(pathinfo($post['file_path'], PATHINFO_EXTENSION));
                                    $file_name = basename($post['file_path']);

                                    // Image files
                                    $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                    // Video files
                                    $video_extensions = ['mp4', 'webm', 'ogg'];
                                    // Audio files
                                    $audio_extensions = ['mp3', 'wav', 'ogg'];
                                    // Document files
                                    $document_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];

                                    if (in_array($file_extension, $image_extensions)): ?>
                                        <div class="media-container image">
                                            <img src="<?php echo htmlspecialchars($post['file_path']); ?>" alt="Post Image">
                                            <div class="media-overlay">
                                                <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="view-btn" target="_blank">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="download-btn" download>
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    <?php elseif (in_array($file_extension, $video_extensions)): ?>
                                        <div class="media-container video">
                                            <video controls>
                                                <source src="<?php echo htmlspecialchars($post['file_path']); ?>" type="video/<?php echo $file_extension; ?>">
                                                Your browser does not support the video tag.
                                            </video>
                                            <div class="media-overlay">
                                                <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="download-btn" download>
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    <?php elseif (in_array($file_extension, $audio_extensions)): ?>
                                        <div class="media-container audio">
                                            <div class="audio-player">
                                                <i class="fas fa-music"></i>
                                                <audio controls>
                                                    <source src="<?php echo htmlspecialchars($post['file_path']); ?>" type="audio/<?php echo $file_extension; ?>">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                            <div class="media-overlay">
                                                <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="download-btn" download>
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="media-container document">
                                            <div class="document-preview">
                                                <i class="fas <?php 
                                                    echo in_array($file_extension, ['pdf']) ? 'fa-file-pdf' : 
                                                        (in_array($file_extension, ['doc', 'docx']) ? 'fa-file-word' :
                                                        (in_array($file_extension, ['xls', 'xlsx']) ? 'fa-file-excel' :
                                                        (in_array($file_extension, ['ppt', 'pptx']) ? 'fa-file-powerpoint' :
                                                        'fa-file-alt'))); ?>"></i>
                                                <span class="document-name"><?php echo htmlspecialchars($file_name); ?></span>
                                                <div class="document-actions">
                                                    <?php if ($file_extension === 'pdf'): ?>
                                                        <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="view-btn" target="_blank">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="download-btn" download>
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($post['status'] === 'rejected' && $post['message']): ?>
                            <div class="rejection-message">
                                <h4>Rejection Reason:</h4>
                                <p><?php echo htmlspecialchars($post['message']); ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="post-footer">
                            <span class="timestamp">
                                <?php 
                                $date = new DateTime($post['created_at']);
                                echo $date->format('M d, Y h:i A');
                                ?>
                            </span>
                            <?php if ($post['status'] === 'rejected'): ?>
                                <button class="appeal-btn" onclick="showAppealModal(<?php echo $post['id']; ?>)">
                                    <i class="fas fa-exclamation-circle"></i> Appeal
                                </button>
                                <button class="delete-btn" onclick="deletePost(<?php echo $post['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-posts">No <?php echo $status_filter === 'all' ? '' : $status_filter . ' '; ?>posts found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div id="messageModal" class="message-modal">
        <div class="message-modal-content">
            <div class="message-modal-header">
                <span class="close-modal">&times;</span>
            </div>
            <div class="message-modal-body">
                <div class="message-icon">
                    <i class="fas"></i>
                </div>
                <p id="modalMessage"></p>
            </div>
            <div class="message-modal-footer">
                <button onclick="closeMessageModal()">OK</button>
            </div>
        </div>
    </div>

    <!-- Appeal Modal -->
    <div id="appealModal" class="message-modal">
        <div class="message-modal-content">
            <div class="message-modal-header">
                <h3>Appeal Post</h3>
                <span class="close-modal" onclick="closeAppealModal()">&times;</span>
            </div>
            <div class="message-modal-body">
                <p>Please provide a reason for your appeal:</p>
                <textarea id="appealReason" rows="4" placeholder="Enter your appeal reason..."></textarea>
            </div>
            <div class="message-modal-footer">
                <button onclick="closeAppealModal()" class="cancel-btn">Cancel</button>
                <button onclick="submitAppeal()" class="submit-btn">Submit Appeal</button>
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
            background-color: #f5f5f5;
            color: #333;
            display: flex;
            flex-direction: row;
            padding-top: 80px;
        }

        /* Main Content */
        #main-content {
            margin-left: 400px;
            padding: 40px;
            flex-grow: 1;
            max-width: 1300px;
            margin-right: 200px;
        }

        .posts-nav {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .posts-nav h1 {
            color: #365486;
            margin: 0 0 20px 0;
            font-size: 28px;
        }

        .nav-tabs {
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .tab {
            text-decoration: none;
            color: #666;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab:hover {
            background: #f8f9fa;
            color: #365486;
        }

        .tab.active {
            background: #365486;
            color: white;
        }

        .tab.active .count {
            background: rgba(255,255,255,0.2);
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .post-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .post-header h3 {
            margin: 0;
            color: #365486;
            font-size: 18px;
        }

        .status {
            padding: 4px 12px;
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

        .post-content {
            margin-bottom: 15px;
        }

        .post-content p {
            color: #666;
            margin: 0 0 10px 0;
        }

        .post-media {
            margin: 15px 0;
            border-radius: 8px;
            overflow: hidden;
        }

        .media-container {
            position: relative;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        .media-container img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .media-container video {
            width: 100%;
            max-height: 200px;
            display: block;
        }

        .media-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .media-container:hover .media-overlay {
            opacity: 1;
        }

        .view-btn, .download-btn {
            padding: 8px 16px;
            background: #fff;
            color: #365486;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .view-btn:hover, .download-btn:hover {
            background: #365486;
            color: #fff;
            transform: translateY(-2px);
        }

        .audio-player {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8f9fa;
        }

        .audio-player i {
            font-size: 24px;
            color: #365486;
        }

        .audio-player audio {
            flex-grow: 1;
        }

        .document-preview {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .document-preview i {
            font-size: 24px;
            color: #365486;
        }

        .document-name {
            flex-grow: 1;
            font-size: 14px;
            color: #495057;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .document-actions {
            display: flex;
            gap: 10px;
        }

        .rejection-message {
            background: #f8f9fa;
            border-left: 3px solid #dc3545;
            padding: 10px 15px;
            margin: 15px 0;
        }

        .rejection-message h4 {
            color: #dc3545;
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .rejection-message p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }

        .post-footer {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .timestamp {
            color: #888;
            font-size: 12px;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .no-posts {
            grid-column: 1 / -1;
            text-align: center;
            color: #666;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .nav-tabs {
                flex-wrap: wrap;
            }
            
            .tab {
                flex: 1;
                min-width: 120px;
                justify-content: center;
            }

            #main-content {
                margin-left: 0;
                margin-right: 0;
                padding: 20px;
            }

            .media-overlay {
                position: static;
                opacity: 1;
                background: none;
                padding: 10px;
                flex-wrap: wrap;
            }

            .view-btn, .download-btn {
                width: 100%;
                justify-content: center;
            }

            .document-preview {
                flex-direction: column;
                text-align: center;
            }

            .document-actions {
                flex-direction: column;
                width: 100%;
            }
        }

        /* Message Modal Styles */
        .message-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translate(-50%, -60%); opacity: 0; }
            to { transform: translate(-50%, -50%); opacity: 1; }
        }

        .message-modal-content {
            background-color: #fff;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            animation: slideIn 0.3s ease;
        }

        .message-modal-header {
            display: flex;
            justify-content: flex-end;
        }

        .close-modal {
            color: #aaa;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-modal:hover {
            color: #666;
        }

        .message-modal-body {
            text-align: center;
            padding: 20px 0;
        }

        .message-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .message-icon.success i {
            color: #28a745;
        }

        .message-icon.error i {
            color: #dc3545;
        }

        .message-modal-body p {
            color: #333;
            font-size: 16px;
            margin: 0;
        }

        .message-modal-footer {
            text-align: center;
            padding-top: 20px;
        }

        .message-modal-footer button {
            background-color: #365486;
            color: white;
            border: none;
            padding: 8px 24px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .message-modal-footer button:hover {
            background-color: #2a4268;
            transform: translateY(-2px);
        }

        /* Appeal Modal Specific Styles */
        #appealModal .message-modal-content {
            max-width: 500px;
        }

        #appealModal .message-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        #appealModal .message-modal-header h3 {
            color: #365486;
            margin: 0;
        }

        #appealModal textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: vertical;
            font-family: inherit;
            margin-top: 10px;
        }

        #appealModal textarea:focus {
            outline: none;
            border-color: #365486;
        }

        #appealModal .message-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .cancel-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 24px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .submit-btn {
            background-color: #365486;
            color: white;
            border: none;
            padding: 8px 24px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .cancel-btn:hover, .submit-btn:hover {
            transform: translateY(-2px);
        }

        .appeal-btn {
            background-color: #ffc107;
            color: #856404;
            border: none;
            padding: 6px 12px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            margin-right: 10px;
        }

        .appeal-btn:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
        }

        /* Adjust post footer for multiple buttons */
        .post-footer .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>

    <!-- Sidebar -->
    <?php include 'components/layout/guest/sidebar.php'; ?>

    <!-- Include Chat Widget -->
    <?php include 'components/widgets/chat.php'; ?>

    <script>
    function showMessageModal(message, type = 'success') {
        const modal = document.getElementById('messageModal');
        const messageIcon = modal.querySelector('.message-icon');
        const iconElement = messageIcon.querySelector('i');
        
        // Set icon and class based on type
        messageIcon.className = 'message-icon ' + type;
        iconElement.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        
        // Set message
        document.getElementById('modalMessage').textContent = message;
        
        // Show modal
        modal.style.display = 'block';
    }

    function closeMessageModal() {
        const modal = document.getElementById('messageModal');
        modal.style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const messageModal = document.getElementById('messageModal');
        const appealModal = document.getElementById('appealModal');
        
        if (event.target === messageModal) {
            closeMessageModal();
        }
        if (event.target === appealModal) {
            closeAppealModal();
        }
    }

    // Close modal when clicking X
    document.querySelector('.close-modal').onclick = closeMessageModal;

    function deletePost(postId) {
        if (!confirm('Are you sure you want to delete this post?')) {
            return;
        }

        fetch('delete_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Remove the post card from the UI
                const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
                if (postCard) {
                    postCard.remove();
                }

                // Update the counts in the tabs
                const rejectedCountSpan = document.querySelector('a[href="?status=rejected"] .count');
                const allCountSpan = document.querySelector('a[href="?status=all"] .count');
                
                if (rejectedCountSpan) {
                    let count = parseInt(rejectedCountSpan.textContent) - 1;
                    rejectedCountSpan.textContent = count;
                    
                    // Update all count
                    if (allCountSpan) {
                        allCountSpan.textContent = parseInt(allCountSpan.textContent) - 1;
                    }

                    // If no more rejected posts, reload to remove the badge
                    if (count === 0) {
                        window.location.reload();
                    }
                }

                // Show success message with custom modal
                showMessageModal('Post deleted successfully');
            } else {
                // Show error message with custom modal
                showMessageModal(data.message || 'Error deleting post', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessageModal('Error deleting post', 'error');
        });
    }

    let currentPostId = null;

    function showAppealModal(postId) {
        currentPostId = postId;
        document.getElementById('appealModal').style.display = 'block';
        document.getElementById('appealReason').value = '';
    }

    function closeAppealModal() {
        document.getElementById('appealModal').style.display = 'none';
        currentPostId = null;
    }

    function submitAppeal() {
        const appealReason = document.getElementById('appealReason').value.trim();
        
        if (!appealReason) {
            showMessageModal('Please provide a reason for your appeal', 'error');
            return;
        }

        fetch('appeal_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${currentPostId}&appeal_reason=${encodeURIComponent(appealReason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update UI
                const postCard = document.querySelector(`.post-card[data-post-id="${currentPostId}"]`);
                if (postCard) {
                    // Update status badge
                    const statusBadge = postCard.querySelector('.status');
                    statusBadge.className = 'status pending';
                    statusBadge.textContent = 'Pending';

                    // Remove action buttons
                    const actionButtons = postCard.querySelector('.action-buttons');
                    if (actionButtons) {
                        actionButtons.remove();
                    }
                }

                // Update counts
                const pendingCountSpan = document.querySelector('a[href="?status=pending"] .count');
                const rejectedCountSpan = document.querySelector('a[href="?status=rejected"] .count');
                
                if (pendingCountSpan && rejectedCountSpan) {
                    pendingCountSpan.textContent = parseInt(pendingCountSpan.textContent) + 1;
                    rejectedCountSpan.textContent = parseInt(rejectedCountSpan.textContent) - 1;
                }

                closeAppealModal();
                showMessageModal('Appeal submitted successfully');
            } else {
                showMessageModal(data.message || 'Error submitting appeal', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessageModal('Error submitting appeal', 'error');
        });
    }
    </script>
</body>
</html> 