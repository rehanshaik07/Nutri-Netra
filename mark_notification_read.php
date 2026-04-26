<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$notification_id = $input['id'] ?? null;

if($notification_id) {
    $notifications = readData('notifications.json');
    
    foreach($notifications as &$notif) {
        if($notif['id'] == $notification_id) {
            if(!isset($notif['read_by'])) {
                $notif['read_by'] = [];
            }
            if(!in_array($_SESSION['user_id'], $notif['read_by'])) {
                $notif['read_by'][] = $_SESSION['user_id'];
            }
            break;
        }
    }
    
    writeData('notifications.json', $notifications);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
