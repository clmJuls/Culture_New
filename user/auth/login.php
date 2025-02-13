<?php
require '../db_conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST['username'];  // This could be either a username or email
    $password = $_POST['password'];
    $username_error = '';
    $password_error = '';

    // First validate if the username/email exists
    if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
        $query = "SELECT * FROM users WHERE email = ?";
    } else {
        $query = "SELECT * FROM users WHERE username = ?";
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Username/email doesn't exist
        $_SESSION['username_error'] = 'Username does not exist!';
        header('Location: login.php');
        exit();
    }

    // If we get here, the username exists, now check password
    $user = $result->fetch_assoc();
    if (!password_verify($password, $user['password'])) {
        // Invalid password
        $_SESSION['password_error'] = 'Invalid password!';
        header('Location: login.php');
        exit();
    }

    // If we get here, both username and password are valid
    // Start session and set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['isAdmin'] = $user['isAdmin'];

    // Remember me functionality
    if (isset($_POST['remember_me'])) {
        setcookie('username', $user['username'], time() + (86400 * 30), "/"); // 30 days
    }

    header('Location: ../home.php');
    exit();
}

// Get any error messages
$username_error = isset($_SESSION['username_error']) ? $_SESSION['username_error'] : '';
$password_error = isset($_SESSION['password_error']) ? $_SESSION['password_error'] : '';

// Clear the error messages from session
unset($_SESSION['username_error']);
unset($_SESSION['password_error']);

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
            <a href="../explore.php">Explore</a>
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
        <div class="login-container">
            <h2>Login</h2>
            <form method="POST" autocomplete="off">
                <div class="input-container">
                    <input type="text" name="username" placeholder="Username" required>
                    <?php if (!empty($username_error)): ?>
                        <div class="error-message"><?php echo $username_error; ?></div>
                    <?php endif; ?>
                </div>
                <div class="input-container">
                    <input type="password" name="password" placeholder="Password" required>
                    <?php if (!empty($password_error)): ?>
                        <div class="error-message"><?php echo $password_error; ?></div>
                    <?php endif; ?>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox" name="remember_me"> Remember me</label>
                    <a href="forgot-password.php">Forgot password?</a>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <div class="signup-link">
                <p>Don't have an account? <a href="signup.php">Create Account</a></p>
            </div>
        </div>
    </div>

    <style>
         .main-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
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

        .input-container .error-message {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 5px;
            padding: 5px 0;
        }

        .input-container input:focus {
            border-color: #4a6ea5;
        }

        /* When there's an error, change the input border color */
        .input-container input.error {
            border-color: #dc3545;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .remember-forgot label {
            color: #777;
        }

        .remember-forgot a {
            color: #4a6ea5;
            text-decoration: none;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .login-btn {
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

        .login-btn:hover {
            background-color: #1c3d8c;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
        }

        .signup-link a {
            color: #4a6ea5;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</body>
</html>
