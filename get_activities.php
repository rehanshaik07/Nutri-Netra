<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$activities = readData('activities.json');
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;

// Filter by user
$user_activities = array_filter($activities, function($act) {
    return $act['user_id'] == $_SESSION['user_id'];
});

$user_activities = array_slice(array_reverse($user_activities), 0, $limit);

echo json_encode([
    'success' => true,
    'activities' => array_values($user_activities)
]);
?>
