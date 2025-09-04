<?php
session_start();
require_once 'db_conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to update your information.');
            window.location.href = '../user/auth/login.php';
          </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KulturaBase</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin: 0;
            padding: 60px 0 0 0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Main Content Wrapper */
        .main-content-wrapper {
            margin-left: 240px;
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
            width: calc(100% - 240px);
        }

        /* Container */
        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Search Section */
        .search {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            padding: 80px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .search::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .search h1 {
            font-size: 4.5rem;
            color: #fff;
            margin-bottom: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }

        .search p {
            color: #fff;
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
            line-height: 1.8;
            position: relative;
            z-index: 2;
        }

        /* Hero Section */
        .hero {
            padding: 80px 0;
            background: #f0f8ff;
        }

        .hero-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 60px;
        }

        .hero-content {
            flex: 1;
            max-width: 600px;
        }

        .hero-content h1 {
            font-size: 3rem;
            color: #003366;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .hero-image {
            flex: 1;
            max-width: 500px;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .hero-image img:hover {
            transform: translateY(-10px);
        }

        /* Feature Cards */
        .features {
            padding: 80px 0;
            background: #fff;
        }

        .features h2 {
            text-align: center;
            font-size: 2.5rem;
            color: #003366;
            margin-bottom: 50px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-card i {
            font-size: 2.5rem;
            color: #365486;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            color: #003366;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Add chart styles */
        .chart-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .chart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .chart-row {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-box {
            flex: 1;
            min-width: 0;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .chart-title {
            text-align: center;
            font-size: 2.5rem;
            color: #003366;
            margin-bottom: 40px;
            font-weight: 600;
        }

        .line-charts {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Gallery Section */
        .gallery-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .gallery-section h2 {
            text-align: center;
            font-size: 2.5rem;
            color: #003366;
            margin-bottom: 20px;
        }

        .gallery-section p {
            text-align: center;
            color: #666;
            max-width: 700px;
            margin: 0 auto 40px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .gallery-grid img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .gallery-grid img:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* Buttons */
        .cta-btn {
            display: inline-block;
            padding: 15px 30px;
            background: #365486;
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(54, 84, 134, 0.3);
        }

        .cta-btn:hover {
            background: #2a4268;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(54, 84, 134, 0.4);
        }

        .button-container {
            margin-top: 30px;
        }

        /* Responsive Design */
        @media screen and (max-width: 1150px) {
            .main-content-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .search {
                padding: 60px 20px;
            }

            .search h1 {
                font-size: 3.5rem;
            }

            .hero {
                padding: 60px 0;
            }

            .hero-container {
                flex-direction: column;
                text-align: center;
                gap: 40px;
            }

            .hero-content, .hero-image {
                max-width: 100%;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .features-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media screen and (max-width: 768px) {
            .search {
                padding: 40px 20px;
            }

            .search h1 {
                font-size: 2.5rem;
            }

            .search p {
                font-size: 1.1rem;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .feature-card {
                padding: 30px 20px;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }

            .gallery-grid img {
                height: 200px;
            }
        }

        @media screen and (max-width: 480px) {
            .search {
                padding: 30px 15px;
            }

            .search h1 {
                font-size: 2rem;
            }

            .hero {
                padding: 40px 0;
            }

            .hero-content h1 {
                font-size: 1.8rem;
            }

            .features h2,
            .gallery-section h2 {
                font-size: 2rem;
            }

            .feature-card {
                padding: 25px 15px;
            }

            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .cta-btn {
                padding: 12px 25px;
                font-size: 0.9rem;
            }
        }

        @media screen and (max-width: 992px) {
            .chart-row {
                flex-direction: column;
            }
            
            .chart-box {
                width: 100%;
            }
        }

        /* Custom Scrollbar */
        .main-content-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        .main-content-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .main-content-wrapper::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .main-content-wrapper::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Add About section styles */
        .about {
            padding: 80px 0;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            position: relative;
            overflow: hidden;
        }

        .about::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(54, 84, 134, 0.05) 0%, rgba(54, 84, 134, 0) 70%);
            border-radius: 50%;
            transform: translate(200px, -200px);
        }

        .about-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: flex-start;
        }

        .about-content h2 {
            font-size: 2.5rem;
            color: #003366;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .about-content p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 25px;
            line-height: 1.8;
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 40px;
        }

        .stat-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-box i {
            font-size: 2rem;
            color: #365486;
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 2.5rem;
            color: #365486;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-label {
            color: #666;
            font-size: 1.1rem;
            font-weight: 500;
            line-height: 1.4;
        }

        .about-visual {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .main-image-container {
            position: relative;
            flex-shrink: 0;
        }

        .main-image {
            width: 100%;
            max-width: 350px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-left: 100px;
        }

        .floating-card {
            position: absolute;
            background: white;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 500px;
        }

        .floating-card.top {
            top: 30px;
            right: 40px;
            padding-right: 60px;
            padding-left: 20px;
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .floating-card.bottom {
            bottom: 30px;
            left: -30px;
            padding: 20px;
        }

        /* Founders Section */
        .founders-section {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .founders-section h3 {
            font-size: 1.5rem;
            color: #003366;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .founders-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .founder-card {
            text-align: center;
            padding: 15px;
            border-radius: 15px;
            background: #f8f9fa;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .founder-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .founder-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #365486;
        }

        .founder-info h4 {
            font-size: 0.9rem;
            color: #003366;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .founder-info p {
            font-size: 0.8rem;
            color: #666;
            margin: 0;
        }

        .floating-card i {
            font-size: 2rem;
            color: #365486;
        }

        .floating-card-content h4 {
            color: #003366;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .floating-card-content p {
            color: #666;
            font-size: 0.9rem;
        }

        @media screen and (max-width: 992px) {
            .about-container {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .about-visual {
                order: -1;
            }

            .floating-card {
                position: static !important;
                margin: 20px 0;
                transform: none !important;
            }

            .founders-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
            }

            .founder-card img {
                width: 70px;
                height: 70px;
            }
        }

        @media screen and (max-width: 576px) {
            .about-stats {
                grid-template-columns: 1fr;
            }

            .founders-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .founder-card {
                display: flex;
                align-items: center;
                text-align: left;
                gap: 15px;
                padding: 15px;
            }

            .founder-card img {
                width: 60px;
                height: 60px;
                margin-bottom: 0;
            }

            .founder-info h4 {
                font-size: 1rem;
            }

            .founder-info p {
                font-size: 0.85rem;
            }
        }
    </style>
    
    <!-- Navigation Bar -->
    <?php 
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
        include 'components/layout/admin/navbar.php';
    } else {
        include 'components/layout/guest/navbar.php';
    }
    ?>

    <!-- Main content wrapper -->
    <div class="main-content-wrapper">
        <!-- Search Section -->
        <div class="search">
            <h1 id="category-heading">Welcome to KulturaBase</h1> 
            <p>Your gateway to a world of cultural knowledge and discussions.</p>
        </div>

        <!-- Hero Section -->
                        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                    <section class="chart-section">
                        <div class="container">
                            <h2 class="chart-title">Analytics Overview</h2>
                            <div class="chart-container">
                                <div class="chart-row">
                                    <div class="chart-box">
                                        <canvas id="learningStylesChart"></canvas>
                                    </div>
                                    <div class="chart-box line-charts">
                                        <canvas id="postsPerWeekChart"></canvas>
                                        <canvas id="postsPerMonthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php else: ?>
                    <section id="home" class="hero">
                        <div class="container hero-container">
                            <div class="hero-content">
                                <h1>Experience the Culture</h1>
                                <p>Explore the beauty of global traditions and connect with communities worldwide. Start your journey with Kulturifiko today.</p>
                                <div class="button-container">
                                    <a href="explore.php" class="cta-btn explore-btn">Start Exploring</a>
                                </div>
                            </div>
                            <div class="hero-image">
                                <img src="https://i.pinimg.com/736x/be/8c/6c/be8c6cbf1d049825ffd2df0442f0c66b.jpg" alt="Cultural Exploration">
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

        <!-- About Section -->
        <section id="about" class="about">
            <div class="container">
                <div class="about-container">
                    <div class="about-content">
                        <h2>Empowering Cultural Exchange</h2>
                        <p>Welcome to KulturaBase, where we believe in the power of sharing and preserving cultural knowledge. Our platform serves as a bridge connecting people, traditions, and stories from around the world.</p>
                        <p>Through our innovative learning approaches and community-driven content, we're creating a space where cultural understanding flourishes and diverse perspectives are celebrated.</p>

                        <div class="about-stats">
                            <div class="stat-box">
                                <i class="fas fa-book-open"></i>
                                <div class="stat-label">The Travel Journals</div>
                            </div>
                            <div class="stat-box">
                                <i class="fa-solid fa-shirt"></i>
                                <div class="stat-label">The Travel Apparel</div>
                            </div>
                            <div class="stat-box">
                                <i class="fa-solid fa-plane"></i>
                                <div class="stat-label">The Travel Community</div>
                            </div>
                            <div class="stat-box">
                                <i class="fas fa-graduation-cap"></i>
                                <div class="stat-label">4 Learning Styles</div>
                            </div>
                        </div>
                    </div>

                    <div class="about-visual">
                        <div class="main-image-container">
                            <img src="assets/founder.jpg" alt="Cultural Exchange" class="main-image">
                            <div class="floating-card top">
                                <i class="fas fa-globe-americas"></i>
                                <div class="floating-card-content">
                                    <h4>Kulturifiko</h4>
                                    <p>CEO, founder</p>
                                </div>
                            </div>
                            <div class="floating-card bottom">
                                <i class="fas fa-users"></i>
                                <div class="floating-card-content">
                                    <h4>Ms. Luisa Baguiwet</h4>
                                    <p>Our partner</p>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="founders-section">
                            <h3>Meet Our Founders</h3>
                            <div class="founders-grid">
                                <div class="founder-card">
                                    <img src="./assets/founders/ritz.jpg" alt="Founder 1">
                                    <div class="founder-info">
                                        <h4>Ritz Paul Madueño</h4>
                                        <p>Project Leader</p>
                                    </div>
                                </div>
                                <div class="founder-card">
                                    <img src="./assets/founders/kim.jpg" alt="Founder 2">
                                    <div class="founder-info">
                                        <h4>Kimberly Cumaling</h4>
                                        <p>Developer</p>
                                    </div>
                                </div>
                                <div class="founder-card">
                                    <img src="./assets/founders/kath.jpg" alt="Founder 3">
                                    <div class="founder-info">
                                        <h4>Kathleen Mae Duco</h4>
                                        <p>Co-Developer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features">
            <div class="container">
                <h2>Discover Our Features</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-globe"></i>
                        <h3>Philippine Community</h3>
                        <p>Connect with people from different cultures nationwide.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-book"></i>
                        <h3>Cultural Learning</h3>
                        <p>Learn about diverse traditions and customs.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-users"></i>
                        <h3>Interactive Sharing</h3>
                        <p>Share and discuss cultural experiences.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Learning Styles Chart Section -->
        

        <!-- Gallery Section -->
        <section id="gallery" class="gallery-section">
            <div class="container">
                <h2>Gallery</h2>
                <p>Explore snapshots of cultural moments from around the world.</p>
                <div class="gallery-grid">
                    <img src="https://i.pinimg.com/736x/76/b5/c2/76b5c226f33b37337934bce7ab9c0159.jpg" alt="Cultural Image 1">
                    <img src="https://i.pinimg.com/736x/1d/c6/2f/1dc62ff8ecf9107fe08af2ca13b1a2f5.jpg" alt="Cultural Image 2">
                    <img src="https://i.pinimg.com/736x/fd/d3/68/fdd36868ad02196d0f17c2bc2e022d43.jpg" alt="Cultural Image 3">
                    <img src="https://i.pinimg.com/736x/ef/d2/64/efd264f714d553dda14755cb78034699.jpg" alt="Cultural Image 4">
                </div>
            </div>
        </section>  
        
    </div> <!-- End of main-content-wrapper -->

    <!-- Sidebar -->
    <?php include 'components/layout/guest/sidebar.php'; ?>

    <!-- Include Chat Widget -->
    <?php include 'components/widgets/chat.php'; ?>

    <script>
        // Function to format dates
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        }

        // Function to format months
        function formatMonth(monthString) {
            const date = new Date(monthString);
            return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        }

        // Create Learning Styles Chart
        fetch('handlers/get_learning_styles_stats.php')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    const data = result.data;
                    const labels = data.map(item => item.style);
                    const values = data.map(item => item.count);

                    new Chart(document.getElementById('learningStylesChart'), {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Number of Posts',
                                data: values,
                                backgroundColor: [
                                    '#365486', // Visual
                                    '#7FC7D9', // Auditory & Oral
                                    '#DCF2F1', // Read & Write
                                    '#0F1035'  // Kinesthetic
                                ],
                                hoverOffset: 4,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            family: "'Poppins', sans-serif"
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Learning Styles Distribution',
                                    font: {
                                        size: 16,
                                        family: "'Poppins', sans-serif",
                                        weight: '600'
                                    },
                                    padding: {
                                        bottom: 20
                                    }
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error fetching learning styles data:', error));

        // Create Posts Per Week Chart
        fetch('handlers/get_posts_per_week.php')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    const dates = result.data.dates.map(formatDate);
                    const counts = result.data.counts;

                    new Chart(document.getElementById('postsPerWeekChart'), {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'Posts per Day',
                                data: counts,
                                fill: false,
                                borderColor: '#365486',
                                tension: 0.1,
                                pointBackgroundColor: '#7FC7D9',
                                pointBorderColor: '#365486',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            family: "'Poppins', sans-serif"
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Posts Activity (Last 7 Days)',
                                    font: {
                                        size: 16,
                                        family: "'Poppins', sans-serif",
                                        weight: '600'
                                    },
                                    padding: {
                                        bottom: 20
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error fetching posts per week data:', error));

        // Create Posts Per Month Chart
        fetch('handlers/get_posts_per_month.php')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    const months = result.data.months.map(formatMonth);
                    const counts = result.data.counts;

                    new Chart(document.getElementById('postsPerMonthChart'), {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Posts per Month',
                                data: counts,
                                fill: false,
                                borderColor: '#0F1035',
                                tension: 0.1,
                                pointBackgroundColor: '#DCF2F1',
                                pointBorderColor: '#0F1035',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            family: "'Poppins', sans-serif"
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Posts Activity (Last 6 Months)',
                                    font: {
                                        size: 16,
                                        family: "'Poppins', sans-serif",
                                        weight: '600'
                                    },
                                    padding: {
                                        bottom: 20
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error fetching posts per month data:', error));
    </script>

</body>
</html>