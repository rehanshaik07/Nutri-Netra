<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_stats = readData('user_stats.json');

// Sort by different metrics
$lowest_wastage = $user_stats;
usort($lowest_wastage, function($a, $b) {
    return $a['total_wastage'] <=> $b['total_wastage'];
});

$highest_attendance = $user_stats;
usort($highest_attendance, function($a, $b) {
    return $b['attendance_count'] <=> $a['attendance_count'];
});

$highest_rating = array_filter($user_stats, function($u) {
    return $u['total_feedback'] > 0;
});
usort($highest_rating, function($a, $b) {
    return $b['avg_rating'] <=> $a['avg_rating'];
});

echo json_encode([
    'success' => true,
    'leaderboards' => [
        'lowest_wastage' => array_slice($lowest_wastage, 0, 10),
        'highest_attendance' => array_slice($highest_attendance, 0, 10),
        'highest_rating' => array_slice($highest_rating, 0, 10)
    ],
    'current_user' => [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ]
]);
?>
