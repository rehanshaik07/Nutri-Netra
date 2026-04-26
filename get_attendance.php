<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$attendance = readData('attendance.json');
$user_attendance = array_filter($attendance, function($item) {
    return $item['user_id'] == $_SESSION['user_id'];
});

// Get last 10 records
$user_attendance = array_slice(array_reverse($user_attendance), 0, 10);

echo json_encode(['success' => true, 'data' => array_values($user_attendance)]);
?>
