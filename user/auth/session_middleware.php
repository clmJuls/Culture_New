<?php
require_once __DIR__ . '/SessionManager.php';

function checkSession($conn) {
    $sessionManager = new SessionManager($conn);
    
    // Check if user has an access token
    if (!isset($_SESSION['access_token'])) {
        // No token found, force logout
        header('Location: /Culture_New/user/auth/logout.php');
        exit();
    }

    // Validate the session
    $userId = $sessionManager->validateSession($_SESSION['access_token']);
    if (!$userId || $userId != $_SESSION['user_id']) {
        // Invalid or expired session, force logout
        header('Location: /Culture_New/user/auth/logout.php');
        exit();
    }
}
?>
