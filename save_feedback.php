<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) {
    $input = $_POST;
}

$errors = [];

// Validation
if(!isset($input['rating']) || $input['rating'] < 1 || $input['rating'] > 5) {
    $errors[] = 'Please provide a valid rating (1-5)';
}

if(!isset($input['date']) || empty($input['date'])) {
    $errors[] = 'Date is required';
}

if(!isset($input['meal_type']) || !in_array($input['meal_type'], ['breakfast', 'lunch', 'dinner'])) {
    $errors[] = 'Invalid meal type';
}

if(isset($input['comments']) && strlen($input['comments']) > 500) {
    $errors[] = 'Comments cannot exceed 500 characters';
}

if(empty($errors)) {
    $feedback = readData('feedback.json');
    
    // Check if user already gave feedback for this meal
    $existing = false;
    foreach($feedback as $item) {
        if($item['user_id'] == $_SESSION['user_id'] && 
           $item['date'] == $input['date'] && 
           $item['meal_type'] == $input['meal_type']) {
            $existing = true;
            break;
        }
    }
    
    if($existing) {
        echo json_encode(['success' => false, 'message' => 'You have already submitted feedback for this meal']);
        exit();
    }
    
    // Save feedback
    $new_feedback = [
        'id' => getNextId($feedback),
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['username'],
        'date' => $input['date'],
        'meal_type' => $input['meal_type'],
        'rating' => intval($input['rating']),
        'comments' => trim($input['comments'] ?? ''),
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR']
    ];
    
    $feedback[] = $new_feedback;
    writeData('feedback.json', $feedback);
    
    // Check if feedback contains compliments for admin notification
    $positive_keywords = ['good', 'great', 'excellent', 'awesome', 'delicious', 'amazing', 'perfect', 'love', 'best'];
    $negative_keywords = ['bad', 'poor', 'terrible', 'awful', 'horrible', 'disgusting', 'worst', 'hate'];
    
    $has_compliment = false;
    $has_complaint = false;
    
    foreach($positive_keywords as $keyword) {
        if(stripos($new_feedback['comments'], $keyword) !== false) {
            $has_compliment = true;
            break;
        }
    }
    
    foreach($negative_keywords as $keyword) {
        if(stripos($new_feedback['comments'], $keyword) !== false) {
            $has_complaint = true;
            break;
        }
    }
    
    // Create notification for admin
    $notifications = readData('notifications.json');
    $notification = [
        'id' => getNextId($notifications),
        'type' => $has_complaint ? 'complaint' : ($has_compliment ? 'compliment' : 'feedback'),
        'message' => "New feedback from {$new_feedback['user_name']} for {$new_feedback['meal_type']} on {$new_feedback['date']}",
        'rating' => $new_feedback['rating'],
        'feedback_id' => $new_feedback['id'],
        'created_at' => date('Y-m-d H:i:s'),
        'read' => false
    ];
    $notifications[] = $notification;
    writeData('notifications.json', $notifications);
    
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
    
    $user_stat['total_feedback']++;
    $user_stat['avg_rating'] = ($user_stat['avg_rating'] * ($user_stat['total_feedback'] - 1) + $new_feedback['rating']) / $user_stat['total_feedback'];
    writeData('user_stats.json', $user_stats);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your feedback! Your input helps us improve.',
        'feedback_id' => $new_feedback['id']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Please fix the following errors',
        'errors' => $errors
    ]);
}
?>
