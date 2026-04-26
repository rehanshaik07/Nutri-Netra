<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) {
    $input = $_POST;
}

$response = [];

// Validation
$errors = [];

if(!isset($input['username']) || empty(trim($input['username']))) {
    $errors[] = 'Username is required';
} elseif(strlen($input['username']) < 3) {
    $errors[] = 'Username must be at least 3 characters';
} elseif(strlen($input['username']) > 50) {
    $errors[] = 'Username must be less than 50 characters';
} elseif(!preg_match('/^[a-zA-Z0-9_]+$/', $input['username'])) {
    $errors[] = 'Username can only contain letters, numbers, and underscore';
}

if(!isset($input['email']) || empty(trim($input['email']))) {
    $errors[] = 'Email is required';
} elseif(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address';
}

if(!isset($input['password']) || empty($input['password'])) {
    $errors[] = 'Password is required';
} elseif(strlen($input['password']) < 6) {
    $errors[] = 'Password must be at least 6 characters';
} elseif(strlen($input['password']) > 100) {
    $errors[] = 'Password must be less than 100 characters';
}

// Check if passwords match (optional)
if(isset($input['confirm_password']) && $input['password'] !== $input['confirm_password']) {
    $errors[] = 'Passwords do not match';
}

if(empty($errors)) {
    $users = readData('users.json');
    
    // Check if username exists
    $username_exists = false;
    $email_exists = false;
    
    foreach($users as $user) {
        if(strtolower($user['username']) === strtolower($input['username'])) {
            $username_exists = true;
        }
        if(strtolower($user['email']) === strtolower($input['email'])) {
            $email_exists = true;
        }
    }
    
    if($username_exists) {
        $errors[] = 'Username already taken. Please choose another.';
    }
    
    if($email_exists) {
        $errors[] = 'Email already registered. Please use another email or login.';
    }
}

if(empty($errors)) {
    // Create new user
    $new_user = [
        'id' => getNextId($users),
        'username' => trim($input['username']),
        'email' => trim($input['email']),
        'password' => password_hash($input['password'], PASSWORD_DEFAULT),
        'role' => 'user',
        'created_at' => date('Y-m-d H:i:s'),
        'last_login' => null,
        'login_count' => 0,
        'is_active' => true,
        'profile_pic' => null
    ];
    
    $users[] = $new_user;
    writeData('users.json', $users);
    
    // Send welcome email (simulated)
    $welcome_data = [
        'user_id' => $new_user['id'],
        'email' => $new_user['email'],
        'sent_at' => date('Y-m-d H:i:s'),
        'type' => 'welcome'
    ];
    $welcome_emails = readData('welcome_emails.json');
    $welcome_emails[] = $welcome_data;
    writeData('welcome_emails.json', $welcome_emails);
    
    $response = [
        'success' => true,
        'message' => 'Registration successful! Please login to continue.',
        'user' => [
            'username' => $new_user['username'],
            'email' => $new_user['email']
        ]
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Registration failed',
        'errors' => $errors
    ];
}

echo json_encode($response);
?>
