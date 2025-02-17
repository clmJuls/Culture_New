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
            <span>🔗</span>
            <a href="#">About Kulturifiko</a>
        </div>
    </div>
</div>

<style>
    .sidebar {
        position: fixed;
        top: 60px;
        left: 0;
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
        transition: transform 0.3s ease-in-out;
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

    /* Update media query with !important to ensure it takes precedence */
    @media screen and (max-width: 768px) {
        .sidebar {
            transform: translateX(-240px) !important;
        }

        .sidebar.sidebar-active {
            transform: translateX(0) !important;
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
</style>
