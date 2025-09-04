<?php
require '../db_conn.php';
require 'SessionManager.php';

// Prevent caching to avoid back button access
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();

$sessionManager = new SessionManager($conn);

// Invalidate the access token if it exists
if (isset($_SESSION['access_token'])) {
    $sessionManager->invalidateSession($_SESSION['access_token']);
}

// Clear all cookies related to authentication
if (isset($_COOKIE['access_token'])) {
    setcookie('access_token', '', time() - 3600, '/', '', false, true);
}
if (isset($_COOKIE['username'])) {
    setcookie('username', '', time() - 3600, '/', '', false, true);
}

// Clear any other session-related cookies
if (isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', '', time() - 3600, '/', '', false, true);
}

// Unset all session variables
$_SESSION = array();

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Destroy the session
session_destroy();

// Additional security: Clear any remaining session data
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page with logout success parameter
header('Location: login.php?logout=success');
exit();
?>