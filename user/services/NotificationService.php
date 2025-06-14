<?php
class NotificationService {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Create a new notification
     * 
     * @param int $user_id - The ID of the user to notify
     * @param string $title - The notification title
     * @param string $message - The notification message
     * @param string $redirect_url - The URL to redirect to when clicking the notification
     * @return bool - Returns true if successful, false otherwise
     */
    public function createNotification($user_id, $title, $message, $redirect_url) {
        try {
            $query = "INSERT INTO notifications (user_id, title, message, redirect_url) 
                     VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("isss", $user_id, $title, $message, $redirect_url);
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get notifications for a specific user
     * 
     * @param int $user_id - The ID of the user
     * @param int $limit - Maximum number of notifications to return
     * @param bool $unread_only - If true, only return unread notifications
     * @return array - Returns array of notifications
     */
    public function getUserNotifications($user_id, $limit = 20, $unread_only = false) {
        try {
            $query = "SELECT * FROM notifications 
                     WHERE user_id = ? " .
                     ($unread_only ? "AND is_read = 0 " : "") .
                     "ORDER BY created_at DESC 
                     LIMIT ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $user_id, $limit);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $notifications = [];
            
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
            
            $stmt->close();
            return $notifications;
        } catch (Exception $e) {
            error_log("Error fetching notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mark a notification as read
     * 
     * @param int $notification_id - The ID of the notification
     * @param int $user_id - The ID of the user (for security)
     * @return bool - Returns true if successful, false otherwise
     */
    public function markAsRead($notification_id, $user_id) {
        try {
            $query = "UPDATE notifications 
                     SET is_read = 1 
                     WHERE id = ? AND user_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $notification_id, $user_id);
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a notification
     * 
     * @param int $notification_id - The ID of the notification
     * @param int $user_id - The ID of the user (for security)
     * @return bool - Returns true if successful, false otherwise
     */
    public function deleteNotification($notification_id, $user_id) {
        try {
            $query = "DELETE FROM notifications 
                     WHERE id = ? AND user_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $notification_id, $user_id);
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error deleting notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread notification count for a user
     * 
     * @param int $user_id - The ID of the user
     * @return int - Returns the count of unread notifications
     */
    public function getUnreadCount($user_id) {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM notifications 
                     WHERE user_id = ? AND is_read = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            $stmt->close();
            return (int)$row['count'];
        } catch (Exception $e) {
            error_log("Error getting unread count: " . $e->getMessage());
            return 0;
        }
    }
} 