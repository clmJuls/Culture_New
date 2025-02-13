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
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            color: #4A4947;
            line-height: 1.6;
            padding-top: 80px;
        }
    </style>
    
    <!-- Navigation Bar -->
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <img src="logo.png" alt="Kulturifiko Logo">
            <h1>Kulturabase</h1>
        </div>
        <div>
            <a href="home.php">Home</a>
            <a href="create-post.php">+ Create</a>
            <a href="explore.php">Explore</a>
            <a href="notification.php">Notification</a>
            <div class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown()">Menu</a>
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="settings.php">Settings</a>
                </div>
            </div>
            <a href="generate_report.php">Generate Report</a>
            <a href="auth/login.php">Log Out</a>
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
            width: auto;
        }

        .navbar h1 {
            color: #DCF2F1;
            font-size: 2rem;
            font-weight: 600;
            margin-left: 10px;
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
    </style>

    <script>
        function toggleDropdown() {
            var dropdownContent = document.querySelector(".dropdown-content");
            dropdownContent.classList.toggle("show");
        }
    </script>

<!-- Geography Section -->
<section class="geography-hero">
    <div class="geography-content">
        <br><br><br><br><br><br><br><br><br>
        <h1 class="geography-title">The Role of Demographics in Culture</h1>
        <p class="geography-description">Demographics play a key role in shaping cultural, social, and economic structures. Understanding population trends, migration, and age distribution helps us understand the evolution of cultures across the world.</p>
        <br>
        <div class="geography-image">
            <img src="https://i.pinimg.com/736x/41/72/e7/4172e7a64cbec1b71766bb97025cc534.jpg" alt="Demographics and Culture" />
        </div>
    </div>
</section>

<section class="journals">
    <div class="container">
        <br><br><br><br><br>
        <h2>Demographics Journals</h2>
        <p>Demographics studies the structure of populations, including factors like age, gender, race, migration, and population density. Dive into journals that explore how these demographic factors influence culture and society.</p>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" placeholder="Search journals...">
            <button type="submit">Search</button>
        </div>

        <div class="journal-grid">
            <!-- Journal Card 1 -->
            <div class="journal-card">
                <img src="population.jpg" alt="Population Growth">
                <div class="journal-card-content">
                    <h3>Population Growth</h3>
                    <p>Explore how rapid population growth impacts urbanization, resources, and cultural evolution.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 2 -->
            <div class="journal-card">
                <img src="migration.jpg" alt="Migration Patterns">
                <div class="journal-card-content">
                    <h3>Migration Patterns</h3>
                    <p>Understand how migration, both voluntary and forced, shapes demographic changes and cultural exchange.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 3 -->
            <div class="journal-card">
                <img src="age.jpg" alt="Age Distribution">
                <div class="journal-card-content">
                    <h3>Age Distribution</h3>
                    <p>Learn about the effect of age distribution on economic and social structures, from the elderly to the youth.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 4 -->
            <div class="journal-card">
                <img src="urbanization.jpg" alt="Urbanization and Demographics">
                <div class="journal-card-content">
                    <h3>Urbanization and Demographics</h3>
                    <p>Explore how urbanization influences demographic patterns, such as population density and migration.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 5 -->
            <div class="journal-card">
                <img src="gender.jpg" alt="Gender Demographics">
                <div class="journal-card-content">
                    <h3>Gender Demographics</h3>
                    <p>Examine how gender distribution affects economic participation, political influence, and social roles.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 6 -->
            <div class="journal-card">
                <img src="urban.jpg" alt="Rural vs Urban Populations">
                <div class="journal-card-content">
                    <h3>Rural vs Urban Populations</h3>
                    <p>Study the demographic differences between rural and urban populations and their cultural implications.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 7 -->
            <div class="journal-card">
                <img src="ethnic.jpg" alt="Ethnic and Racial Demographics">
                <div class="journal-card-content">
                    <h3>Ethnic and Racial Demographics</h3>
                    <p>Explore the relationship between ethnic and racial diversity and cultural practices in different societies.</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>

            <!-- Journal Card 8 -->
            <div class="journal-card">
                <img src="aging.jpg" alt="Population Aging">
                <div class="journal-card-content">
                    <h3>Population Aging</h3>
                    <p>Investigate the effects of an aging population on social services, culture, and labor markets.</p>
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
    background-image: url('https://i.pinimg.com/736x/63/e7/08/63e708d10ecb834af8a193a4ec979f03.jpg'); 
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
    font-size: 2.5rem;
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
<div class="sidebar">
    <div class="logo-section">
    </div>

    <div class="menu-section">
        <h3>Elements of Culture</h3>
        <div class="menu-item">
            <ul>
                <li><a href="geography.php">Geography</a></li>
                <li><a href="history.php">History</a></li>
                <li><a href="demographics.php" class="active">Demographics</a></li>
                <li><a href="culture.php">Culture</a></li>
            </ul>
        </div>

        <!-- <div class="menu-section">
            <h3>Learning Styles</h3>
            <div class="menu-item">
                <ul>
                    <li><input type="checkbox">Visual</li>
                    <li><input type="checkbox">Auditory & Oral</li>
                    <li><input type="checkbox">Read & Write</li>
                    <li><input type="checkbox">Kinesthetic</li>
                </ul>
            </div> -->

        <div class="menu-section">
            <h3>Location</h3>
            <div class="menu-item">
                <a href="choose-loc.php"><span>+</span> Choose a location</a>
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
  /* Sidebar */
  .sidebar {
    position: fixed;
    top: 60px; 
    left: 0;
    width: 240px;  
    height: 100vh;
    background-color: #365486;
    padding-top: 30px;
    z-index: 999; 
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-y: auto;
    flex-grow: 1;
    box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
    border-radius: 0 5px 5px 0;
}

/* Logo Section */
.logo-section {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 15px;
  margin-bottom: 15px;
}

.logo-section img {
  max-width: 100px;
  border-radius: 5px;
}

/* Section Menus */
.menu-section {
  margin-bottom: 10px;
}

.menu-section h3 {
  font-size: 15px;
  margin-bottom: 8px;
  color: #DCF2F1;
}

/* Menu Items */
.menu-item {
  display: inline-block;
  align-items: center;
  justify-content: flex-start;
  margin: 3px 0;
  cursor: pointer;
  transition: background 0.2s ease;
  padding: 5px 5px;
  border-radius: 4px;
  color: #ffffff;
}

.menu-item a {
    color: #ffffff;
    text-decoration: none;
    font-size: .8rem;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 30px;
}

.menu-item a:hover {
    background-color: #7FC7D9;
    color: #0F1035;
}

.menu-item a.active {
    background-color: #1e3c72;
    color: #fff;
}

.menu-item ul {
    list-style: none;
    padding: 0;
}
  
.menu-item li {
    margin-bottom: 10px;
    font-size: .8rem;
}
  
input[type="checkbox"] {
    margin-right: 5px;
}

#chosen-location-container {
    margin-top: 20px; 
    display: block;
}

#chosen-location-container label {
    font-size: 12px; 
    color: #ffffff;
}
</style>

</body>
</head>
</html>