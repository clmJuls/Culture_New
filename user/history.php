<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
            padding-top: 80px;
        }
    </style>
</head>
<body>
    <?php
    // Start the session at the beginning
    session_start();
    
    // Include database connection
    require_once 'db_conn.php';
    
    // Include navbar
    include 'components/layout/guest/navbar.php';
    ?>

<!-- Geography Section -->
<section class="geography-hero">
    <div class="geography-content">
        <br><br><br><br><br><br><br><br><br>
        <h1 class="geography-title">The Role of History in Culture</h1>
        <p class="geography-description">History shapes cultural practices, beliefs, and the social structures that influence our world today. From ancient civilizations to modern history, explore the impact of past events on the present.</p>
        <br>
        <div class="geography-image">
            <img src="https://i.pinimg.com/736x/f7/fb/96/f7fb9635d8975aa88d0f18124f862e55.jpg" alt="History and Culture" />
        </div>
    </div>
</section>

<section class="journals">
    <div class="container">
        <br><br><br><br>
        <h2>History Journals</h2>
        <p>History explores the past and its influence on the present. Dive into journals that focus on how historical events, movements, and people have shaped the cultures we live in today.</p>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" placeholder="Search journals...">
            <button type="submit">Search</button>
        </div>

        <div class="journal-grid">
            <!-- Journal Card 1 -->
            <div class="journal-card">
                <img src="assets/journal/ancient.jpg" alt="Ancient Civilizations">
                <div class="journal-card-content">
                    <h3>Ancient Civilizations</h3>
                    <p>Discover the rise and fall of ancient civilizations and their cultural legacies.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 2 -->
            <div class="journal-card">
                <img src="assets/journal/wars.jpg" alt="World Wars">
                <div class="journal-card-content">
                    <h3>The World Wars</h3>
                    <p>Learn about the impact of the two World Wars on global culture and politics.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 3 -->
            <div class="journal-card">
                <img src="assets/journal/renaissance.jpg" alt="Renaissance">
                <div class="journal-card-content">
                    <h3>The Renaissance</h3>
                    <p>Explore the cultural and intellectual rebirth that changed the course of history.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 4 -->
            <div class="journal-card">
                <img src="assets/journal/movements.jpg" alt="Revolutionary Movements">
                <div class="journal-card-content">
                    <h3>Revolutionary Movements</h3>
                    <p>Understand how revolutions, from the French Revolution to civil rights movements, have shaped modern societies.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 5 -->
            <div class="journal-card">
                <img src="assets/journal/colonialiam.jpg" alt="Colonialism">
                <div class="journal-card-content">
                    <h3>The Age of Colonialism</h3>
                    <p>Examine the lasting effects of colonialism on cultures around the world.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 6 -->
            <div class="journal-card">
                <img src="assets/journal/cultural_evolution.jpg" alt="Cultural Evolution">
                <div class="journal-card-content">
                    <h3>Cultural Evolution</h3>
                    <p>Explore the evolution of cultural practices, beliefs, and traditions through history.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 7 -->
            <div class="journal-card">
                <img src="assets/journal/figures.jpg" alt="Historic Figures">
                <div class="journal-card-content">
                    <h3>Influential Historical Figures</h3>
                    <p>Learn about the lives of historical figures who shaped the course of human history.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 8 -->
            <div class="journal-card">
                <img src="assets/journal/heritage.jpg" alt="Cultural Heritage">
                <div class="journal-card-content">
                    <h3>Cultural Heritage</h3>
                    <p>Discover how historical events and practices influence modern cultural heritage preservation.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
 /* Geography Hero Section */
.geography-hero {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 60vh;
    background-image: url('https://i.pinimg.com/736x/17/97/45/179745a3ef2508595c382f04fee5451a.jpg');
    background-size: cover;
    background-position: center;
    color: white;
    text-align: center;
    position: relative;
}

.geography-hero::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); 
}

.geography-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    padding: 20px;
}

.geography-title {
    font-size: 2.8rem;
    margin-bottom: 20px;
    font-weight: bold;
}

.geography-description p {
    font-size: 1.2rem;
    margin-bottom: 30px;
}

.geography-image img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin-top: 20px;
}

/* Search Bar Styles */
.search-bar {
    width: 50%;
    margin: 20px auto;
    text-align: center;
}

.search-bar input {
    width: 80%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-right: 10px;
}

.search-bar button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #00196d;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.search-bar button:hover {
    background-color: #187fd3;
}


/* Journals Section */
.journals {
    background-color: #f4f4f4;
    padding: 50px 20px;
    font-family: Arial, sans-serif;
}

.journals .container {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
}

.journals h2 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 10px;
}

.journals p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 30px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.journal-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.journal-card {
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
}

.journal-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.journal-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
}

.journal-card-content {
    padding: 15px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.journal-card h3 {
    font-size: 1.1rem;
    color: #333;
    margin: 0 0 10px;
    font-weight: bold;
}

.journal-card p {
    font-size: 0.9rem;
    color: #666;
    margin: 0 0 15px;
}

.journal-card .read-more {
    text-decoration: none;
    color: #ffffff;
    background-color: #007bff;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.875rem;
    text-align: center;
    display: inline-block;
    transition: background-color 0.3s;
}

.journal-card .read-more:hover {
    background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 768px) {
    .journal-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .journal-grid {
        grid-template-columns: 1fr;
    }
}

/* Call to Action Section */
.cta-section {
    text-align: center;
    margin: 40px 0;
}

.cta-button {
    font-size: 1.1rem;
    padding: 15px 30px;
    background-color: #022597;
    color: white;
    text-decoration: none;
    border-radius: 30px;
    margin: 10px;
    transition: background-color 0.3s ease;
}

.cta-button:hover {
    background-color: #0052b1;
}
</style>

<!-- Sidebar -->
<?php include 'components/layout/guest/sidebar.php'; ?>
</body>
</html>