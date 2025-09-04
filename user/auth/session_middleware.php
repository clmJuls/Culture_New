<?php
require_once __DIR__ . '/SessionManager.php';

function checkSession($conn) {
    // Add cache control headers to prevent back button access after logout
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    $sessionManager = new SessionManager($conn);

    // Check if user has an access token
    if (!isset($_SESSION['access_token']) || !isset($_SESSION['user_id'])) {
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

    // Additional security check: verify session hasn't been hijacked
    if (!isset($_SESSION['user_agent']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        // Session might be hijacked, force logout
        header('Location: /Culture_New/user/auth/logout.php');
        exit();
    }
}

function initializeSecureSession() {
    // Store user agent for session security
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
}
?>
