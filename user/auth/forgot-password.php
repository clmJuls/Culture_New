<?php
require '../db_conn.php';
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../home.php'); // Redirect to home if logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KulturaBase</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('https://socialstudieshelp.com/wp-content/uploads/2024/02/Exploring-the-Cultural-Diversity-of-Europe.webp');
            background-size: cover;
            background-position: center;
            background-color: #f4e1d2;
            position: relative;
            overflow-x: hidden;
        }

        .main-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1;
            padding: 20px;
        }

        .forgot-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(136, 67, 60, 0.2);
            border: 2px solid #88433c;
            position: relative;
            overflow: hidden;
        }

        /* Filipino-inspired decorative elements */
        .forgot-container::before,
        .forgot-container::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background-image: 
                radial-gradient(circle at center, transparent 30%, #88433c11 30%),
                repeating-linear-gradient(45deg, #88433c11 0px, #88433c11 2px, transparent 2px, transparent 8px);
            border-radius: 50%;
        }

        .forgot-container::before {
            top: -75px;
            left: -75px;
        }

        .forgot-container::after {
            bottom: -75px;
            right: -75px;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            color: #88433c;
            text-shadow: 1px 1px 0 rgba(0,0,0,0.1);
        }

        p {
            color: #666;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .input-container {
            margin-bottom: 25px;
            position: relative;
        }

        .input-container input {
            width: 100%;
            padding: 15px;
            border: 2px solid #daa520;
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.9);
        }

        .input-container input:focus {
            border-color: #88433c;
            box-shadow: 0 0 0 3px rgba(136, 67, 60, 0.1);
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #88433c, #daa520);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #daa520, #88433c);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(136, 67, 60, 0.3);
        }

        /* Back to Login Link */
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(136, 67, 60, 0.2);
        }

        .login-link a {
            color: #88433c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #daa520;
        }

        /* Input placeholder styling */
        ::placeholder {
            color: #999;
            opacity: 0.8;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <?php include '../components/layout/auth/navbar.php'; ?>

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
    </div>
</body>
</html>
