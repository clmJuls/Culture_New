<?php
require '../db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $conn->real_escape_string($_POST['username']);
    
    $query = "SELECT id FROM users WHERE username = '$username'";
    $result = $conn->query($query);
    
    echo json_encode(['exists' => $result->num_rows > 0]);
}

$conn->close(); 