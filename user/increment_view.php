<?php
session_start();
require_once 'db_conn.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$post_id = (int)$_GET['id'];

$query = "UPDATE demographics_posts SET views = views + 1 WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $post_id);

$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo json_encode(['success' => $success]);
?> 