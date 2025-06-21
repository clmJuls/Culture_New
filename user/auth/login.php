<?php
require '../db_conn.php';
require 'SessionManager.php';
session_start();

// Initialize SessionManager
$sessionManager = new SessionManager($conn);

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../home.php'); // Redirect to home if logged in
    exit();
}

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
    // Create access token and session
    $access_token = $sessionManager->createSession($user['id']);
    
    // Start session and set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['isAdmin'] = $user['isAdmin'];
    $_SESSION['isPremium'] = $user['isPremium'];
    $_SESSION['access_token'] = $access_token;

    // Remember me functionality
    if (isset($_POST['remember_me'])) {
        setcookie('username', $user['username'], time() + (86400 * 30), "/"); // 30 days
        setcookie('access_token', $access_token, time() + (86400 * 30), "/"); // 30 days
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
            min-height: 100vh;
            display: flex;
            background-image: url('https://socialstudieshelp.com/wp-content/uploads/2024/02/Exploring-the-Cultural-Diversity-of-Europe.webp');
            background-size: cover;
            background-position: center;
            flex-direction: column;
            background-color: #f4e1d2; /* Warm, earthy tone common in Filipino textiles */
            position: relative;
            overflow-x: hidden;
        }

        /* Filipino-inspired background pattern */
       

        .main-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .login-container {
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
        .login-container::before,
        .login-container::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background-image: 
                radial-gradient(circle at center, transparent 30%, #88433c11 30%),
                repeating-linear-gradient(45deg, #88433c11 0px, #88433c11 2px, transparent 2px, transparent 8px);
            border-radius: 50%;
        }

        .login-container::before {
            top: -75px;
            left: -75px;
        }

        .login-container::after {
            bottom: -75px;
            right: -75px;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            color: #88433c; /* Deep red-brown color common in Filipino art */
            text-shadow: 1px 1px 0 rgba(0,0,0,0.1);
        }

        .input-container {
            margin-bottom: 15px;
            position: relative;
        }

        .input-container input {
            width: 100%;
            padding: 15px;
            border: 2px solid #daa520; /* Golden color inspired by Filipino traditional ornaments */
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

        .input-container .error-message {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 5px;
            padding: 5px 0;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            margin-bottom: 15px;
            color: #555;
        }

        .remember-forgot label {
            color: #666;
        }

        .remember-forgot a {
            color: #88433c;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .remember-forgot a:hover {
            color: #daa520;
        }

        .login-btn {
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

        .login-btn:hover {
            background: linear-gradient(135deg, #daa520, #88433c);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(136, 67, 60, 0.3);
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(136, 67, 60, 0.2);
        }

        .signup-link a {
            color: #88433c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
            color: #daa520;
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

        /* Input placeholder styling */
        ::placeholder {
            color: #999;
            opacity: 0.8;
        }

        /* Checkbox styling */
        input[type="checkbox"] {
            accent-color: #88433c;
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <?php include '../components/layout/auth/navbar.php'; ?>

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

</body>
</html>
