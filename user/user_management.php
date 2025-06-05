<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body>
    <?php
    require 'db_conn.php';
    session_start();

    date_default_timezone_set('Asia/Manila'); // Set this to your timezone

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Check if the current user is an admin
    $user_id = $_SESSION['user_id'];
    $check_admin = $conn->prepare("SELECT isAdmin FROM users WHERE id = ?");
    $check_admin->bind_param("i", $user_id);
    $check_admin->execute();
    $result = $check_admin->get_result();
    $user_data = $result->fetch_assoc();
    
    if (!$user_data['isAdmin']) {
        header("Location: dashboard.php");
        exit();
    }

    // Handle user updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
        $update_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = isset($_POST['password']) && !empty($_POST['password']) ? 
                    password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
        $isAdmin = isset($_POST['isAdmin']) ? 1 : 0;
        $isPremium = isset($_POST['isPremium']) ? 1 : 0;
        
        if ($password) {
            $update_query = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, isAdmin = ?, isPremium = ? WHERE id = ?");
            $update_query->bind_param("sssiii", $username, $email, $password, $isAdmin, $isPremium, $update_id);
        } else {
            $update_query = $conn->prepare("UPDATE users SET username = ?, email = ?, isAdmin = ?, isPremium = ? WHERE id = ?");
            $update_query->bind_param("ssiii", $username, $email, $isAdmin, $isPremium, $update_id);
        }
        
        if ($update_query->execute()) {
            $success_message = "User updated successfully!";
        } else {
            $error_message = "Error updating user: " . $conn->error;
        }
    }

    // Get all users
    $users_query = $conn->query("SELECT * FROM users ORDER BY id ASC");
    $users = $users_query->fetch_all(MYSQLI_ASSOC);

    include 'components/layout/admin/navbar.php'; 
    ?>

    <div class="container">
        <h1 class="page-title">User Management</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="user-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Premium</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $user['isAdmin'] ? 'active' : 'inactive'; ?>">
                                <?php echo $user['isAdmin'] ? 'Yes' : 'No'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $user['isPremium'] ? 'premium' : 'standard'; ?>">
                                <?php echo $user['isPremium'] ? 'Yes' : 'No'; ?>
                            </span>
                        </td>
                        <td>
                            <button class="edit-btn" onclick="openEditModal(<?php echo $user['id']; ?>)">Edit</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit User</h2>
            <form id="editUserForm" method="POST">
                <input type="hidden" id="user_id" name="user_id">
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password (leave blank to keep current):</label>
                    <input type="password" id="password" name="password">
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="isAdmin" name="isAdmin">
                    <label for="isAdmin">Admin Access</label>
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="isPremium" name="isPremium">
                    <label for="isPremium">Premium User</label>
                </div>
                
                <button type="submit" name="update_user" class="submit-btn">Update User</button>
            </form>
        </div>
    </div>

<style>
    /* General */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f7f7f7;
        color: #4A4947;
        line-height: 1.6;
        padding-top: 80px;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 50px;
    }
    
    .page-title {
        color: #365486;
        margin-bottom: 20px;
        font-size: 2rem;
        text-align: center;
    }
    
    /* User Table */
    .user-table {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    th {
        background-color: #365486;
        color: white;
        font-weight: 600;
    }
    
    tr:hover {
        background-color: #f5f5f5;
    }
    
    /* Status Badges */
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .active {
        background-color: #28a745;
        color: white;
    }
    
    .inactive {
        background-color: #dc3545;
        color: white;
    }
    
    .premium {
        background-color: #ffc107;
        color: #333;
    }
    
    .standard {
        background-color: #6c757d;
        color: white;
    }
    
    /* Buttons */
    .edit-btn {
        background-color: #365486;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .edit-btn:hover {
        background-color: #2a4170;
    }
    
    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }
    
    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 30px;
        border-radius: 8px;
        width: 500px;
        max-width: 90%;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .close:hover {
        color: #333;
    }
    
    /* Form */
    .form-group {
        margin-bottom: 20px;
    }
    
    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: 'Poppins', sans-serif;
    }
    
    .checkbox {
        display: flex;
        align-items: center;
    }
    
    .checkbox input {
        margin-right: 10px;
    }
    
    .submit-btn {
        background-color: #365486;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        transition: background-color 0.3s;
    }
    
    .submit-btn:hover {
        background-color: #2a4170;
    }
    
    /* Alerts */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<script>
    // Modal functionality
    const modal = document.getElementById('editModal');
    
    function openEditModal(userId) {
        // Fetch user data
        fetch(`get_user.php?id=${userId}`)
            .then(response => response.json())
            .then(user => {
                document.getElementById('user_id').value = user.id;
                document.getElementById('username').value = user.username;
                document.getElementById('email').value = user.email;
                document.getElementById('password').value = '';
                document.getElementById('isAdmin').checked = user.isAdmin == 1;
                document.getElementById('isPremium').checked = user.isPremium == 1;
                
                modal.style.display = 'block';
            })
            .catch(error => console.error('Error fetching user data:', error));
    }
    
    function closeEditModal() {
        modal.style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            closeEditModal();
        }
    }
</script>
</body>
</html>