<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutri-Netra - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            color: #667eea;
            font-weight: bold;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            border-radius: 10px;
            padding: 10px 30px;
        }
        
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2>🥗 Nutri-Netra</h2>
            <p>Mess Management System</p>
        </div>
        
        <div id="alertMessage"></div>
        
        <form id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username or Email</label>
                <input type="text" class="form-control" id="username" placeholder="Enter username or email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-login">Login</button>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted">Demo Credentials:</p>
            <small class="text-muted">Admin: admin / admin123</small><br>
            <small class="text-muted">User: john_doe / user123</small>
        </div>
        
        <div class="text-center mt-3">
            <p>Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" style="color: #667eea;">Register here</a></p>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title">Create New Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" class="form-control" id="regUsername" required placeholder="Choose a username">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" id="regEmail" required placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" class="form-control" id="regPassword" required placeholder="Choose a password">
                        </div>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; width: 100%;">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'api/login.php',
                method: 'POST',
                data: {
                    username: $('#username').val(),
                    password: $('#password').val()
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        window.location.href = 'dashboard.php';
                    } else {
                        $('#alertMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                        setTimeout(function() {
                            $('#alertMessage').html('');
                        }, 3000);
                    }
                }
            });
        });

        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'api/register.php',
                method: 'POST',
                data: {
                    username: $('#regUsername').val(),
                    email: $('#regEmail').val(),
                    password: $('#regPassword').val()
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        alert('Registration successful! Please login.');
                        $('#registerModal').modal('hide');
                        $('#registerForm')[0].reset();
                    } else {
                        alert(response.message);
                    }
                }
            });
        });
    </script>
</body>
</html>
