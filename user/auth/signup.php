<?php
require '../db_conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Password validation
    $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    if (!preg_match($password_pattern, $password)) {
        $errors['password'] = 'Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match!';
    }

    // Check for duplicate email
    $email_check_query = "SELECT * FROM users WHERE email = '$email'";
    $email_check_result = $conn->query($email_check_query);
    if ($email_check_result->num_rows > 0) {
        $errors['email'] = 'Email is already taken!';
    }

    // Check for duplicate username
    $username_check_query = "SELECT * FROM users WHERE username = '$username'";
    $username_check_result = $conn->query($username_check_query);
    if ($username_check_result->num_rows > 0) {
        $errors['username'] = 'Username is already taken!';
    }

    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the new user
        $query = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$hashed_password')";
        
        if ($conn->query($query) === TRUE) {
            $_SESSION['success_message'] = 'Sign Up Successful!';
            header('Location: login.php');
            exit();
        } else {
            $errors['general'] = 'Something went wrong. Please try again later.';
        }
    }

    // Store errors and form data in session
    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        $_SESSION['signup_data'] = ['email' => $email, 'username' => $username];
        header('Location: signup.php');
        exit();
    }
}

// Get stored errors and data, then clear them
$errors = isset($_SESSION['signup_errors']) ? $_SESSION['signup_errors'] : [];
$old_data = isset($_SESSION['signup_data']) ? $_SESSION['signup_data'] : [];
unset($_SESSION['signup_errors']);
unset($_SESSION['signup_data']);

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

     <!-- Main Sign-Up Section -->
     <div class="main-container">
        <div class="signup-container">
            <h2>Sign Up</h2>
            <?php if (isset($errors['general'])): ?>
                <div class="error-message general-error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
            <form action="signup.php" method="POST" autocomplete="off">
                <div class="input-group">
                    <input type="email" 
                           name="email" 
                           placeholder="Email Address"
                           required>
                    <div class="error-text" id="email-error">
                        <i class="fas fa-exclamation-circle"></i> Please enter a valid email
                    </div>
                </div>

                <div class="input-group">
                    <input type="text" 
                           name="username" 
                           placeholder="Username"
                           class="<?php echo isset($errors['username']) ? 'error' : ''; ?>"
                           value="<?php echo isset($old_data['username']) ? htmlspecialchars($old_data['username']) : ''; ?>"
                           required>
                    <?php if (isset($errors['username'])): ?>
                        <span class="error-text"><?php echo $errors['username']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <input type="password" 
                           name="password" 
                           placeholder="Password"
                           pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}"
                           required>
                </div>

                <div class="input-group">
                    <input type="password" 
                           name="confirm_password" 
                           placeholder="Confirm Password"
                           required>
                    <div class="password-requirements">
                        <div class="requirement" id="length" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i> At least 8 characters
                        </div>
                        <div class="requirement" id="uppercase" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i> At least one uppercase letter
                        </div>
                        <div class="requirement" id="lowercase" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i> At least one lowercase letter
                        </div>
                        <div class="requirement" id="number" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i> At least one number
                        </div>
                        <div class="requirement" id="special" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i> At least one special character
                        </div>
                        <div class="requirement" id="match" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i> Passwords must match
                        </div>
                    </div>
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

       .input-group {
           margin-bottom: 20px;
           position: relative;
       }

       .input-group input {
           width: 100%;
           padding: 15px;
           border: 2px solid #ddd;
           border-radius: 10px;
           font-size: 1rem;
           outline: none;
           transition: all 0.3s ease;
           background-color: #f9f9f9;
       }

       .input-group input:focus {
           border-color: #4a6ea5;
       }

       .input-group input.error {
           border-color: #dc3545;
       }

       .error-text {
           color: #dc3545;
           font-size: 0.8rem;
           margin-top: 5px;
           padding-left: 5px;
           display: none;
       }

       .error-text i {
           margin-right: 5px;
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
           margin-top: 10px;
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

       .error-message {
           color: #dc3545;
           font-size: 0.8rem;
           margin-top: 5px;
           padding: 5px 0;
       }

       .general-error {
           background-color: #f8d7da;
           border: 1px solid #f5c6cb;
           border-radius: 5px;
           padding: 10px;
           margin-bottom: 20px;
           text-align: center;
       }

       .password-requirements {
           margin-top: 5px;
           padding-left: 5px;
       }

       .requirement {
           margin: 3px 0;
           color: #dc3545;
           font-size: 0.8rem;
           transition: all 0.3s ease;
           opacity: 1;
       }

       .requirement.valid {
           color: #198754;  /* Green color */
       }

       .requirement.fade-out {
           opacity: 0;
           transition: opacity 2s ease;
       }

       .requirement i {
           margin-right: 5px;
       }

       @keyframes fadeOut {
           from { opacity: 1; }
           to { opacity: 0; }
       }
   </style>

   <script src="script/signup.js"></script>
</body>
</html>
