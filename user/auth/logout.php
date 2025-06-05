<?php
require '../db_conn.php';
require 'SessionManager.php';
session_start();

$sessionManager = new SessionManager($conn);

// Invalidate the access token if it exists
if (isset($_SESSION['access_token'])) {
    $sessionManager->invalidateSession($_SESSION['access_token']);
}

// Clear cookies if they exist
if (isset($_COOKIE['access_token'])) {
    setcookie('access_token', '', time() - 3600, '/');
}
if (isset($_COOKIE['username'])) {
    setcookie('username', '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit();
?>