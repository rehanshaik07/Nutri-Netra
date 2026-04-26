<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$notifications = readData('notifications.json');
$user_notifications = [];

foreach($notifications as $notif) {
    // Mark as read for this user if not already
    if(!in_array($_SESSION['user_id'], $notif['read_by'] ?? [])) {
        $user_notifications[] = $notif;
    }
}

// Get recent only (last 7 days)
$user_notifications = array_filter($user_notifications, function($notif) {
    return strtotime($notif['created_at']) >= strtotime('-7 days');
});

echo json_encode([
    'success' => true,
    'notifications' => array_values($user_notifications),
    'unread_count' => count($user_notifications)
]);
?>