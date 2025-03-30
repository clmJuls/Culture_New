<?php
header('Content-Type: application/json');
session_start();
require_once '../db_conn.php';

try {
    // Get search term
    $searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

    // Query to search posts by title
    $query = "SELECT cp.*, u.username 
              FROM culture_posts cp 
              LEFT JOIN users u ON cp.user_id = u.id 
              WHERE cp.title LIKE '%$searchTerm%'
              ORDER BY cp.created_at DESC";

    $result = mysqli_query($conn, $query);
    $posts = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $posts[] = $row;
        }
        mysqli_free_result($result);
    }

    echo json_encode(['success' => true, 'posts' => $posts]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?> 