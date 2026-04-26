<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

session_start();

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
if(!$input) {
    $input = $_POST;
}

$response = [];

// Log attempt
$log_entry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    'username' => $input['username'] ?? 'unknown'
];

if(isset($input['username']) && isset($input['password']) && !empty($input['username']) && !empty($input['password'])) {
    
    $username = trim($input['username']);
    $password = $input['password'];
    
    // Get user from database
    $user = getUserByUsername($username);
    
    if($user && password_verify($password, $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        
        // Update last login
        $users = readData('users.json');
        foreach($users as &$u) {
            if($u['id'] == $user['id']) {
                $u['last_login'] = date('Y-m-d H:i:s');
                $u['login_count'] = ($u['login_count'] ?? 0) + 1;
                break;
            }
        }
        writeData('users.json', $users);
        
        // Log successful login
        $log_entry['status'] = 'success';
        $log_entry['user_id'] = $user['id'];
        $log_entry['role'] = $user['role'];
        
        $response = [
            'success' => true,
            'message' => 'Login successful! Redirecting...',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    } else {
        // Failed login
        $log_entry['status'] = 'failed';
        $log_entry['reason'] = 'Invalid credentials';
        
        $response = [
            'success' => false,
            'message' => 'Invalid username or password. Please try again.'
        ];
    }
} else {
    $log_entry['status'] = 'failed';
    $log_entry['reason'] = 'Missing credentials';
    
    $response = [
        'success' => false,
        'message' => 'Please provide both username and password.'
    ];
}

// Save login attempt log
$login_logs = readData('login_logs.json');
$login_logs[] = $log_entry;
if(count($login_logs) > 100) {
    $login_logs = array_slice($login_logs, -100);
}
writeData('login_logs.json', $login_logs);

echo json_encode($response);
?>
