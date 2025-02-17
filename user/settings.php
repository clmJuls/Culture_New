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

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $about = $_POST['about'];
    $location = $_POST['location'];
    $birthday = $_POST['birthday'];
    $website = $_POST['website'];
    $skills = $_POST['skills'];

    // Check for duplicate email or username (excluding current user's own email/username)
    $duplicate_check_query = "SELECT * FROM users WHERE (email = '$email' OR username = '$username') AND id != '$user_id'";
    $duplicate_check_result = $conn->query($duplicate_check_query);

    if ($duplicate_check_result->num_rows > 0) {
        echo "<script>
                alert('Email or username is already taken.');
                window.location.href = 'settings.php';
              </script>";
        exit();
    }

    // Update query with or without password
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_query = "
            UPDATE users 
            SET full_name = '$full_name', 
                email = '$email', 
                username = '$username', 
                password = '$hashed_password', 
                about = '$about', 
                location = '$location', 
                birthday = '$birthday', 
                website = '$website', 
                skills = '$skills' 
            WHERE id = '$user_id'";
    } else {
        $update_query = "
            UPDATE users 
            SET full_name = '$full_name', 
                email = '$email', 
                username = '$username', 
                about = '$about', 
                location = '$location', 
                birthday = '$birthday', 
                website = '$website', 
                skills = '$skills' 
            WHERE id = '$user_id'";
    }

    if ($conn->query($update_query) === TRUE) {
        echo "<script>
                alert('Information updated successfully!');
                window.location.href = 'home.php';
              </script>";
    } else {
        echo "<script>
                alert('An error occurred. Please try again later.');
                window.location.href = 'settings.php';
              </script>";
    }
}

// Fetch existing user information
$query = "SELECT full_name, email, username, about, location, birthday, website, skills FROM users WHERE id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $full_name = htmlspecialchars($user['full_name']);
    $email = htmlspecialchars($user['email']);
    $username = htmlspecialchars($user['username']);
    $about = htmlspecialchars($user['about']);
    $location = htmlspecialchars($user['location']);
    $birthday = htmlspecialchars($user['birthday']);
    $website = htmlspecialchars($user['website']);
    $skills = htmlspecialchars($user['skills']);
} else {
    echo "<script>
            alert('User not found.');
            window.location.href = 'home.php';
          </script>";
    exit();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <body style="margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f9f9f9;">
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
    <?php include 'components/layout/guest/navbar.php'; ?>

<!-- Search Section -->
<!-- <div class="search">
    <h1 id="category-heading">Cultural Database</h1> 
    <p>Your gateway to a world of cultural knowledge and discussions.</p>
    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Search articles, topics, or discussions..." />
            <button>Search</button>
        </div>
    </div>
</div> -->
<div style="margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f9f9f9;">
    <div style="
        padding: 20px;
        width: 400px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        background-color: white;
    ">
        <h2 style="margin-top: 80px; margin-bottom: 15px; font-size: 20px; text-align: center;">Update Personal Info</h2>
        <form method="POST" action="update_info.php" style="display: flex; flex-direction: column;">
            <div style="margin-bottom: 10px;">
                <input 
                    type="text" 
                    name="full_name" 
                    placeholder="Full Name" 
                    value="<?php echo $full_name; ?>" 
                    required 
                    style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 10px;">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email Address" 
                    value="<?php echo $email; ?>" 
                    required 
                    style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 10px;">
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username" 
                    value="<?php echo $username; ?>" 
                    required 
                    style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 10px;">
                <textarea 
                    name="about" 
                    placeholder="About" 
                    style="width: 100%; height: 60px; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;"><?php echo $about; ?></textarea>
            </div>
            <div style="margin-bottom: 10px;">
                <input 
                    type="text" 
                    name="location" 
                    placeholder="Location" 
                    value="<?php echo $location; ?>" 
                    style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 10px;">
                <input 
                    type="date" 
                    name="birthday" 
                    value="<?php echo $birthday; ?>" 
                    style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 10px;">
                <input 
                    type="url" 
                    name="website" 
                    placeholder="Website" 
                    value="<?php echo $website; ?>" 
                    style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="margin-bottom: 10px;">
                <textarea 
                    name="skills" 
                    placeholder="Skills (comma-separated)" 
                    style="width: 100%; height: 60px; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;"><?php echo $skills; ?></textarea>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <button 
                    type="submit" 
                    style="
                        width: 48%; 
                        padding: 10px; 
                        font-size: 16px; 
                        color: white; 
                        background-color: #28a745; 
                        border: none; 
                        border-radius: 4px; 
                        cursor: pointer;">
                    Save
                </button>
                <button 
                    type="button" 
                    style="
                        width: 48%; 
                        padding: 10px; 
                        font-size: 16px; 
                        color: white; 
                        background-color: #dc3545; 
                        border: none; 
                        border-radius: 4px; 
                        cursor: pointer;" 
                    onclick="window.location.href='settings.php';">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Search Section */
    .search {
        text-align: center;
        padding: 50px 20px;
        background: linear-gradient(45deg, #1e3c72, #2a5298)
    }

    .search h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #fff;
    }

    .search p {
        font-size: 1rem;
        margin-bottom: 20px;
        color: #fff;
    }

/* Search Bar */
    .search-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        border: 1px solid #ddd;
        border-radius: 50px;
        padding: 10px 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 60%;
        margin: 0 auto;
    }

    .search-bar input {
        border: none;
        outline: none;
        font-size: 1rem;
        flex: 1;
        padding: 10px;
        border-radius: 50px;
    }

    .search-bar input::placeholder {
        color: #aaa;
    }

    .search-bar button {
        background-color: #000;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 1rem;
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

<!-- Sidebar -->
<!-- <div class="sidebar">
    <div class="logo-section">
    </div>

        <div class="menu-section">
            <h3>Elements of Culture</h3>
            <div class="menu-item">
                <ul>
                    <li><a href="geography.php">Geography</a></li>
                    <li><a href="history.php">History</a></li>
                    <li><a href="demographics.php">Demographics</a></li>
                    <li><a href="culture.php">Culture</a></li>
                </ul>
            </div>

        <div class="menu-section">
            <h3>Learning Styles</h3>
            <div class="menu-item">
                <ul>
                    <li><input type="checkbox">Visual</li>
                    <li><input type="checkbox">Auditory & Oral</li>
                    <li><input type="checkbox">Read & Write</li>
                    <li><input type="checkbox">Kinesthetic</li>
                </ul>
            </div>

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
  </div> -->

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

.explore-btn {
    padding: 10px 20px;
    background: #00438f; 
    color: white;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 10px; 
    cursor: pointer;
    position: relative;
    display: inline-block;
    text-decoration: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
    transition: all 0.3s ease; 
}

.explore-btn:hover {
    background: #0056b3; 
    transform: translateY(-3px); 
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.explore-btn:active {
    transform: translateY(1px); 
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
}

.explore-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.5); 
}

.explore-btn:hover {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% {
        transform: translateY(-3px);
    }
    50% {
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(-3px);
    }
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