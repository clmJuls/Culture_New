<?php
require '../db_conn.php';
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../home.php'); // Redirect to home if logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];

    // Email validation - require .com domain
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (!preg_match('/\.com$/i', $email)) {
        $errors['email'] = 'Email must have a .com domain extension.';
    }

    // Password validation
    $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    if (!preg_match($password_pattern, $password)) {
        $errors['password'] = 'Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match!';
    }

    // Check for duplicate email
    $email_check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $email_check_result = $stmt->get_result();
    if ($email_check_result->num_rows > 0) {
        $errors['email'] = 'Email is already taken!';
    }
    $stmt->close();

    // Check for duplicate username
    $username_check_query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($username_check_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $username_check_result = $stmt->get_result();
    if ($username_check_result->num_rows > 0) {
        $errors['username'] = 'Username is already taken!';
    }
    $stmt->close();

    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the new user
        $query = "INSERT INTO users (email, username, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $email, $username, $hashed_password);

        if ($stmt->execute()) {
            $stmt->close();
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
    <title>KulturaBase</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('../assets/img/BG_CULTURE.png');
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

        .signup-container {
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
        .signup-container::before,
        .signup-container::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background-image: 
                radial-gradient(circle at center, transparent 30%, #88433c11 30%),
                repeating-linear-gradient(45deg, #88433c11 0px, #88433c11 2px, transparent 2px, transparent 8px);
            border-radius: 50%;
        }

        .signup-container::before {
            top: -75px;
            left: -75px;
        }

        .signup-container::after {
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

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #daa520;
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.9);
        }

        .input-group input:focus {
            border-color: #88433c;
            box-shadow: 0 0 0 3px rgba(136, 67, 60, 0.1);
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
            margin-top: 10px;
        }

        .signup-btn:hover {
            background: linear-gradient(135deg, #daa520, #88433c);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(136, 67, 60, 0.3);
        }

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
            color: #198754;
        }

        .requirement.fade-out {
            opacity: 0;
            transition: opacity 2s ease;
        }

        .requirement i {
            margin-right: 5px;
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
                           id="email"
                           placeholder="Email Address (must end with .com)"
                           pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$"
                           title="Email must have a .com domain extension"
                           class="<?php echo isset($errors['email']) ? 'error' : ''; ?>"
                           value="<?php echo isset($old_data['email']) ? htmlspecialchars($old_data['email']) : ''; ?>"
                           required>
                    <div class="error-text" id="email-error" <?php echo isset($errors['email']) ? 'style="display: block;"' : ''; ?>>
                        <?php echo isset($errors['email']) ? $errors['email'] : '<i class="fas fa-exclamation-circle"></i> Please enter a valid email with .com domain'; ?>
                    </div>
                </div>

                <div class="input-group">
                    <input type="text" 
                           name="username" 
                           id="username"
                           placeholder="Username"
                           class="<?php echo isset($errors['username']) ? 'error' : ''; ?>"
                           value="<?php echo isset($old_data['username']) ? htmlspecialchars($old_data['username']) : ''; ?>"
                           required>
                    <div class="error-text" id="username-error" <?php echo isset($errors['username']) ? 'style="display: block;"' : ''; ?>>
                        <?php echo isset($errors['username']) ? $errors['username'] : '<i class="fas fa-exclamation-circle"></i> Please enter a username'; ?>
                    </div>
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
    </div>

   <script src="script/signup.js"></script>
</body>
</html>
