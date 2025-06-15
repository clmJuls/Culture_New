<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <body>

    <?php
    // Start the session at the beginning
    session_start();
    
    // Include database connection
    require_once 'db_conn.php';
    
    // Include navbar
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
        include 'components/layout/admin/navbar.php';
    } else {
        include 'components/layout/guest/navbar.php';
    }
    ?>
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
    overflow-x: hidden;
}

/* Color Palette */
:root {
    --primary-blue: #007bff;
    --light-blue: #e3f2fd;
    --white: #ffffff;
    --gray: #6c757d;
    --dark-blue: #0056b3;
}

/* Main Content */
#main-content {
    margin-left: 240px;
    padding: 40px;
    flex-grow: 1;
    max-width: 1300px;
    margin-right: auto;
    transition: margin-left 0.3s ease-in-out;
}

/* Header */
#header {
    text-align: center;
    margin-bottom: 50px;
    padding: 60px 40px;
    background: linear-gradient(135deg, #fff, #f8f9fa);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(54, 84, 134, 0.1);
    position: relative;
    overflow: hidden;
}

#header .logo img {
    max-width: 100%;
    height: auto;
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
    padding: 50px;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(54, 84, 134, 0.1);
    position: relative;
    border: 1px solid rgba(54, 84, 134, 0.1);
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
    padding: 50px;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(54, 84, 134, 0.1);
    position: relative;
    border: 1px solid rgba(54, 84, 134, 0.1);
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

/* Partners Section */
#partners {
    margin-bottom: 60px;
    padding: 50px;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(54, 84, 134, 0.1);
    position: relative;
    border: 1px solid rgba(54, 84, 134, 0.1);
}

#partners h2 {
    font-size: 28px;
    font-weight: 600;
    color: #2C3E50;
    margin-bottom: 40px;
    text-align: center;
}

.partners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    justify-items: center;
}

.partner-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
    max-width: 350px;
    display: flex;
    flex-direction: column;
}

.partner-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.partner-logo {
    margin-bottom: 20px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.partner-logo img {
    width: auto;
    height: 100%;
    max-height: 110px;
    object-fit: contain;
}

.partner-logo.pines-journey {
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 150px;
}

.logo-text {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1.2;
    margin: 0;
}

.text-primary {
    --bs-primary-rgb: 13, 110, 253;
    --bs-text-opacity: 1;
    color: rgba(var(--bs-primary-rgb), var(--bs-text-opacity)) !important;
}

.text-success {
    --bs-success-rgb: 25, 135, 84;
    --bs-text-opacity: 1;
    color: rgba(var(--bs-success-rgb), var(--bs-text-opacity)) !important;
}

.partner-card h3 {
    font-size: 24px;
    color: #2C3E50;
    margin-bottom: 15px;
}

.partner-card p {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
    line-height: 1.6;
    flex-grow: 1;
}

.partner-link {
    display: inline-block;
    padding: 10px 25px;
    background: #365486;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    transition: all 0.3s ease;
    margin-top: auto;
}

.partner-link:hover {
    background: #2a4268;
    transform: translateY(-2px);
}

/* Call to Action */
#cta {
    text-align: center;
    padding: 80px 40px;
    background: linear-gradient(135deg, #365486, #0F1035);
    color: #fff;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(15, 16, 53, 0.2);
    position: relative;
    overflow: hidden;
}

#cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('assets/pattern.png');
    opacity: 0.1;
    pointer-events: none;
}

#cta h2 {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 25px;
    font-family: 'Roboto', sans-serif;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#cta p {
    font-size: 18px;
    line-height: 1.8;
    margin-bottom: 35px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.cta-button {
    background-color: #fff;
    color: #0F1035;
    font-size: 18px;
    padding: 18px 40px;
    text-decoration: none;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: 2px solid transparent;
}

.cta-button:hover {
    background-color: transparent;
    color: #fff;
    border-color: #fff;
    transform: translateY(-2px);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Responsive Design */
@media screen and (max-width: 1150px) {
    #main-content {
        margin-left: 0;
        width: 100%;
        padding: 20px;
    }

    #header {
        padding: 40px 20px;
    }

    #header h1 {
        font-size: 28px;
    }

    .founder-details {
        gap: 30px;
    }

    #about-kulturifiko,
    #founder {
        padding: 30px;
    }
}

@media screen and (max-width: 768px) {
    #main-content {
        padding: 15px;
    }

    #header {
        padding: 30px 15px;
    }

    #header h1 {
        font-size: 24px;
    }

    .founder-photo {
        flex: 0 0 200px;
    }

    .founder-photo .founder-img {
        width: 200px;
        height: 200px;
    }

    .founder-name {
        font-size: 22px;
    }

    .founder-description,
    #about-kulturifiko p,
    #about-kulturifiko ul li {
        font-size: 16px;
    }

    #cta {
        padding: 40px 20px;
    }

    #cta h2 {
        font-size: 28px;
    }

    .cta-button {
        padding: 15px 30px;
        font-size: 16px;
    }
}

@media screen and (max-width: 480px) {
    #header h1 {
        font-size: 20px;
    }

    .founder-photo {
        flex: 0 0 150px;
    }

    .founder-photo .founder-img {
        width: 150px;
        height: 150px;
    }

    #founder h2,
    #about-kulturifiko h2 {
        font-size: 24px;
    }

    .founder-name {
        font-size: 20px;
    }

    .founder-description,
    #about-kulturifiko p,
    #about-kulturifiko ul li {
        font-size: 14px;
    }

    #cta h2 {
        font-size: 24px;
    }

    #cta p {
        font-size: 16px;
    }

    .cta-button {
        padding: 12px 25px;
        font-size: 14px;
    }

    #about-kulturifiko,
    #founder {
        padding: 20px;
    }
}
</style>

<div id="main-content">
    <header id="header">
        <div class="logo">
            <!-- Add the Kulturifiko logo her1e -->
             <br>
             <br>
            <img src="assets/logo/AboutLogo.jfif" alt="Kulturifiko Logo" width="700" height="450">
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

    <section id="partners">
        <h2>Our Partners</h2>
        <div class="partners-grid">
            <div class="partner-card">
                <div class="partner-logo">
                    <img src="assets/logo/AboutLogo.jfif" alt="Kulturifiko Logo">
                </div>
                <h3>Kulturifiko</h3>
                <p>Empowering cultural connections and sustainable tourism experiences.</p>
                <a href="#" class="partner-link">Learn More</a>
            </div>
            
            <div class="partner-card">
                <div class="partner-logo pines-journey">
                    <h3 class="logo-text">
                        <span class="text-primary">Pines'</span>
                        <span class="text-success">Journey</span>
                    </h3>
                </div>
                <p>Explore the beauty of travel with our trusted partner.</p>
                <a href="https://pinesjourney.infinityfreeapp.com/index.php" class="partner-link" target="_blank">Visit Website</a>
            </div>
        </div>
    </section>

    <section id="cta">
        <h2>Join the Kulturifiko Movement</h2>
        <p>Be part of a global community that prioritizes sustainable and meaningful 
        <br>travel experiences. Together, we can create lasting change in the tourism and hospital industries!</p>
        <a href="join-us.html" class="cta-button">Join Us</a>
    </section>
</div>


<style>
/* Main Content */
#main-content {
    margin-left: 240px;
    padding: 40px;
    flex-grow: 1;
    max-width: 1300px;
    margin-right: auto;
    transition: margin-left 0.3s ease-in-out;
}

/* Header */
#header {
    text-align: center;
    margin-bottom: 50px;
    padding: 60px 40px;
    background: linear-gradient(135deg, #fff, #f8f9fa);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(54, 84, 134, 0.1);
    position: relative;
    overflow: hidden;
}

#header .logo img {
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
    padding: 50px;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(54, 84, 134, 0.1);
    position: relative;
    border: 1px solid rgba(54, 84, 134, 0.1);
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
    padding: 50px;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(54, 84, 134, 0.1);
    position: relative;
    border: 1px solid rgba(54, 84, 134, 0.1);
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

/* Partners Section */
#partners {
    margin-bottom: 60px;
    padding: 50px;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(54, 84, 134, 0.1);
    position: relative;
    border: 1px solid rgba(54, 84, 134, 0.1);
}

#partners h2 {
    font-size: 28px;
    font-weight: 600;
    color: #2C3E50;
    margin-bottom: 40px;
    text-align: center;
}

.partners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    justify-items: center;
}

.partner-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
    max-width: 350px;
    display: flex;
    flex-direction: column;
}

.partner-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.partner-logo {
    margin-bottom: 20px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.partner-logo img {
    width: auto;
    height: 100%;
    max-height: 110px;
    object-fit: contain;
}

.partner-logo.pines-journey {
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 150px;
}

.logo-text {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1.2;
    margin: 0;
}

.text-primary {
    --bs-primary-rgb: 13, 110, 253;
    --bs-text-opacity: 1;
    color: rgba(var(--bs-primary-rgb), var(--bs-text-opacity)) !important;
}

.text-success {
    --bs-success-rgb: 25, 135, 84;
    --bs-text-opacity: 1;
    color: rgba(var(--bs-success-rgb), var(--bs-text-opacity)) !important;
}

.partner-card h3 {
    font-size: 24px;
    color: #2C3E50;
    margin-bottom: 15px;
}

.partner-card p {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
    line-height: 1.6;
    flex-grow: 1;
}

.partner-link {
    display: inline-block;
    padding: 10px 25px;
    background: #365486;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    transition: all 0.3s ease;
    margin-top: auto;
}

.partner-link:hover {
    background: #2a4268;
    transform: translateY(-2px);
}

/* Call to Action */
#cta {
    text-align: center;
    padding: 80px 40px;
    background: linear-gradient(135deg, #365486, #0F1035);
    color: #fff;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(15, 16, 53, 0.2);
    position: relative;
    overflow: hidden;
}

#cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('assets/pattern.png');
    opacity: 0.1;
    pointer-events: none;
}

#cta h2 {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 25px;
    font-family: 'Roboto', sans-serif;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#cta p {
    font-size: 18px;
    line-height: 1.8;
    margin-bottom: 35px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.cta-button {
    background-color: #fff;
    color: #0F1035;
    font-size: 18px;
    padding: 18px 40px;
    text-decoration: none;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: 2px solid transparent;
}

.cta-button:hover {
    background-color: transparent;
    color: #fff;
    border-color: #fff;
    transform: translateY(-2px);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Responsive Design */
@media screen and (max-width: 1150px) {
    #main-content {
        margin-left: 0;
        width: 100%;
        padding: 20px;
    }

    #header {
        padding: 40px 20px;
    }

    #header h1 {
        font-size: 28px;
    }

    .founder-details {
        gap: 30px;
    }

    #about-kulturifiko,
    #founder {
        padding: 30px;
    }
}

@media screen and (max-width: 768px) {
    #main-content {
        padding: 15px;
    }

    #header {
        padding: 30px 15px;
    }

    #header h1 {
        font-size: 24px;
    }

    .founder-photo {
        flex: 0 0 200px;
    }

    .founder-photo .founder-img {
        width: 200px;
        height: 200px;
    }

    .founder-name {
        font-size: 22px;
    }

    .founder-description,
    #about-kulturifiko p,
    #about-kulturifiko ul li {
        font-size: 16px;
    }

    #cta {
        padding: 40px 20px;
    }

    #cta h2 {
        font-size: 28px;
    }

    .cta-button {
        padding: 15px 30px;
        font-size: 16px;
    }
}

@media screen and (max-width: 480px) {
    #header h1 {
        font-size: 20px;
    }

    .founder-photo {
        flex: 0 0 150px;
    }

    .founder-photo .founder-img {
        width: 150px;
        height: 150px;
    }

    #founder h2,
    #about-kulturifiko h2 {
        font-size: 24px;
    }

    .founder-name {
        font-size: 20px;
    }

    .founder-description,
    #about-kulturifiko p,
    #about-kulturifiko ul li {
        font-size: 14px;
    }

    #cta h2 {
        font-size: 24px;
    }

    #cta p {
        font-size: 16px;
    }

    .cta-button {
        padding: 12px 25px;
        font-size: 14px;
    }

    #about-kulturifiko,
    #founder {
        padding: 20px;
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