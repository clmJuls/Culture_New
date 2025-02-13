<?php
require 'db_conn.php';
session_start();

// Security check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle different post actions
$action = isset($_GET['action']) ? $_GET['action'] : 
          (isset($_POST['action']) ? $_POST['action'] : null);

switch ($action) {
    // Fetch user's posts
    case 'fetch':
        fetchUserPosts($conn, $user_id);
        break;

    // Delete a specific post
    case 'delete':
        deletePost($conn, $user_id);
        break;

    // Create a new post
    case 'create':
        createPost($conn, $user_id);
        break;

    // Update an existing post
    case 'update':
        updatePost($conn, $user_id);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit();
}

function fetchUserPosts($conn, $user_id) {
    $query = "
        SELECT p.*, 
               u.username, 
               u.full_name, 
               u.profile_picture, 
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        // Format created_at to be more readable
        $row['formatted_date'] = date('d M Y, h:i A', strtotime($row['created_at']));
        
        // Sanitize output
        $row['title'] = htmlspecialchars($row['title']);
        $row['description'] = htmlspecialchars($row['description']);
        
        $posts[] = $row;
    }

    echo json_encode($posts);
    exit();
}

function deletePost($conn, $user_id) {
    // Validate input
    if (!isset($_POST['post_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing post ID']);
        exit();
    }

    $post_id = $_POST['post_id'];

    // Verify post ownership
    $verify_query = "SELECT file_path FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized to delete this post']);
        exit();
    }

    // Get file path for potential file deletion
    $post = $result->fetch_assoc();
    $file_path = $post['file_path'];

    // Start transaction
    $conn->begin_transaction();
    try {
        // Delete associated likes
        $delete_likes = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
        $delete_likes->bind_param("i", $post_id);
        $delete_likes->execute();

        // Delete associated comments
        $delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
        $delete_comments->bind_param("i", $post_id);
        $delete_comments->execute();

        // Delete the post
        $delete_post = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $delete_post->bind_param("i", $post_id);
        $delete_post->execute();

        // Commit transaction
        $conn->commit();

        // Delete associated file if it exists
        if ($file_path && file_exists($file_path)) {
            unlink($file_path);
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Post deleted successfully'
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();

        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to delete post', 
            'details' => $e->getMessage()
        ]);
    }
    exit();
}

function createPost($conn, $user_id) {
    // Validate input
    $required_fields = ['title', 'description'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing $field"]);
            exit();
        }
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $culture_elements = isset($_POST['culture_elements']) ? $_POST['culture_elements'] : null;

    // Handle file upload
    $file_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = uniqid() . '_' . basename($_FILES['file']['name']);
        $file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload file']);
            exit();
        }
    }

    // Prepare and execute insert
    $query = "INSERT INTO posts (user_id, title, description, file_path, culture_elements) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $user_id, $title, $description, $file_path, $culture_elements);

    try {
        $stmt->execute();
        $post_id = $conn->insert_id;

        echo json_encode([
            'success' => true, 
            'message' => 'Post created successfully',
            'post_id' => $post_id
        ]);
    } catch (Exception $e) {
        // Remove uploaded file if database insert fails
        if ($file_path && file_exists($file_path)) {
            unlink($file_path);
        }

        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to create post', 
            'details' => $e->getMessage()
        ]);
    }
    exit();
}

function updatePost($conn, $user_id) {
    // Validate input
    if (!isset($_POST['post_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing post ID']);
        exit();
    }

    $post_id = $_POST['post_id'];
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $culture_elements = $_POST['culture_elements'] ?? null;

    // Verify post ownership
    $verify_query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized to update this post']);
        exit();
    }

    // Prepare update query
    $update_fields = [];
    $param_types = '';
    $param_values = [];

    if ($title !== null) {
        $update_fields[] = "title = ?";
        $param_types .= 's';
        $param_values[] = $title;
    }

    if ($description !== null) {
        $update_fields[] = "description = ?";
        $param_types .= 's';
        $param_values[] = $description;
    }

    if ($culture_elements !== null) {
        $update_fields[] = "culture_elements = ?";
        $param_types .= 's';
        $param_values[] = $culture_elements;
    }

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = uniqid() . '_' . basename($_FILES['file']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            $update_fields[] = "file_path = ?";
            $param_types .= 's';
            $param_values[] = $file_path;

            // Delete old file if exists
            $old_post = $result->fetch_assoc();
            if ($old_post['file_path'] && file_exists($old_post['file_path'])) {
                unlink($old_post['file_path']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload file']);
            exit();
        }
    }

    // If no fields to update
    if (empty($update_fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No update fields provided']);
        exit();
    }

    // Construct and execute update query
    $param_types .= 'i';
    $param_values[] = $post_id;

    $query = "UPDATE posts SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $stmt = $conn->prepare($query);

    // Dynamically bind parameters
    $stmt->bind_param($param_types, ...$param_values);

    try {
        $stmt->execute();

        echo json_encode([
            'success' => true, 
            'message' => 'Post updated successfully'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to update post', 
            'details' => $e->getMessage()
        ]);
    }
    exit();
}

// Close database connection
$conn->close();