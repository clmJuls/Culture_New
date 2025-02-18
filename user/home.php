<?php
require 'db_conn.php';
session_start();

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
            padding-top: 60px;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
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

<!-- Search Section -->
<div class="search">
    <h1 id="category-heading">Cultural Database</h1> 
    <p>Your gateway to a world of cultural knowledge and discussions.</p>
    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Search articles, topics, or discussions..." />
            <button>Search</button>
        </div>
    </div>
</div>

<style>
/* Search Section */
    .search {
        background: linear-gradient(45deg, #1e3c72, #2a5298);
        padding: 40px 20px;
        text-align: center;
        width: 100%;
        margin-bottom: 30px;
    }

    .search h1 {
        font-size: 2.5rem;
        color: #fff;
        margin-bottom: 15px;
    }

    .search p {
        color: #fff;
        margin-bottom: 25px;
    }

    .search-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .search-bar {
        display: flex;
        gap: 10px;
        background: white;
        border-radius: 50px;
        padding: 5px;
        max-width: 600px;
        margin: 0 auto;
    }

    .search-bar input {
        flex: 1;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 16px;
    }

    .search-bar button {
        background: #000;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .search-bar button:hover {
        transform: scale(1.05);
    }
</style>

<script>
const categories = document.querySelectorAll('.categories span');

// Add click event listener to each category span
categories.forEach(category => {
    category.addEventListener('click', function() {
        // Remove 'active' class from all categories
        categories.forEach(cat => cat.classList.remove('active'));
        // Add 'active' class to the clicked category
        this.classList.add('active');
    });
});
</script>

<!-- Hero Section -->
<section id="home" class="hero">
  <div class="container hero-container">
      <div class="hero-content">
        <h1>Experience the Culture</h1>
        <p>Explore the beauty of global traditions and connect with communities worldwide. Start your journey with Kulturifiko today.</p>
        <a href="explore.php" class="cta-btn explore-btn">Start Exploring</a>
      </div>
      <br>
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
  
  <!-- Partners Section -->
  <section id="partners" class="partners-section">
    <div class="container">
      <h2>Our Partners</h2>
      <p>We collaborate with these incredible organizations to bring cultures together.</p>
      <div class="partners-grid">
        <img src="https://i.pinimg.com/736x/08/5a/d4/085ad448933875d5c3f3da93bfaac820.jpg" alt="UNICEF Logo">
        <img src="https://i.pinimg.com/736x/3f/89/b2/3f89b281abd80b6d92cc131652f5ddfc.jpg" alt="Red Cross Logo">
        <img src="https://i.pinimg.com/736x/80/5b/b9/805bb99df768a69afe3b83b7d4e3b9a6.jpg" alt="Smithsonian Logo">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSPvtIXri6znQj6CQiMAj7HyYFcw9LnGdFv2g&s" alt="Cultural Survival Logo">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ2Sin1P5o24LYo6gbinywSWVoPo68INzkvRA&s" alt="WHO Logo">
        <img src="https://www.cdnlogo.com/logos/n/6/national-geographic-channel.svg" alt="National Geographic Logo">
      </div>
    </div>
  </section>   

  <style>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* Hero Section */
.hero {
  padding: 40px;
  display: flex;
  align-items: center;
  gap: 40px;
  background: #f0f8ff;
}

.hero-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.hero-content {
  flex: 1;
  max-width: 600px;
}

.hero-content h1 {
  font-size: 2.5rem;
  color: #003366;
  margin-bottom: 20px;
}

.hero-content p {
  font-size: 1.2rem;
  color: #555555;
  margin-bottom: 30px;
  line-height: 1.6;
}

.cta-btn {
  background-color: #003366;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 1rem;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.cta-btn:hover {
  background-color: #00509e;
}

.hero-image {
  flex: 1;
  max-width: 500px;
}

.hero-image img {
  width: 100%;
  height: auto;
  border-radius: 10px;
}

/* Features Section */
.features {
  background-color: #f7fbff;
  padding: 40px 20px;
}

.features h2 {
  text-align: center;
  margin-bottom: 40px;
  color: #365486;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
}

.feature-card {
  background: white;
  padding: 30px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
}

.feature-card i {
  font-size: 2rem;
  color: #365486;
  margin-bottom: 20px;
}

/* Gallery Section */
.gallery-section,
.partners-section {
  padding: 60px 20px;
  background-color: #f0f8ff;
  text-align: center;
}

.container h2 {
  font-size: 2.5rem;
  color: #003366;
  margin-bottom: 20px;
}

.container p {
  font-size: 1.2rem;
  color: #555555;
  margin-bottom: 40px;
}

/* Gallery Section */
.gallery-section {
  background-color: #f0f8ff; /* Light blue background */
  padding: 50px 0;
  text-align: center;
}

.gallery-section h2 {
  font-size: 2.5rem;
  color: #007bff; /* Blue color */
  margin-bottom: 20px;
}

.gallery-section p {
  font-size: 1.2rem;
  color: #333;
  margin-bottom: 40px;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.gallery-grid img {
  width: 100%;
  height: auto;
  border-radius: 8px;
  transition: transform 0.3s ease;
}

.gallery-grid img:hover {
  transform: scale(1.05);
}

/* Partners Section */
.partners-section {
  background-color: #ffffff;
  padding: 50px 0;
  text-align: center;
}

.partners-section h2 {
  font-size: 2.5rem;
  color: #007bff;
  margin-bottom: 20px;
}

.partners-section p {
  font-size: 1.2rem;
  color: #333;
  margin-bottom: 40px;
}

.partners-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 20px;
  justify-items: center;
}

.partners-grid img {
  width: 120px;
  height: auto;
  transition: transform 0.3s ease;
}

.partners-grid img:hover {
  transform: scale(1.1);
}

/* Responsive breakpoints */
@media screen and (max-width: 1400px) {
    .hero {
        padding: 30px;
    }
}

@media screen and (max-width: 1200px) {
    .main-container {
        width: calc(100% - 240px);
    }
}

@media screen and (max-width: 992px) {
    .hero {
        flex-direction: column;
        text-align: center;
    }
    
    .hero-content, .hero-image {
        max-width: 100%;
    }
}

@media screen and (max-width: 768px) {
    .main-container {
        margin-left: 200px;
        width: calc(100% - 200px);
    }
    
    .search h1 {
        font-size: 2rem;
    }
    
    .search-bar {
        flex-direction: column;
        padding: 10px;
    }
    
    .search-bar input,
    .search-bar button {
        width: 100%;
    }
}

@media screen and (max-width: 576px) {
    .main-container {
        margin-left: 0;
        width: 100%;
        padding: 10px;
    }
    
    .hero {
        padding: 20px;
    }
    
    .search h1 {
        font-size: 1.8rem;
    }
}
  </style>

  <script>
    // Testimonials Slider
let currentSlide = 0;

const testimonials = document.querySelectorAll('.testimonial');
const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');

function showSlide(index) {
  testimonials.forEach((testimonial, i) => {
    testimonial.style.transform = `translateX(${100 * (i - index)}%)`;
  });
}

prevBtn.addEventListener('click', () => {
  currentSlide = (currentSlide - 1 + testimonials.length) % testimonials.length;
  showSlide(currentSlide);
});

nextBtn.addEventListener('click', () => {
  currentSlide = (currentSlide + 1) % testimonials.length;
  showSlide(currentSlide);
});

showSlide(currentSlide);

  </script>

<!-- Sidebar -->
<?php include 'components/layout/guest/sidebar.php'; ?>

<!-- Include Chat Widget -->
<?php include 'components/widgets/chat.php'; ?>

</body>
</head>
</html>