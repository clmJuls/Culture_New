<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to edit your profile.');
            window.location.href = '../auth/login.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$query = "SELECT username, location, website, profile_picture FROM users WHERE id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = htmlspecialchars($user['username']);
    $location = htmlspecialchars($user['location']);
    $website = htmlspecialchars($user['website']);
    $profile_picture = htmlspecialchars($user['profile_picture']);
} else {
    echo "<script>
            alert('User not found.');
            window.location.href = 'edit-profile.php';
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $new_location = $_POST['location'];
    $new_website = $_POST['website'];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($_FILES['profile_picture']['name']);
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_file)) {
            $profile_picture = $upload_file;
        }
    }

    // Update user data
    $update_query = "UPDATE users SET 
                        username = '$new_username', 
                        location = '$new_location', 
                        website = '$new_website', 
                        profile_picture = '$profile_picture' 
                    WHERE id = '$user_id'";

    if ($conn->query($update_query) === TRUE) {
        echo "<script>
                alert('Profile updated successfully!');
                window.location.href = 'edit-profile.php';
              </script>";
    } else {
        echo "<script>
                alert('An error occurred. Please try again later.');
              </script>";
    }
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
           <img src="assets/logo/logo.png "alt="Kulturifiko Logo">
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
            <a href="#" onclick="handleLogout()">Log Out</a>
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
        function handleLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'auth/logout.php';
            }
        }
    </script>


<div class="edit-profile-container" style="max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">
    <h2 style="text-align: center; margin-bottom: 20px;">Edit Profile</h2>



    <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
      <div class="profile-picture" style="text-align: center; margin-bottom: 20px;">
          <div class="profile-img" style="display: inline-block;">
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" id="profile-img" class="profile-img-preview" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
          </div>
          <label for="profile-picture-upload" class="upload-btn" style="display: block; margin-top: 10px; cursor: pointer; color: #007bff;">Change Picture</label>
          <input type="file" id="profile-picture-upload" name="profile_picture" accept="image/*" onchange="previewImage(event)" style="display: none;">
      </div>
      <div class="form-group" style="margin-bottom: 15px;">
        <label for="username" style="display: block; margin-bottom: 5px;">Username</label>
        <input type="text" id="username" name="username" placeholder="Username..." value="<?php echo $username; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
      </div>

      <div class="form-group" style="margin-bottom: 15px;">
        <label for="location" style="display: block; margin-bottom: 5px;">Location</label>
        <input type="text" id="location" name="location" placeholder="Location..." value="<?php echo $location; ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
      </div>

      <div class="form-group" style="margin-bottom: 15px;">
        <label for="website" style="display: block; margin-bottom: 5px;">Website</label>
        <input type="url" id="website" name="website" placeholder="Website..." value="<?php echo $website; ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
      </div>

      <button type="submit" class="save-btn" style="padding: 10px; background-color: #28a745; color: white; font-size: 16px; border: none; border-radius: 4px; cursor: pointer;">Save Changes</button>
    </form>
</div>

  <style>
    /* Edit Profile Container */
.edit-profile-container {
  max-width: 900px;
  margin: 40px auto;
  background-color: #fff;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Cover Photo Section */
.cover-photo-container {
  position: relative;
  width: 100%;
  height: 250px;
  margin-bottom: 20px;
}

.cover-photo {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.cover-img-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}


/* Change Cover Photo Button */
.upload-cover-btn {
  position: absolute;
  bottom: 15px;
  left: 15px;
  padding: 12px 25px;
  background: linear-gradient(45deg, #4e8bde, #56c8f9); /* Gradient background */
  color: #fff;
  font-size: 16px;
  font-weight: bold;
  border-radius: 25px;
  cursor: pointer;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  text-transform: uppercase;
  text-decoration: none;
}

.upload-cover-btn:hover {
  background: linear-gradient(45deg, #56c8f9, #4e8bde);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
  transform: translateY(-2px); /* Hover effect: move the button up */
}

.upload-cover-btn:active {
  transform: translateY(2px); /* Active effect: move the button down */
}

.cover-photo input[type="file"] {
  display: none;
}

.profile-picture {
  text-align: center;
  margin-bottom: 30px;
  position: relative;
}

.profile-img {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  position: relative;
  margin: 0 auto;
  border: 4px solid #fff;  /* White border around profile picture */
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-img:hover {
  transform: scale(1.05); /* Slight zoom effect on hover */
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15); /* More prominent shadow on hover */
}

.upload-btn {
  font-size: 16px;
  color: #007bff;
  cursor: pointer;
  text-decoration: underline;
  display: block;
  margin-top: 10px;
}

.upload-btn:hover {
  color: #0056b3;
}

.profile-picture input[type="file"] {
  display: none;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 16px;
  color: #555;
  margin-bottom: 8px;
}

.form-group input,
.form-group textarea {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background-color: #f9f9f9;
}

.form-group input[type="file"] {
  display: none;
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: #007bff;
  outline: none;
}

.save-btn {
  padding: 12px 20px;
  font-size: 16px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.save-btn:hover {
  background-color: #0056b3;
}

@media screen and (max-width: 768px) {
  .edit-profile-container {
    padding: 30px;
  }

  .form-group input,
  .form-group textarea {
    font-size: 14px;
  }

  .save-btn {
    padding: 10px 18px;
  }
}
  </style>

    <script>
function previewCoverImage(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById('cover-img').src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById('profile-img').src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }
</script>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo-section">
    </div>

        <div class="menu-section">
            <h3>Elements of Culture</h3>
            <div class="menu-item">
                <ul>
                <li><a href="geography.php">Geography</a></li>
                <li><a href="history.php" class="active">History</a></li>
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