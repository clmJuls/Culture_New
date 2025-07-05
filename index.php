<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KulturaBase</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="user/assets/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="user/assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="user/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="user/assets/favicon/favicon-16x16.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        /* Header Styles */
        .header {
            background-color: #365486;
            padding: 20px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container img {
            height: 50px;
            width: auto;
        }

        .logo {
            color: #DCF2F1;
            font-size: 2rem;
            font-weight: 600;
            text-decoration: none;
            margin-left: 10px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: #DCF2F1;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 30px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-links a:hover {
            background-color: #7FC7D9;
            color: #0F1035;
        }

        .auth-buttons a {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            color: #DCF2F1;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .login-btn:hover, .signup-btn:hover {
            background-color: #7FC7D9;
            color: #0F1035;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('https://socialstudieshelp.com/wp-content/uploads/2024/02/Exploring-the-Cultural-Diversity-of-Europe.webp');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
            padding: 0 5%;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background-color: #4a6ea5;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: transform 0.3s, background-color 0.3s;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            background-color: #1c3d8c;
        }

        /* Features Section */
        .features {
            padding: 5rem 5%;
            background-color: white;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            background-color: #f8f9fa;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #4a6ea5;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* About Section */
        .about {
            padding: 5rem 5%;
            background-color: white;
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .about-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .about-logo img {
            width: 300px;
            height: 250px;
            margin-bottom: 20px;
        }

        .about-title {
            font-size: 36px;
            font-weight: 700;
            color: #2C3E50;
            text-align: center;
            margin-bottom: 30px;
        }

        .about-description {
            font-size: 18px;
            line-height: 1.6;
            color: #555;
            text-align: justify;
            margin-bottom: 20px;
        }

        .about-list {
            list-style-type: none;
            padding-left: 20px;
            margin-bottom: 20px;
        }

        .about-list li {
            font-size: 18px;
            margin-bottom: 10px;
            line-height: 1.8;
        }

        /* Contact Section (Footer) */
        .footer {
            background-color: #365486;
            color: #DCF2F1;
            padding: 4rem 5%;
            text-align: center;
        }

        .contact-title {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: #DCF2F1;
        }

        .contact-description {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-links a {
            color: #DCF2F1;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: #7FC7D9;
        }

        .social-links {
            margin-bottom: 2rem;
        }

        .social-links a {
            color: #DCF2F1;
            font-size: 1.5rem;
            margin: 0 1rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #7FC7D9;
        }

        .copyright {
            color: #DCF2F1;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <div class="logo-container">
            <img src="user/assets/logo/logo.png" alt="Kulturifiko Logo">
                <a href="/" class="logo">KulturaBase</a>
            </div>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
            </div>
            <div class="auth-buttons">
                <a href="user/auth/login.php" class="login-btn">Login</a>
                <a href="user/auth/signup.php" class="signup-btn">Sign Up</a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Discover World Cultures</h1>
            <p>Join our community to explore, learn, and share cultural experiences from around the globe. Connect with people who share your passion for cultural diversity.</p>
            <a href="user/auth/signup.php" class="cta-button">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <h2 class="section-title">Why Choose KulturaBase?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-globe feature-icon"></i>
                <h3>Global Community</h3>
                <p>Connect with culture enthusiasts from around the world and share your unique perspectives.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-book-reader feature-icon"></i>
                <h3>Cultural Learning</h3>
                <p>Access rich cultural content, traditions, and stories from diverse communities worldwide.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-users feature-icon"></i>
                <h3>Interactive Experience</h3>
                <p>Engage in discussions, share experiences, and participate in cultural exchange programs.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-calendar-alt feature-icon"></i>
                <h3>Cultural Events</h3>
                <p>Stay updated with cultural events, festivals, and celebrations happening globally.</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="about-content">
            <div class="about-logo">
            <img src="user/assets/logo/logo.png" alt="Kulturifiko Logo">
            </div>
            <h1 class="about-title">Kulturifiko: Empowering Structural Equity in Tourism and the Hospital Industry</h1>
            <div class="about-description">
                <p>At Kulturifiko, we aim to foster structural equity by engaging travelers in cultural immersion that promotes sustainability within the tourism and hospital industries. Our initiative seeks to connect travelers with local communities in meaningful ways, ensuring that their journeys contribute to long-term positive impacts.</p>
                <p>Through our thoughtfully designed products:</p>
                <p class="about-list">
                    <li><strong>The Travel Journal:</strong> A personal space for travelers to capture their experiences, reflections, and cultural insights.</li>
                    <li><strong>The Travel Apparel:</strong> Eco-friendly and functional clothing, designed to enhance cultural immersion while promoting sustainability.</li>
                    <li><strong>The Travel Community (Mobile App + Website):</strong> A platform that connects travelers with local communities, offering guidance and support for authentic travel experiences.</li>
                </p>
                <p>With Kulturifiko, your travels will be guided by a genuine intention to learn, contribute, and make a positive impact.</p>
            </div>
        </div>
    </section>

    <!-- Contact Section (Footer) -->
    <footer class="footer" id="contact">
        <h2 class="contact-title">Contact Us</h2>
        <p class="contact-description">Have questions about Kulturifiko? We'd love to hear from you and help you start your cultural journey.</p>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#about">About Us</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
            <p class="copyright">&copy; 2025 KulturaBase. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
