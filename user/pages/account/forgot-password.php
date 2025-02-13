<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cultural Database</title>
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
            <img src="logo.png" alt="Kulturifiko Logo">
            <h1>Kulturifiko</h1>
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
            <a href="auth/login.php" class="active">Log Out</a>
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

    <!-- Main Content -->
    <div class="main-container">
    <div class="forgot-container">
        <h2>Forgot Password</h2>
        <p>Please enter your email address below, and we will send you a link to reset your password.</p>

        <br>

        <div class="input-container">
            <input type="email" placeholder="Enter your email address">
        </div>

        <button class="submit-btn">Send Reset Link</button>

        <br>

        <div class="login-link">
            <p>Remembered your password? <a href="login.php">Log In</a></p>
        </div>
    </div>

    <style>
         .main-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .forgot-container {
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

        /* Submit Button */
        .submit-btn {
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

        .submit-btn:hover {
            background-color: #1c3d8c;
        }

        /* Back to Login Link */
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
