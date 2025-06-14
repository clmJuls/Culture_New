<div class="navbar">
        <div style="display: flex; align-items: center;">
            <a href="home.php" class="logo-link" style="display: flex; align-items: center; text-decoration: none;">
                <img src="assets/logo/logo.png" alt="Kulturifiko Logo">
                <h1>Kulturabase</h1>
            </a>
        </div>
        <div class="nav-links">
            <a href="home.php" <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'class="active"' : ''; ?>>Home</a>
            <a href="create-post.php" <?php echo basename($_SERVER['PHP_SELF']) == 'create-post.php' ? 'class="active"' : ''; ?>>+ Create</a>
            <a href="explore.php" <?php echo basename($_SERVER['PHP_SELF']) == 'explore.php' ? 'class="active"' : ''; ?>>Explore</a>
            <a href="generate_report.php" <?php echo basename($_SERVER['PHP_SELF']) == 'generate_report.php' ? 'class="active"' : ''; ?>>Generate Report</a>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
            <a href="user_management.php" <?php echo basename($_SERVER['PHP_SELF']) == 'user_management.php' ? 'class="active"' : ''; ?>>User Management</a>
            <a href="post-requests.php" <?php echo basename($_SERVER['PHP_SELF']) == 'post-requests.php' ? 'class="active"' : ''; ?>>
                <i class="fas fa-tasks"></i> Post Requests
            </a>
            <?php endif; ?>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 0): ?>
            <a href="my-posts.php" <?php echo basename($_SERVER['PHP_SELF']) == 'my-posts.php' ? 'class="active"' : ''; ?>>
                My Posts
            </a>
            <?php endif; ?>
            <div class="notification-dropdown">
                <div class="notification-icon" onclick="toggleNotificationDropdown()">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="notification-dropdown-content">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                    </div>
                    <div class="notification-list">
                        <!-- Notifications will be loaded here via AJAX -->
                    </div>
                    <div class="notification-footer">
                        <a href="notification.php">See All</a>
                    </div>
                </div>
            </div>
            <div class="user-dropdown">
                <div class="user-info" onclick="toggleUserDropdown()">
                    <?php
                    // Get the user's profile picture from the database
                    $user_id = $_SESSION['user_id'];
                    $query = "SELECT profile_picture, username FROM users WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    
                    if ($user && !empty($user['profile_picture'])) {
                        $avatar_url = $user['profile_picture'];
                    } else {
                        $avatar_url = 'assets/hero/v07_20@Shanks.png';
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Profile" class="user-avatar">
                </div>
                <div class="user-dropdown-content">
                    <a href="profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <!-- <a href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a> -->
                    <hr class="dropdown-divider">
                    <a href="#" onclick="showLogoutDialog()">
                        <i class="fas fa-sign-out-alt"></i> Log Out
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #365486;
            padding: 20px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar img {
            height: 50px;
            width: 50px;
        }

        .navbar h1 {
            color: #DCF2F1;
            font-size: 2rem;
            font-weight: 600;
            margin-left: 10px;
        }

        /* Add specific style for logo link to prevent hover effects */
        .logo-link {
            background-color: transparent !important;
        }
        
        .logo-link:hover {
            background-color: transparent !important;
            color: #DCF2F1 !important;
            transform: none !important;
        }

        .navbar a {
            color: #DCF2F1;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1rem;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 30px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #7FC7D9;
            color: #0F1035;
        }

        .navbar a.active {
            background-color: #1e3c72;
            color: #fff;
        }
        
    /* Dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
        }

        .dropdown-content a {
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-content a:last-child {
            border-bottom: none;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

    /* Toggle class for show/hide */
        .show {
            display: block;
        }

    /* User Dropdown Styles */
    .nav-links {
        display: flex;
        align-items: center;
    }

    .user-dropdown {
        position: relative;
        display: inline-block;
        margin-left: 15px;
    }

    .user-info {
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 8px;
        border-radius: 50px;
        transition: background-color 0.3s ease;
    }

    .user-info:hover {
        background-color: rgba(30, 60, 114, 0.2);
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0;
        border: none;
        padding: 0;
        display: block;
        flex-shrink: 0;
    }

    .user-name {
        display: none;
    }

    .fa-chevron-down {
        display: none;
    }

    .user-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 200px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        border-radius: 8px;
        padding: 8px 0;
        z-index: 1000;
    }

    .user-dropdown-content a {
        color: #333;
        padding: 12px 16px;
        text-decoration: none;
        display: flex;
        align-items: center;
        margin: 0;
        border-radius: 0;
    }

    .user-dropdown-content a i {
        margin-right: 10px;
        width: 20px;
    }

    .user-dropdown-content a:hover {
        background-color: #f8f9fa;
        color: #365486;
    }

    .dropdown-divider {
        margin: 8px 0;
        border: none;
        border-top: 1px solid #eee;
    }

    /* Show dropdown when active */
    .user-dropdown.active .user-dropdown-content {
        display: block;
    }

    /* Notification Dropdown Styles */
    .notification-dropdown {
        position: relative;
        display: inline-block;
        margin-left: 15px;
    }

    .notification-icon {
        cursor: pointer;
        padding: 10px;
        color: #DCF2F1;
        font-size: 1.2rem;
    }

    .notification-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: white;
        width: 1000px;
        max-height: 1000px;
        overflow-y: auto;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        z-index: 1000;
        margin-top: 10px;
        padding: 0;
        overflow-x: hidden;
    }

    .notification-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        background-color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .notification-header h3 {
        margin: 0;
        color: #000;
        font-size: 1.2rem;
        font-weight: 600;
        padding-left: 10px;
    }

    .notification-list {
        padding: 0;
        background-color: white;
    }

    .notification-item {
        padding: 20px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
    }

    .notification-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .notification-content {
        padding: 0 10px;
    }

    .notification-circle {
        width: 12px;
        height: 12px;
        border: 2px solid #666;
        border-radius: 50%;
        margin-top: 5px;
        flex-shrink: 0;
    }

    .notification-text {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 500;
        color: #000;
        margin-bottom: 4px;
        font-size: 0.95rem;
    }

    .notification-message {
        color: #666;
        margin-bottom: 2px;
        font-size: 0.9rem;
    }

    .notification-time {
        font-size: 0.8rem;
        color: #666;
        text-align: right;
    }

    .notification-footer {
        padding: 12px;
        text-align: center;
        border-top: 1px solid #eee;
        background-color: white;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .notification-footer a {
        color: #365486;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        margin: 0;
        padding: 0;
    }

    .notification-footer a:hover {
        text-decoration: underline;
        background-color: transparent;
        color: #365486;
    }

    /* Custom scrollbar */
    .notification-dropdown-content::-webkit-scrollbar {
        width: 4px;
    }

    .notification-dropdown-content::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notification-dropdown-content::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 2px;
    }

    .notification-dropdown-content::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
    </style>

    <script>
        function toggleUserDropdown() {
            const dropdown = document.querySelector('.user-dropdown');
            dropdown.classList.toggle('active');

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.remove('active');
                }
            });
        }

        function showLogoutDialog() {
            document.getElementById('logoutDialog').style.display = 'flex';
        }

        function toggleNotificationDropdown() {
            const dropdown = document.querySelector('.notification-dropdown');
            dropdown.classList.toggle('active');

            // Load notifications via AJAX when dropdown is opened
            if (dropdown.classList.contains('active')) {
                loadNotifications();
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.remove('active');
                }
            });
        }

        function loadNotifications() {
            const notificationList = document.querySelector('.notification-list');
            
            // AJAX call to fetch notifications
            fetch('get_notifications.php?limit=5')
                .then(response => response.text())
                .then(data => {
                    notificationList.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    notificationList.innerHTML = '<div class="notification-item">Error loading notifications</div>';
                });
        }
    </script>

<?php include 'components/dialog/logout.php'; ?>