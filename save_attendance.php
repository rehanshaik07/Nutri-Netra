<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to mark attendance']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) {
    $input = $_POST;
}

$errors = [];

// Validate inputs
if(!isset($input['date']) || empty($input['date'])) {
    $errors[] = 'Date is required';
} elseif(strtotime($input['date']) > strtotime('today')) {
    $errors[] = 'Cannot mark attendance for future dates';
} elseif(strtotime($input['date']) < strtotime('-7 days')) {
    $errors[] = 'Cannot mark attendance for dates older than 7 days';
}

if(!isset($input['meal_type']) || !in_array($input['meal_type'], ['breakfast', 'lunch', 'dinner'])) {
    $errors[] = 'Invalid meal type';
}

if(!isset($input['status']) || !in_array($input['status'], ['present', 'absent'])) {
    $errors[] = 'Invalid status';
}

// Check meal time limits
$current_hour = date('H');
$current_time = time();
$meal_deadlines = [
    'breakfast' => strtotime('today 10:00:00'), // Can mark until 10 AM
    'lunch' => strtotime('today 15:00:00'),     // Can mark until 3 PM
    'dinner' => strtotime('today 22:00:00')     // Can mark until 10 PM
];

if($input['date'] == date('Y-m-d')) {
    $deadline = $meal_deadlines[$input['meal_type']];
    if($current_time > $deadline) {
        $errors[] = "Cannot mark {$input['meal_type']} attendance after the allowed time";
    }
}

if(empty($errors)) {
    $attendance = readData('attendance.json');
    
    // Check if already marked
    $existing_index = -1;
    foreach($attendance as $index => $record) {
        if($record['user_id'] == $_SESSION['user_id'] && 
           $record['date'] == $input['date'] && 
           $record['meal_type'] == $input['meal_type']) {
            $existing_index = $index;
            break;
        }
    }
    
    $was_changed = false;
    
    if($existing_index >= 0) {
        // Update existing
        if($attendance[$existing_index]['status'] != $input['status']) {
            $attendance[$existing_index]['status'] = $input['status'];
            $attendance[$existing_index]['updated_at'] = date('Y-m-d H:i:s');
            $was_changed = true;
        }
        $message = "Attendance updated successfully";
    } else {
        // Add new record
        $new_record = [
            'id' => getNextId($attendance),
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'date' => $input['date'],
            'meal_type' => $input['meal_type'],
            'status' => $input['status'],
            'marked_at' => date('Y-m-d H:i:s'),
            'marked_from' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];
        $attendance[] = $new_record;
        $message = "Attendance marked successfully";
    }
    
    writeData('attendance.json', $attendance);
    
    // Update user stats
    $user_stats = readData('user_stats.json');
    $user_stat = null;
    foreach($user_stats as &$stat) {
        if($stat['user_id'] == $_SESSION['user_id']) {
            $user_stat = &$stat;
            break;
        }
    }
    
    if(!$user_stat) {
        $user_stat = [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'total_feedback' => 0,
            'avg_rating' => 0,
            'total_wastage' => 0,
            'attendance_count' => 0
        ];
        $user_stats[] = $user_stat;
    }
    
    // Count unique attendance days
    $unique_days = [];
    foreach($attendance as $record) {
        if($record['user_id'] == $_SESSION['user_id'] && $record['status'] == 'present') {
            $unique_days[$record['date']] = true;
        }
    }
    $user_stat['attendance_count'] = count($unique_days);
    writeData('user_stats.json', $user_stats);
    
    // Create activity log
    $activities = readData('activities.json');
    $activities[] = [
        'id' => getNextId($activities),
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'action' => $existing_index >= 0 ? 'update_attendance' : 'mark_attendance',
        'details' => "Marked {$input['status']} for {$input['meal_type']} on {$input['date']}",
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if(count($activities) > 200) {
        $activities = array_slice($activities, -200);
    }
    writeData('activities.json', $activities);
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => [
            'status' => $input['status'],
            'date' => $input['date'],
            'meal_type' => $input['meal_type']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Please fix the following errors',
        'errors' => $errors
    ]);
}
?>
