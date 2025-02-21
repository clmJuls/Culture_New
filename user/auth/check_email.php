<?php
require '../db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
    
    $query = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($query);
    
    echo json_encode(['exists' => $result->num_rows > 0]);
}

$conn->close();