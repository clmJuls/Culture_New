<?php

$is_production = $_SERVER['HTTP_HOST'] === 'darkturquoise-trout-965292.hostingersite.com';

if ($is_production) {
    $host = 'localhost'; 
    $dbname = 'u707284814_blog_db';
    $username = 'u707284814_kulturabase';
    $password = 'Kultura1001';
} else {
    $host = 'localhost';
    $dbname = 'blog_db';
    $username = 'root';
    $password = '';
}

try {
    $conn = mysqli_connect($host, $username, $password, $dbname);
    
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("We're experiencing technical difficulties. Please try again later.");
}

?>
