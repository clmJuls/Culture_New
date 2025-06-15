<!-- Sidebar -->
<div class="sidebar">
    <!-- Mobile User Profile Section -->
    <div class="mobile-user-section">
        <?php
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $query = "SELECT profile_picture, username FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'assets/hero/v07_20@Shanks.png';
                $username = $row['username'];
                ?>
                <div class="mobile-user-profile">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="mobile-avatar">
                    <span class="mobile-username"><?php echo htmlspecialchars($username); ?></span>
                    <div class="mobile-user-actions">
                        <a href="profile.php" class="mobile-profile-link">
                            <i class="fas fa-user"></i> View Profile
                        </a>
                        <a href="#" onclick="showLogoutDialog()" class="mobile-logout-link">
                            <i class="fas fa-sign-out-alt"></i> Log Out
                        </a>
                    </div>
                </div>
                <div class="mobile-notification-section">
                    <a href="notification.php" class="mobile-notification-link">
                        <i class="fas fa-bell"></i>
                        Notifications
                    </a>
                </div>

                <!-- Primary Navigation (Mobile Only) -->
                <div class="menu-section mobile-menu">
                    <h3>Main Menu</h3>
                    <div class="menu-item">
                        <ul>
                            <li><a href="home.php" <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'class="active"' : ''; ?>><i class="fas fa-home"></i> Home</a></li>
                            <li><a href="create-post.php" <?php echo basename($_SERVER['PHP_SELF']) == 'create-post.php' ? 'class="active"' : ''; ?>><i class="fas fa-plus"></i> Create</a></li>
                            <li><a href="explore.php" <?php echo basename($_SERVER['PHP_SELF']) == 'explore.php' ? 'class="active"' : ''; ?>><i class="fas fa-compass"></i> Explore</a></li>
                            <li><a href="my-posts.php" <?php echo basename($_SERVER['PHP_SELF']) == 'my-posts.php' ? 'class="active"' : ''; ?>><i class="fas fa-book"></i> My Posts</a></li>
                            <?php if (isset($_SESSION['isPremium']) && $_SESSION['isPremium'] == 1): ?>
                            <li><a href="generate_report.php" <?php echo basename($_SERVER['PHP_SELF']) == 'generate_report.php' ? 'class="active"' : ''; ?>><i class="fas fa-chart-bar"></i> Generate Report</a></li>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                            <li><a href="user_management.php" <?php echo basename($_SERVER['PHP_SELF']) == 'user_management.php' ? 'class="active"' : ''; ?>><i class="fas fa-users"></i> User Management</a></li>
                            <li><a href="post-requests.php" <?php echo basename($_SERVER['PHP_SELF']) == 'post-requests.php' ? 'class="active"' : ''; ?>><i class="fas fa-tasks"></i> Post Requests</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <div class="menu-section">
        <h3 style="margin-top: 15px;">Elements of Culture</h3>
        <div class="menu-item">
            <ul>
                <li><a href="geography.php" <?php echo basename($_SERVER['PHP_SELF']) == 'geography.php' ? 'class="active"' : ''; ?>>Geography</a></li>
                <li><a href="history.php" <?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'class="active"' : ''; ?>>History</a></li>
                <li><a href="demographics.php" <?php echo basename($_SERVER['PHP_SELF']) == 'demographics.php' ? 'class="active"' : ''; ?>>Demographics</a></li>
                <li><a href="culture.php" <?php echo basename($_SERVER['PHP_SELF']) == 'culture.php' ? 'class="active"' : ''; ?>>Culture</a></li>
            </ul>
        </div>
    </div>
    
    <div class="menu-section" style="margin-top: 0px;">
        <h3>Resources</h3>
        <div class="menu-item">
            <a href="about.php" <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'class="active"' : ''; ?>>About Kulturifiko</a>
        </div>
    </div>
</div>

<style>    /* Base sidebar styles */
    .sidebar {
        position: fixed;
        top: 0px;
        left: -2px;
        width: 240px;
        height: calc(100vh - 0px);
        background-color: #365486;
        padding: 20px 0;
        z-index: 999;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        overflow-y: auto;
        box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
        border-radius: 0 5px 5px 0;
        transition: all 0.3s ease-in-out;
    }

    /* Global overlay style */
    .sidebar::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: -1;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        pointer-events: none;
    }

    .sidebar.sidebar-active::before {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    /* Disable overlay on desktop */
    @media screen and (min-width: 1151px) {
        .sidebar::before {
            display: none;
        }
    }

    /* Desktop menu styles */
    .menu-section {
        width: 100%;
        padding: 0 20px;
        margin-bottom: 20px;
        margin-top: 80px; /* Added margin-top for large screens */
    }

    /* Adjustments for mobile */
    @media screen and (max-width: 1150px) {
        .menu-section {
            margin-top: 0; /* Remove margin-top on mobile */
        }
    }

    .menu-section h3 {
        color: #DCF2F1;
        font-size: 15px;
        margin-bottom: 15px;
    }

    .menu-item {
        width: 100%;
    }

    .menu-item ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu-item li {
        margin-bottom: 10px;
    }

    .menu-item a {
        color: #ffffff;
        text-decoration: none;
        font-size: 0.9rem;
        padding: 8px 16px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
    }

    .menu-item a:hover {
        background-color: #7FC7D9;
        color: #0F1035;
    }

    .menu-item a.active {
        background-color: #1e3c72;
        color: #fff;
    }

    /* Mobile sections hidden by default */
    .mobile-user-section,
    .menu-section.mobile-menu {
        display: none;
    }    @media screen and (max-width: 1150px) {
        .sidebar {
            transform: translateX(-100%);
            left: 0;
            width: 280px;
            background-color: #365486;
            z-index: 9999;
        }

        .sidebar.sidebar-active {
            transform: translateX(0);
        }

        /* Overlay styles */
        .sidebar::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            pointer-events: none;
        }

        .sidebar.sidebar-active::before {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* Show mobile sections */
        .mobile-user-section {
            display: block;
            width: 100%;
            padding: 0px;
            margin-bottom: 0px;
        }

        .mobile-user-profile {
            text-align: center;
            margin-bottom: 20px;
        }

        .mobile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #7FC7D9;
            background-color: #DCF2F1;
        }

        .mobile-username {
            color: #DCF2F1;
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 15px;
            display: block;
        }

        .mobile-user-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .mobile-notification-section {
            margin: 15px 0;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .menu-section.mobile-menu {
            display: block;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Style all links in mobile view */
        .mobile-user-section a,
        .menu-section a {
            color: #ffffff;
            text-decoration: none;
            padding: 0px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .mobile-user-section a:hover,
        .menu-section a:hover {
        }

    /* Overlay styles are defined globally */
    @media screen and (max-width: 1150px) {
        /* Mobile-specific styles handled elsewhere */
    }

        /* Ensure overlay is completely disabled on desktop */
        @media screen and (min-width: 1151px) {
            .sidebar::before {
                display: none;
            }
            
            .sidebar {
                transform: none !important;
            }

            body {
                overflow: auto !important;
            }
        }
    }
      /* Base styles for regular menu sections */
    .menu-section {
        width: 100%;
        padding: 0 20px;
        margin-bottom: 20px;
        display: block !important; /* Force display of regular menu sections */
    }

    /* Hide only mobile-specific sections by default */
    .mobile-user-section,
    .menu-section.mobile-menu {
        display: none !important;
    }

    /* Show mobile sections on mobile */
    @media screen and (max-width: 1150px) {
        .mobile-user-section,
        .menu-section.mobile-menu {
            display: block !important;
        }
    }

    /* Remove redundant mobile navigation menu */
    .menu-section.mobile-nav {
        display: none !important;
    }

    /* Medium screens and below */
    @media screen and (max-width: 1150px) {
        .sidebar {
            background-color: #000;
            transform: translateX(-100%);
            left: 0;
            width: 280px; /* Slightly wider on mobile for better touch targets */
        }

        .sidebar.sidebar-active {
            transform: translateX(0);
        }

        .hamburger-menu {
            display: flex;
        }        /* Add overlay for sidebar */
        .sidebar::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            pointer-events: none;
        }

        .sidebar.sidebar-active::after {
            opacity: 1;
            pointer-events: auto;
        }
    }

    @media screen and (max-width: 1024px) {
        .sidebar {
            width: 220px;
        }

        .menu-item a {
            font-size: 0.75rem;
            padding: 6px 14px;
        }
    }

    @media screen and (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            width: 280px;
            z-index: 1001;
        }

        .sidebar.sidebar-active {
            transform: translateX(0);
        }

        /* Show mobile nav in sidebar on smaller screens */
        .mobile-nav {
            display: block;
            margin-bottom: 30px;
        }

        .menu-section h3 {
            font-size: 14px;
        }

        .menu-item a {
            font-size: 0.8rem;
            padding: 10px 16px;
        }

        /* Add overlay when sidebar is active */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .sidebar-overlay.active {
            display: block;
        }
    }

    @media screen and (max-width: 480px) {
        .sidebar {
            width: 260px;
        }

        .menu-section {
            padding: 0 15px;
        }

        .menu-item a {
            font-size: 0.75rem;
            padding: 8px 12px;
        }
    }    /* Enhanced Mobile Styles */
    .mobile-user-section {
        display: none;
        width: 100%;
        padding: 0px;
        margin-bottom: 0px;
        border-radius: 10px;
    }

    .mobile-user-profile {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 20px;
    }

    .mobile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        border: 3px solid #7FC7D9;
    }

    .mobile-username {
        color: #DCF2F1;
        font-size: 1.2rem;
        font-weight: 500;
        margin-bottom: 15px;
    }

    .mobile-user-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    .mobile-profile-link,
    .mobile-logout-link,
    .mobile-notification-link {
        color: #DCF2F1;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .mobile-profile-link:hover,
    .mobile-logout-link:hover,
    .mobile-notification-link:hover {
        background-color: #7FC7D9;
        color: #0F1035;
    }

    .mobile-notification-section {
        width: 100%;
        margin: 20px 0;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Mobile Menu Styles */
    .menu-section.mobile-menu {
        display: block;
        width: 100%;
        padding: 0 20px;
    }

    .menu-section.mobile-menu h3 {
        font-size: 15px;
        margin-bottom: 15px;
        color: #DCF2F1;
    }

    .menu-section.mobile-menu .menu-item {
        width: 100%;
    }

    .menu-section.mobile-menu .menu-item ul {
        list-style: none;
        padding: 0;
    }

    .menu-section.mobile-menu .menu-item li {
        margin-bottom: 10px;
    }

    .menu-section.mobile-menu .menu-item a {
        color: #ffffff;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        padding: 10px 16px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
    }

    .menu-section.mobile-menu .menu-item a:hover {
        background-color: #7FC7D9;
        color: #0F1035;
    }

    .menu-section.mobile-menu .menu-item a.active {
        background-color: #1e3c72;
        color: #fff;
    }

    /* Global overlay style */
    .sidebar::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: -1;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        pointer-events: none;
    }

        .sidebar.sidebar-active::before {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
    }

    /* Ensure overlay is completely disabled on desktop */
    @media screen and (min-width: 1151px) {
        .sidebar::before {
            display: none;
        }
        
        .sidebar {
            transform: none !important;
        }

        body {
            overflow: auto !important;
        }
    }
</style>

<script>
    // Add window resize listener to handle sidebar state
    window.addEventListener('resize', function() {
        const sidebar = document.querySelector('.sidebar');
        if (window.innerWidth > 1150) {
            // Reset sidebar state on desktop
            sidebar.classList.remove('sidebar-active');
            document.body.style.overflow = '';
        }
    });

    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('sidebar-active');
        
        // Toggle body scroll
        if (sidebar.classList.contains('sidebar-active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    // Close sidebar when clicking outside
    document.addEventListener('click', (e) => {
        const sidebar = document.querySelector('.sidebar');
        const hamburgerMenu = document.querySelector('.hamburger-menu');
        
        if (!sidebar.contains(e.target) && !hamburgerMenu.contains(e.target)) {
            sidebar.classList.remove('sidebar-active');
            document.body.style.overflow = '';
        }
    });
</script>
