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
    <?php include 'components/layout/guest/navbar.php'; ?>


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
<?php include 'components/layout/guest/sidebar.php'; ?>

</body>
</head>
</html>

<?php
// Add database connection close here, after all components are included
$conn->close();
?>