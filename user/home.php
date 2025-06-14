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
    <title>Kulturabase</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            .gallery-section h2,{
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
            <h1 id="category-heading">Welcome to Kulturabase</h1> 
            <p>Your gateway to a world of cultural knowledge and discussions.</p>
        </div>

        <!-- Hero Section -->
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

        <!-- About Section -->
        <section id="about" class="about">
            <div class="container">
                <h2>Who We Are</h2>
                <p>We are a platform that celebrates cultural diversity, creating a space to share stories, events, and experiences that connect us all.</p>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features">
            <div class="container">
                <h2>Discover Our Features</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-globe"></i>
                        <h3>Global Community</h3>
                        <p>Connect with people from different cultures worldwide.</p>
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

</body>
</html>