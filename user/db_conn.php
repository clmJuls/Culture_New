<?php

// Database connection configuration
$host = 'localhost';
$dbname = 'blog_db';
$username = 'root';
$password = '';

// Establish database connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
