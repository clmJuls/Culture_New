<?php
require_once '../db_conn.php';
header('Content-Type: application/json');

try {
    $query = "SELECT 
        SUBSTRING_INDEX(SUBSTRING_INDEX(t.learning_styles, ',', n.n), ',', -1) style,
        COUNT(*) as count
    FROM 
        posts t CROSS JOIN 
        (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4) n
    WHERE 
        n.n <= 1 + (LENGTH(t.learning_styles) - LENGTH(REPLACE(t.learning_styles, ',', '')))
        AND status = 'approved'
    GROUP BY style
    ORDER BY count DESC";

    $result = $conn->query($query);
    
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $style = trim($row['style']);
        if (!empty($style)) {
            $data[] = array(
                'style' => $style,
                'count' => (int)$row['count']
            );
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 