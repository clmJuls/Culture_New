<?php
require_once '../db_conn.php';
header('Content-Type: application/json');

try {
    // Get posts count for the last 6 months
    $query = "SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as post_count
    FROM posts
    WHERE 
        created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        AND status = 'approved'
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC";

    $result = $conn->query($query);
    
    // Initialize the last 6 months with 0 counts
    $months = array();
    for ($i = 5; $i >= 0; $i--) {
        $date = date('Y-m', strtotime("-$i months"));
        $months[$date] = 0;
    }

    // Fill in actual counts
    while ($row = $result->fetch_assoc()) {
        $months[$row['month']] = (int)$row['post_count'];
    }

    $response = array(
        'status' => 'success',
        'data' => array(
            'months' => array_keys($months),
            'counts' => array_values($months)
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