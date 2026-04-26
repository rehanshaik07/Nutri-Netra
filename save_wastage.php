<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to report wastage']);
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
    $errors[] = 'Cannot report wastage for future dates';
}

if(!isset($input['meal_type']) || !in_array($input['meal_type'], ['breakfast', 'lunch', 'dinner'])) {
    $errors[] = 'Invalid meal type';
}

if(!isset($input['item_name']) || empty(trim($input['item_name']))) {
    $errors[] = 'Item name is required';
} elseif(strlen($input['item_name']) > 100) {
    $errors[] = 'Item name is too long';
}

if(!isset($input['quantity']) || !is_numeric($input['quantity']) || $input['quantity'] <= 0) {
    $errors[] = 'Please provide a valid quantity';
} elseif($input['quantity'] > 10) {
    $errors[] = 'Quantity seems too high. Please verify.';
}

if(isset($input['reason']) && strlen($input['reason']) > 500) {
    $errors[] = 'Reason is too long (max 500 characters)';
}

if(empty($errors)) {
    $wastage = readData('wastage.json');
    
    $new_record = [
        'id' => getNextId($wastage),
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'date' => $input['date'],
        'meal_type' => $input['meal_type'],
        'item_name' => trim($input['item_name']),
        'quantity' => floatval($input['quantity']),
        'reason' => trim($input['reason'] ?? ''),
        'created_at' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR']
    ];
    
    $wastage[] = $new_record;
    writeData('wastage.json', $wastage);
    
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
    
    $user_stat['total_wastage'] += $new_record['quantity'];
    writeData('user_stats.json', $user_stats);
    
    // Check if wastage is high and create alert
    if($new_record['quantity'] > 1.0) {
        $alerts = readData('alerts.json');
        $alerts[] = [
            'id' => getNextId($alerts),
            'type' => 'high_wastage',
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'message' => "High wastage reported: {$new_record['quantity']}kg of {$new_record['item_name']}",
            'details' => $new_record,
            'created_at' => date('Y-m-d H:i:s'),
            'resolved' => false
        ];
        writeData('alerts.json', $alerts);
    }
    
    // Create activity log
    $activities = readData('activities.json');
    $activities[] = [
        'id' => getNextId($activities),
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'action' => 'report_wastage',
        'details' => "Reported {$new_record['quantity']}kg of {$new_record['item_name']} for {$new_record['meal_type']}",
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if(count($activities) > 200) {
        $activities = array_slice($activities, -200);
    }
    writeData('activities.json', $activities);
    
    // Eco-friendly tip based on wastage
    $tips = [
        "Start with smaller portions and take more if needed!",
        "Share your feedback about food quality to help reduce wastage.",
        "Consider taking half portions of items you're unsure about.",
        "Ask for smaller servings if you're not very hungry.",
        "Pack extra food for later if the mess allows takeaway."
    ];
    $random_tip = $tips[array_rand($tips)];
    
    echo json_encode([
        'success' => true,
        'message' => 'Wastage reported successfully. Thank you for being mindful!',
        'tip' => $random_tip,
        'data' => [
            'item' => $new_record['item_name'],
            'quantity' => $new_record['quantity'],
            'saved_carbon' => round($new_record['quantity'] * 2.5, 2) // Approximate carbon saving
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
