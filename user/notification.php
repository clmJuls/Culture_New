<?php
require 'db_conn.php';
require 'services/NotificationService.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to access this page.');
            window.location.href = '../user/auth/login.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$isAdmin = $_SESSION['isAdmin'];

// Initialize the notification service
$notificationService = new NotificationService($conn);

// Get notifications for the current user
$notifications = $notificationService->getUserNotifications($user_id);

// Group notifications by date
$groupedNotifications = [];
foreach ($notifications as $notification) {
    $date = date('Y-m-d', strtotime($notification['created_at']));
    if (!isset($groupedNotifications[$date])) {
        $groupedNotifications[$date] = [];
    }
    $groupedNotifications[$date][] = $notification;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KulturaBase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php 
    if ($isAdmin) {
        include 'components/layout/admin/navbar.php';
    } else {
        include 'components/layout/guest/navbar.php';
    }
    ?>
    
    <div class="page-container">
        <div class="notification-header">
            <h1>Notifications</h1>
            <button class="mark-all-read" onclick="markAllAsRead()">
                <i class="fas fa-check"></i> Mark all as read
            </button>
        </div>

        <div class="notification-container">
            <?php if (!empty($groupedNotifications)): ?>
                <?php foreach ($groupedNotifications as $date => $dayNotifications): ?>
                    <div class="notification-group">
                        <div class="date-header">
                            <?php echo formatDateHeader($date); ?>
                        </div>
                        <?php foreach ($dayNotifications as $notification): ?>
                            <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                                <div class="notification-circle"></div>
                                <div class="notification-content">
                                    <div class="notification-title">
                                        <?php echo htmlspecialchars($notification['title']); ?>
                                    </div>
                                    <div class="notification-message">
                                        <?php echo htmlspecialchars($notification['message']); ?>
                                    </div>
                                    <div class="notification-time">
                                        <?php echo date('h:i A', strtotime($notification['created_at'])); ?>
                                    </div>
                                </div>
                                <?php if (!empty($notification['redirect_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($notification['redirect_url']); ?>" 
                                       class="notification-action"
                                       onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                        View
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>No notifications found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding-top: 80px;
        }

        .page-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 50px 4px;
        }

        .notification-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .mark-all-read {
            background: none;
            border: none;
            color: #365486;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .mark-all-read:hover {
            background-color: #f0f0f0;
        }

        .notification-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .notification-group {
            border-bottom: 1px solid #eee;
        }

        .notification-group:last-child {
            border-bottom: none;
        }

        .date-header {
            padding: 12px 20px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #666;
            background-color: #fff;
            border-bottom: 1px solid #eee;
        }

        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 16px 20px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
            gap: 12px;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-circle {
            width: 10px;
            height: 10px;
            border: 2px solid #666;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-weight: 500;
            color: #365486;
            margin-bottom: 4px;
            font-size: 0.95rem;
        }

        .notification-message {
            color: #666;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .notification-time {
            font-size: 0.85rem;
            color: #888;
        }

        .notification-action {
            background-color: #365486;
            color: white;
            text-decoration: none;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 0.85rem;
            align-self: center;
            transition: background-color 0.2s;
        }

        .notification-action:hover {
            background-color: #2a4268;
        }

        .no-notifications {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .no-notifications i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .no-notifications p {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 16px;
            }

            .notification-header {
                margin-bottom: 16px;
            }

            .notification-item {
                padding: 12px 16px;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function markAsRead(notificationId) {
            $.post('ajax/mark_notification_read.php', {
                notification_id: notificationId
            });
        }

        function markAllAsRead() {
            $.post('ajax/mark_all_notifications_read.php', function(response) {
                if (response.success) {
                    location.reload();
                }
            });
        }
    </script>

    <!-- Sidebar -->
    <?php include 'components/layout/guest/sidebar.php'; ?>
    <?php include 'components/widgets/chat.php'; ?>
</body>
</html>

<?php
function formatDateHeader($date) {
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    if ($date === $today) {
        return 'Today';
    } elseif ($date === $yesterday) {
        return 'Yesterday';
    } else {
        return date('F j, Y', strtotime($date));
    }
}