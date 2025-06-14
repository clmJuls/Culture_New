<?php
require '../db_conn.php';
require '../services/NotificationService.php';

// Example usage of NotificationService

class NotificationExample {
    private $notificationService;
    
    public function __construct($db_connection) {
        $this->notificationService = new NotificationService($db_connection);
    }

    /**
     * Create a notification for a new comment
     */
    public function notifyNewComment($post_owner_id, $commenter_username, $post_title, $post_id) {
        $title = "New Comment";
        $message = "$commenter_username commented on your post: \"$post_title\"";
        $redirect_url = "post.php?id=" . $post_id;
        
        $this->notificationService->createNotification(
            $post_owner_id,
            $title,
            $message,
            $redirect_url
        );
    }

    /**
     * Create a notification for a new like
     */
    public function notifyNewLike($post_owner_id, $liker_username, $post_title, $post_id) {
        $title = "New Like";
        $message = "$liker_username liked your post: \"$post_title\"";
        $redirect_url = "post.php?id=" . $post_id;
        
        $this->notificationService->createNotification(
            $post_owner_id,
            $title,
            $message,
            $redirect_url
        );
    }

    /**
     * Create a notification for a new post request (admin only)
     */
    public function notifyNewPostRequest($admin_id, $creator_username, $post_title, $post_id) {
        $title = "New Post Request";
        $message = "$creator_username submitted a new post for review: \"$post_title\"";
        $redirect_url = "post-requests.php?id=" . $post_id;
        
        $this->notificationService->createNotification(
            $admin_id,
            $title,
            $message,
            $redirect_url
        );
    }

    /**
     * Create a notification for post approval/rejection
     */
    public function notifyPostStatus($user_id, $post_title, $status, $post_id) {
        $title = "Post " . ucfirst($status);
        $message = "Your post \"$post_title\" has been $status";
        $redirect_url = "post.php?id=" . $post_id;
        
        $this->notificationService->createNotification(
            $user_id,
            $title,
            $message,
            $redirect_url
        );
    }

    /**
     * Create a notification for a new follower
     */
    public function notifyNewFollower($user_id, $follower_username) {
        $title = "New Follower";
        $message = "$follower_username started following you";
        $redirect_url = "profile.php?username=" . urlencode($follower_username);
        
        $this->notificationService->createNotification(
            $user_id,
            $title,
            $message,
            $redirect_url
        );
    }
}

// Example of how to use in your code:
/*
// When someone comments on a post
$notificationExample = new NotificationExample($conn);
$notificationExample->notifyNewComment(
    $post_owner_id,
    $commenter_username,
    $post_title,
    $post_id
);

// When someone likes a post
$notificationExample->notifyNewLike(
    $post_owner_id,
    $liker_username,
    $post_title,
    $post_id
);

// When a new post is submitted for review (notify admin)
$notificationExample->notifyNewPostRequest(
    $admin_id,
    $creator_username,
    $post_title,
    $post_id
);

// When a post is approved/rejected
$notificationExample->notifyPostStatus(
    $user_id,
    $post_title,
    'approved', // or 'rejected'
    $post_id
);

// When someone follows a user
$notificationExample->notifyNewFollower(
    $user_id,
    $follower_username
);
*/ 