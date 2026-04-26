<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if(!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$errors = [];

if(!isset($input['day']) || empty($input['day'])) {
    $errors[] = 'Day is required';
}

if(!isset($input['meal_type']) || !in_array($input['meal_type'], ['breakfast', 'lunch', 'dinner'])) {
    $errors[] = 'Invalid meal type';
}

if(!isset($input['items']) || empty(trim($input['items']))) {
    $errors[] = 'Menu items cannot be empty';
}

if(empty($errors)) {
    $menu = readData('menu.json');
    $found = false;
    $old_items = '';
    
    // Find and update
    foreach($menu as $index => $item) {
        if($item['day'] === $input['day'] && $item['meal_type'] === $input['meal_type']) {
            $old_items = $item['items'];
            $menu[$index]['items'] = trim($input['items']);
            $menu[$index]['updated_by'] = $_SESSION['username'];
            $menu[$index]['updated_at'] = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    
    // If not found, add new
    if(!$found) {
        $menu[] = [
            'day' => $input['day'],
            'meal_type' => $input['meal_type'],
            'items' => trim($input['items']),
            'created_by' => $_SESSION['username'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_by' => $_SESSION['username'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
    
    writeData('menu.json', $menu);
    
    // Log menu change
    $menu_logs = readData('menu_logs.json');
    $menu_logs[] = [
        'id' => getNextId($menu_logs),
        'user' => $_SESSION['username'],
        'day' => $input['day'],
        'meal_type' => $input['meal_type'],
        'old_items' => $old_items ?? 'New entry',
        'new_items' => $input['items'],
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR']
    ];
    
    if(count($menu_logs) > 100) {
        $menu_logs = array_slice($menu_logs, -100);
    }
    writeData('menu_logs.json', $menu_logs);
    
    // Create notification for users
    $notifications = readData('notifications.json');
    $notifications[] = [
        'id' => getNextId($notifications),
        'type' => 'menu_update',
        'message' => "Menu has been updated for {$input['day']} {$input['meal_type']}",
        'details' => "New: " . substr($input['items'], 0, 100),
        'created_at' => date('Y-m-d H:i:s'),
        'read_by' => []
    ];
    writeData('notifications.json', $notifications);
    
    echo json_encode([
        'success' => true,
        'message' => 'Menu updated successfully!',
        'changes' => [
            'day' => $input['day'],
            'meal_type' => $input['meal_type'],
            'old' => $old_items ?? 'New menu item',
            'new' => $input['items']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
}
?>
