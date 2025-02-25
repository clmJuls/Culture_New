<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <body>
    <style>
    /* General */
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

/* Body */
body {
    background-color: #f5f5f5;
    color: #333;
    display: flex;
    flex-direction: row;
    font-family: 'Roboto', sans-serif;
}

        /* Color Palette */
:root {
  --primary-blue: #007bff;
  --light-blue: #e3f2fd;
  --white: #ffffff;
  --gray: #6c757d;
  --dark-blue: #0056b3;
}
    </style>
    
    <!-- Navigation Bar -->
    <?php
    // Start the session at the beginning
    session_start();
    
    // Include database connection
    require_once 'db_conn.php';
    
    // Include navbar
    include 'components/layout/guest/navbar.php';
    ?>

<div id="main-content">
    <header id="header">
        <div class="logo">
            <!-- Add the Kulturifiko logo here -->
             <br>
             <br>
            <img src="https://scontent.xx.fbcdn.net/v/t1.15752-9/462567709_1724925585031052_4490126238712417040_n.png?_nc_cat=109&ccb=1-7&_nc_sid=0024fc&_nc_ohc=S9UP-k68ScQQ7kNvgFZCz8p&_nc_oc=AdhqsycKNWmfMJxi48g5Fy381arVHvFKzkd8WqJXUGXUQ2t9Orbh2q4-BGPMNRsbEbh54Op-n9ymVEFEvccPGyv1&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent.xx&oh=03_Q7cD1gF9Fo63JcswcyKmjwBkVdKsQTDi1B7q6lvUIG4qW7KcFQ&oe=67CF02AA" alt="Kulturifiko Logo" width="300" height="250">
        </div>
        <h1>Kulturifiko: Empowering Structural Equity in Tourism and the Hospital Industry</h1>
    </header>

    <section id="founder">
        <h2>Meet Our Founder</h2>
        <div class="founder-details">
            <div class="founder-photo">
                <img src="assets/founder.jpg" alt="Founder" class="founder-img">
            </div>
            <div class="founder-info">
                <p class="founder-name">Luisa Barangan Baguiwet</p>
                <p class="founder-description">
                    Luisa Barangan Baguiwet is a visionary in the sustainable tourism space, passionate about cultural immersion and its role in strengthening communities. With a background in sustainable development and global travel, she founded Kulturifiko to bridge the gap between travelers and local communities while also addressing the challenges within the hospital industry.
                </p>
            </div>
        </div>
    </section>

    <section id="about-kulturifiko">
        <h2>About Kulturifiko</h2>
        <p>
            At Kulturifiko, we aim to foster structural equity by engaging travelers in cultural immersion that promotes sustainability within the tourism and hospital industries. Our initiative seeks to connect travelers with local communities in meaningful ways, ensuring that their journeys contribute to long-term positive impacts.
        </p>
        <p>
            Through our thoughtfully designed products:
        </p>
        <ul>
            <li><strong>The Travel Journal:</strong> A personal space for travelers to capture their experiences, reflections, and cultural insights.</li>
            <li><strong>The Travel Apparel:</strong> Eco-friendly and functional clothing, designed to enhance cultural immersion while promoting sustainability.</li>
            <li><strong>The Travel Community (Mobile App + Website):</strong> A platform that connects travelers with local communities, offering guidance and support for authentic travel experiences.</li>
        </ul>
        <p>
            With Kulturifiko, your travels will be guided by a genuine intention to learn, contribute, and make a positive impact.
        </p>
    </section>

    <section id="cta">
        <h2>Join the Kulturifiko Movement</h2>
        <p>Be part of a global community that prioritizes sustainable and meaningful travel experiences. Together, we can create lasting change in the tourism and hospital industries!</p>
        <a href="join-us.html" class="cta-button">Join Us</a>
    </section>
</div>


<style>
/* Main Content */
#main-content {
    margin-left: 400px;
    padding: 40px;
    flex-grow: 1;
    max-width: 1300px;
    margin-right: 200px;
}

/* Header */
#header {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

#header .logo img {
    border-radius: 50%;
    margin-bottom: 20px;
}

#header h1 {
    font-size: 36px;
    font-weight: 700;
    color: #2C3E50;
    margin-top: 20px;
    font-family: 'Roboto', sans-serif;
}

/* Founder Section */
#founder {
    margin-bottom: 60px;
    padding: 40px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

#founder h2 {
    font-size: 28px;
    color: #2C3E50;
    text-align: center;
    margin-bottom: 20px;
    font-family: 'Roboto', sans-serif;
}

.founder-details {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.founder-photo {
    flex: 0 0 250px;
    display: flex;
    justify-content: center;
    border-radius: 50%;
    overflow: hidden;
}

.founder-photo .founder-img {
    width: 250px;
    height: 250px;
    object-fit: cover;
}

.founder-info {
    max-width: 600px;
    padding: 20px;
    text-align: center;
}

.founder-name {
    font-size: 26px;
    font-weight: 600;
    color: #2C3E50;
    margin-bottom: 10px;
}

.founder-description {
    font-size: 18px;
    line-height: 1.6;
    color: #555;
}

/* About Section */
#about-kulturifiko {
    margin-bottom: 60px;
    padding: 40px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

#about-kulturifiko h2 {
    font-size: 28px;
    font-weight: 600;
    color: #2C3E50;
    margin-bottom: 20px;
    text-align: center;
}

#about-kulturifiko p {
    font-size: 18px;
    line-height: 1.6;
    color: #555;
    text-align: justify;
    margin-bottom: 20px;
}

#about-kulturifiko ul {
    list-style-type: none;
    padding-left: 20px;
    margin-bottom: 20px;
}

#about-kulturifiko ul li {
    font-size: 18px;
    margin-bottom: 10px;
    line-height: 1.8;
}

/* Call to Action */
#cta {
    text-align: center;
    padding: 60px 0;
    background-color: #0056b3;
    color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

#cta h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 20px;
    font-family: 'Roboto', sans-serif;
}

#cta p {
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 30px;
}

.cta-button {
    background-color: #0F1035;
    color: #fff;
    font-size: 18px;
    padding: 15px 30px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 500;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.cta-button:hover {
    background-color: #365486;
    color: #fff;
}

/* Responsive Design */
@media (max-width: 768px) {
    #founder-details {
        flex-direction: column;
        text-align: center;
    }

    #cta {
        padding: 40px 20px;
    }

    #main-content {
        padding: 30px;
    }
}
</style>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo-section">
    </div>

        <div class="menu-section">
            <h3>Elements of Culture</h3>
            <div class="menu-item">
                <ul>
                    <li><a href="geography.html">Geography</a></li>
                    <li><a href="history.html">History</a></li>
                    <li><a href="demographics.html">Demographics</a></li>
                    <li><a href="culture.html">Culture</a></li>
                </ul>
            </div>
        
    <div class="menu-section">
      <h3>Resources</h3>
      <div class="menu-item">
        <span>🔗</span>
        <a href="about.html">About Kulturifiko</a>
      </div>
    </div>
  </div>

<!-- Sidebar -->
<?php include 'components/layout/guest/sidebar.php'; ?>

<!-- Include Chat Widget -->
<?php include 'components/widgets/chat.php'; ?>

</body>
</head>
</html>