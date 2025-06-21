<?php
require_once '../db_conn.php';
header('Content-Type: application/json');

try {
    // Get posts count for the last 7 days
    $query = "SELECT 
        DATE(created_at) as post_date,
        COUNT(*) as post_count
    FROM posts
    WHERE 
        created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        AND status = 'approved'
    GROUP BY DATE(created_at)
    ORDER BY post_date ASC";

    $result = $conn->query($query);
    
    // Initialize the last 7 days with 0 counts
    $days = array();
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $days[$date] = 0;
    }

    // Fill in actual counts
    while ($row = $result->fetch_assoc()) {
        $days[$row['post_date']] = (int)$row['post_count'];
    }

    $response = array(
        'status' => 'success',
        'data' => array(
            'dates' => array_keys($days),
            'counts' => array_values($days)
        )
    );
    
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(array(
        'status' => 'error',
        'message' => $e->getMessage()
    ));
}
?> 