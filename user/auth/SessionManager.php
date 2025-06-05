<?php
class SessionManager {
    private $conn;
    private const INACTIVE_TIMEOUT = 900; // 15 minutes in seconds

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createSession($userId) {
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Insert new session
        $stmt = $this->conn->prepare("INSERT INTO user_sessions (user_id, access_token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $token, $expiresAt);
        
        if ($stmt->execute()) {
            return $token;
        }
        return false;
    }

    public function validateSession($token) {
        // Clean up expired sessions first
        $this->cleanupExpiredSessions();

        // Check if token exists and is valid
        $stmt = $this->conn->prepare("SELECT user_id, last_activity FROM user_sessions WHERE access_token = ? AND is_active = 1 AND expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $session = $result->fetch_assoc();
        
        // Update last activity
        $this->updateLastActivity($token);
        
        return $session['user_id'];
    }

    public function updateLastActivity($token) {
        // Update last_activity and extend expires_at
        $newExpiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $stmt = $this->conn->prepare("UPDATE user_sessions SET last_activity = NOW(), expires_at = ? WHERE access_token = ?");
        $stmt->bind_param("ss", $newExpiresAt, $token);
        $stmt->execute();
    }

    public function invalidateSession($token) {
        $stmt = $this->conn->prepare("UPDATE user_sessions SET is_active = 0 WHERE access_token = ?");
        $stmt->bind_param("s", $token);
        return $stmt->execute();
    }

    private function cleanupExpiredSessions() {
        $this->conn->query("DELETE FROM user_sessions WHERE expires_at < NOW() OR is_active = 0");
    }
}
?>
