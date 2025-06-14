<!-- Sidebar -->
<div class="sidebar">
    <div class="logo-section">
    </div>

    <!-- Add mobile nav menu section -->
    <div class="menu-section mobile-nav">
        <h3>Navigation</h3>
        <div class="menu-item">
            <ul>
                <li><a href="home.php" <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="create.php" <?php echo basename($_SERVER['PHP_SELF']) == 'create.php' ? 'class="active"' : ''; ?>>Create</a></li>
                <li><a href="explore.php" <?php echo basename($_SERVER['PHP_SELF']) == 'explore.php' ? 'class="active"' : ''; ?>>Explore</a></li>
            </ul>
        </div>
    </div>

    <div class="menu-section">
        <h3>Elements of Culture</h3>
        <div class="menu-item">
            <ul>
                <li><a href="geography.php" <?php echo basename($_SERVER['PHP_SELF']) == 'geography.php' ? 'class="active"' : ''; ?>>Geography</a></li>
                <li><a href="history.php" <?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'class="active"' : ''; ?>>History</a></li>
                <li><a href="demographics.php" <?php echo basename($_SERVER['PHP_SELF']) == 'demographics.php' ? 'class="active"' : ''; ?>>Demographics</a></li>
                <li><a href="culture.php" <?php echo basename($_SERVER['PHP_SELF']) == 'culture.php' ? 'class="active"' : ''; ?>>Culture</a></li>
            </ul>
        </div>
    </div>
    
    <div class="menu-section">
        <h3>Resources</h3>
        <div class="menu-item">
            <a href="about.php" <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'class="active"' : ''; ?>>About Kulturifiko</a>
        </div>
    </div>
</div>

<style>    .sidebar {
        position: fixed;
        top: 60px;
        left: -2px;
        width: 240px;
        height: calc(100vh - 60px);
        background-color: #365486;
        padding: 30px 0;
        z-index: 999;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow-y: auto;
        box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
        border-radius: 0 5px 5px 0;
        transition: all 0.3s ease-in-out;
    }

    .logo-section {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 15px 0;
        width: 100%;
    }

    .logo-section img {
        max-width: 100px;
        border-radius: 5px;
    }

    .menu-section {
        width: 100%;
        padding: 0 20px;
        margin-bottom: 20px;
    }

    .menu-section h3 {
        font-size: 15px;
        margin-bottom: 12px;
        color: #DCF2F1;
    }

    .menu-item {
        width: 100%;
        margin: 3px 0;
    }

    .menu-item ul {
        list-style: none;
        padding: 0;
        width: 100%;
    }

    .menu-item li {
        margin-bottom: 10px;
    }

    .menu-item a {
        color: #ffffff;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 30px;
        display: block;
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

    .menu-item span {
        margin-right: 8px;
    }

    /* Update media query with !important to ensure it takes precedence */    /* Medium screens and below */
    @media screen and (max-width: 1150px) {
        .sidebar {
            transform: translateX(-100%);
            left: 0;
        }

        .sidebar.sidebar-active {
            transform: translateX(0);
        }

        .hamburger-menu {
            display: flex;
        }

        /* Add overlay for sidebar */
        .sidebar::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }

        .sidebar.sidebar-active::after {
            opacity: 1;
            visibility: visible;
        }
    }

    /* Hide mobile nav by default */
    .mobile-nav {
        display: none;
    }

    /* Show mobile nav in sidebar on smaller screens */
    @media screen and (max-width: 768px) {
        .mobile-nav {
            display: block;
        }
    }

    /* Add responsive styles for the sidebar */
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
    }
</style>
