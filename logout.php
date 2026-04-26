<?php
session_start();

// Comprehensive logout with logging
if(isset($_SESSION['user_id'])) {
    require_once '../config/database.php';
    
    // Log logout activity
    $logout_log = [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'logout_time' => date('Y-m-d H:i:s'),
        'session_duration' => time() - ($_SESSION['login_time'] ?? time()),
        'ip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $logout_logs = readData('logout_logs.json');
    $logout_logs[] = $logout_log;
    if(count($logout_logs) > 100) {
        $logout_logs = array_slice($logout_logs, -100);
    }
    writeData('logout_logs.json', $logout_logs);
}

// Clear all session variables
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect with JavaScript for better UX
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // AJAX request
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
} else {
    // Regular request
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="refresh" content="2;url=../index.php">
        <title>Logging Out...</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .logout-container {
                text-align: center;
                color: white;
            }
            .spinner {
                border: 4px solid rgba(255,255,255,0.3);
                border-radius: 50%;
                border-top: 4px solid white;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
                margin: 20px auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .checkmark {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: #4CAF50;
                margin: 20px auto;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: scaleIn 0.5s ease;
            }
            @keyframes scaleIn {
                from { transform: scale(0); }
                to { transform: scale(1); }
            }
            .checkmark svg {
                width: 50px;
                height: 50px;
            }
        </style>
    </head>
    <body>
        <div class="logout-container">
            <div class="checkmark">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
            </div>
            <h2>Successfully Logged Out!</h2>
            <p>Thank you for using Nutri-Netra</p>
            <div class="spinner"></div>
            <p>Redirecting to login page...</p>
        </div>
        <script>
            setTimeout(function() {
                window.location.href = '../index.php';
            }, 2000);
        </script>
    </body>
    </html>
    <?php
}
exit();
?>
