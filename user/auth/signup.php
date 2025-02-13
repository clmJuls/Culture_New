<?php
require '../db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password validation: must be at least 8 characters, contain uppercase, lowercase, number, and special character
    $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

    if (!preg_match($password_pattern, $password)) {
        echo "<script>
                alert('Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.');
                window.location.href = 'signup.php';
              </script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>
                alert('Passwords do not match!');
                window.location.href = 'signup.php';
              </script>";
        exit();
    }

    // Check for duplicate email
    $email_check_query = "SELECT * FROM users WHERE email = '$email'";
    $email_check_result = $conn->query($email_check_query);
    if ($email_check_result->num_rows > 0) {
        echo "<script>
                alert('Email is already taken!');
                window.location.href = 'signup.php';
              </script>";
        exit();
    }

    // Check for duplicate username
    $username_check_query = "SELECT * FROM users WHERE username = '$username'";
    $username_check_result = $conn->query($username_check_query);
    if ($username_check_result->num_rows > 0) {
        echo "<script>
                alert('Username is already taken!');
                window.location.href = 'signup.php';
              </script>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $query = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$hashed_password')";

    if ($conn->query($query) === TRUE) {
        echo "<script>
                alert('Sign Up Successful!');
                window.location.href = 'login.php'; // Redirect to login page
              </script>";
    } else {
        echo "<script>
                alert('Something went wrong. Please try again later.');
                window.location.href = 'signup.php';
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
    <style>
        /* General */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('https://socialstudieshelp.com/wp-content/uploads/2024/02/Exploring-the-Cultural-Diversity-of-Europe.webp');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <img src="../logo.png" alt="Kulturifiko Logo">
            <h1>Kulturabase</h1>
        </div>
        <div>
            <a href="home.php">Home</a>
            <a href="create-post.php">+ Create</a>
            <a href="notification.php">Notification</a>
            <div class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown()">Menu</a>
                <div class="dropdown-content">
                    <a href="#">Profile</a>
                    <a href="settings.php">Settings</a>
                </div>
            </div>
            <a href="login.php" class="active">Log Out</a>
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

     <!-- Main Sign-Up Section -->
     <div class="main-container">
        <div class="signup-container">
            <h2>Sign Up</h2>
            <form action="signup.php" method="POST">
                <div class="input-container">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="input-container">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-container">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-container">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <button type="submit" class="signup-btn">Sign Up</button>
            </form>
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Log In</a></p>
            </div>
        </div>
    <style>
        .main-container {
           flex-grow: 1;
           display: flex;
           justify-content: center;
           align-items: center;
       }

       .signup-container {
           background-color: rgba(255, 255, 255, 0.8);
           padding: 40px;
           border-radius: 15px;
           width: 100%;
           max-width: 400px;
           box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
       }

       h2 {
           font-size: 2rem;
           margin-bottom: 20px;
           text-align: center;
           color: #333;
       }

       .input-container {
           margin-bottom: 15px;
           position: relative;
       }

       .input-container input {
           width: 100%;
           padding: 15px;
           border: 2px solid #ddd;
           border-radius: 10px;
           font-size: 1rem;
           outline: none;
           transition: border 0.3s ease;
           background-color: #f9f9f9;
       }

       .input-container input:focus {
           border-color: #4a6ea5;
       }

       .signup-btn {
           width: 100%;
           padding: 15px;
           background-color: #4a6ea5;
           color: white;
           border: none;
           border-radius: 10px;
           font-size: 1.1rem;
           cursor: pointer;
           transition: background-color 0.3s ease;
       }

       .signup-btn:hover {
           background-color: #1c3d8c;
       }

       .login-link {
           text-align: center;
           margin-top: 20px;
       }

       .login-link a {
           color: #4a6ea5;
           text-decoration: none;
       }

       .login-link a:hover {
           text-decoration: underline;
       }
   </style>
</body>
</html>
