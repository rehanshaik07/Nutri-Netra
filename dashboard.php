<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutri-Netra - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            color: white;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .sidebar-header p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }
        
        .sidebar .nav-link {
            color: white;
            padding: 12px 25px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }
        
        /* Top Bar */
        .top-bar {
            background: white;
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-icon {
            font-size: 40px;
            margin-bottom: 15px;
            display: inline-block;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .stat-trend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 12px;
        }
        
        .trend-up {
            color: #28a745;
        }
        
        .trend-down {
            color: #dc3545;
        }
        
        /* Charts */
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            border-left: 4px solid #667eea;
            padding-left: 15px;
        }
        
        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-banner::before {
            content: '🍽️';
            position: absolute;
            right: 20px;
            bottom: 10px;
            font-size: 80px;
            opacity: 0.1;
        }
        
        .welcome-banner h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .welcome-banner p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Notifications */
        .notification-dropdown {
            position: relative;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>🥗 Nutri-Netra</h3>
            <p>Mess Management System</p>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link" href="pages/menu.php">
                <i class="fas fa-utensils"></i> Weekly Menu
            </a>
            <a class="nav-link" href="pages/attendance.php">
                <i class="fas fa-calendar-check"></i> Attendance
            </a>
            <a class="nav-link" href="pages/wastage.php">
                <i class="fas fa-trash-alt"></i> Food Wastage
            </a>
            <a class="nav-link" href="pages/feedback.php">
                <i class="fas fa-star"></i> Feedback
            </a>
            <a class="nav-link" href="pages/leaderboard.php">
                <i class="fas fa-trophy"></i> Leaderboard
            </a>
            <?php if($_SESSION['role'] == 'admin'): ?>
            <hr style="background: rgba(255,255,255,0.2); margin: 15px;">
            <a class="nav-link" href="pages/admin.php">
                <i class="fas fa-users-cog"></i> Admin Panel
            </a>
            <a class="nav-link" href="pages/admin-feedback.php">
                <i class="fas fa-envelope"></i> Manage Feedback
            </a>
            <a class="nav-link" href="pages/admin-menu.php">
                <i class="fas fa-edit"></i> Edit Menu
            </a>
            <a class="nav-link" href="pages/admin-reports.php">
                <i class="fas fa-chart-line"></i> Reports
            </a>
            <?php endif; ?>
            <hr style="background: rgba(255,255,255,0.2); margin: 15px;">
            <a class="nav-link" href="api/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <div>
                <h4 class="page-title">Dashboard</h4>
                <small class="text-muted">Welcome back! Here's what's happening today.</small>
            </div>
            <div class="user-info">
                <div class="notification-dropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-bell fa-lg"></i>
                    <span class="notification-badge" id="notificationCount">0</span>
                </div>
                <div class="dropdown-menu dropdown-menu-end" id="notificationDropdown" style="width: 300px;">
                    <div class="dropdown-header">Notifications</div>
                    <div id="notificationList"></div>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 2)); ?>
                </div>
                <div>
                    <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong><br>
                    <small class="text-muted"><?php echo ucfirst($_SESSION['role']); ?></small>
                </div>
            </div>
        </div>
        
        <!-- Welcome Banner -->
        <div class="welcome-banner animate-fadeInUp">
            <h2>Good <?php echo date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening'); ?>, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
            <p>Today is <?php echo date('l, F j, Y'); ?>. Let's make healthy choices!</p>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value" id="attendanceRate">-</div>
                <div class="stat-label">Attendance Rate</div>
                <div class="stat-trend trend-up" id="attendanceTrend">↑ 0% this week</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🗑️</div>
                <div class="stat-value" id="totalWastage">-</div>
                <div class="stat-label">Total Wastage (kg)</div>
                <div class="stat-trend" id="wastageTrend">-</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-value" id="avgRating">-</div>
                <div class="stat-label">Average Rating</div>
                <div class="stat-trend">out of 5</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💬</div>
                <div class="stat-value" id="totalFeedback">-</div>
                <div class="stat-label">Total Feedback</div>
                <div class="stat-trend">submitted</div>
            </div>
        </div>
        
        <!-- Charts Row 1 -->
        <div class="row">
            <div class="col-md-8">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-chart-line me-2"></i>Attendance Trend (Last 7 Days)
                    </div>
                    <canvas id="attendanceChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-chart-pie me-2"></i>Wastage by Meal
                    </div>
                    <canvas id="wastageChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Charts Row 2 -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-chart-bar me-2"></i>Daily Wastage Trend
                    </div>
                    <canvas id="dailyWastageChart" height="250"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-star me-2"></i>Rating Distribution
                    </div>
                    <canvas id="ratingChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-history me-2"></i>Recent Activity
            </div>
            <div id="recentActivities" style="max-height: 300px; overflow-y: auto;">
                <div class="text-center py-3">
                    <div class="loading"></div> Loading activities...
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let attendanceChart, wastageChart, dailyWastageChart, ratingChart;
        
        $(document).ready(function() {
            loadDashboardData();
            loadNotifications();
            loadRecentActivities();
            
            // Refresh every 30 seconds
            setInterval(function() {
                loadDashboardData();
                loadNotifications();
            }, 30000);
        });
        
        function loadDashboardData() {
            $.ajax({
                url: 'api/get_stats.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        const stats = response.stats;
                        
                        // Update stats cards with animations
                        $('#attendanceRate').text(stats.attendance_rate + '%');
                        $('#totalWastage').text(stats.total_wastage);
                        $('#avgRating').text(stats.avg_rating);
                        $('#totalFeedback').text(stats.total_feedback);
                        
                        // Update trends
                        if(stats.wastage_reduction !== undefined) {
                            const reduction = stats.wastage_reduction;
                            if(reduction > 0) {
                                $('#wastageTrend').html(`<span class="trend-down">↓ ${reduction}% this month</span>`);
                            } else if(reduction < 0) {
                                $('#wastageTrend').html(`<span class="trend-up">↑ ${Math.abs(reduction)}% this month</span>`);
                            } else {
                                $('#wastageTrend').html('No change');
                            }
                        }
                        
                        // Update charts
                        if(stats.weekly_attendance) {
                            updateAttendanceChart(stats.weekly_attendance);
                        }
                        
                        if(stats.wastage_by_meal) {
                            updateWastageChart(stats.wastage_by_meal);
                        }
                        
                        if(stats.daily_wastage) {
                            updateDailyWastageChart(stats.daily_wastage);
                        }
                        
                        if(stats.rating_distribution) {
                            updateRatingChart(stats.rating_distribution);
                        }
                    }
                }
            });
        }
        
        function updateAttendanceChart(data) {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            if(attendanceChart) attendanceChart.destroy();
            
            attendanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Attendance Count',
                        data: data.data,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointHoverRadius: 8,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: '#ddd',
                            callbacks: {
                                label: function(context) {
                                    return `Attendance: ${context.parsed.y} students`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
        
        function updateWastageChart(data) {
            const ctx = document.getElementById('wastageChart').getContext('2d');
            if(wastageChart) wastageChart.destroy();
            
            wastageChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Breakfast', 'Lunch', 'Dinner'],
                    datasets: [{
                        data: [data.breakfast, data.lunch, data.dinner],
                        backgroundColor: ['#ffc107', '#28a745', '#dc3545'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value}kg (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function updateDailyWastageChart(data) {
            const ctx = document.getElementById('dailyWastageChart').getContext('2d');
            if(dailyWastageChart) dailyWastageChart.destroy();
            
            dailyWastageChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Wastage (kg)',
                        data: data.data,
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderRadius: 10,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Wastage (kg)'
                            }
                        }
                    }
                }
            });
        }
        
        function updateRatingChart(data) {
            const ctx = document.getElementById('ratingChart').getContext('2d');
            if(ratingChart) ratingChart.destroy();
            
            ratingChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
                    datasets: [{
                        label: 'Number of Ratings',
                        data: [data[1], data[2], data[3], data[4], data[5]],
                        backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'],
                        borderRadius: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
        
        function loadNotifications() {
            $.ajax({
                url: 'api/get_notifications.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#notificationCount').text(response.unread_count);
                        let html = '';
                        if(response.notifications.length === 0) {
                            html = '<div class="dropdown-item text-center text-muted">No new notifications</div>';
                        } else {
                            response.notifications.forEach(notif => {
                                html += `
                                    <div class="dropdown-item">
                                        <i class="fas fa-bell me-2"></i>
                                        <strong>${notif.type.toUpperCase()}</strong><br>
                                        <small>${notif.message}</small><br>
                                        <small class="text-muted">${notif.created_at}</small>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                `;
                            });
                        }
                        $('#notificationList').html(html);
                    }
                }
            });
        }
        
        function loadRecentActivities() {
            $.ajax({
                url: 'api/get_activities.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success && response.activities.length > 0) {
                        let html = '<div class="timeline">';
                        response.activities.forEach(activity => {
                            html += `
                                <div style="padding: 10px 0; border-bottom: 1px solid #eee;">
                                    <i class="fas fa-circle" style="font-size: 8px; color: #667eea; vertical-align: middle;"></i>
                                    <span style="margin-left: 10px;">
                                        <strong>${activity.action.replace('_', ' ').toUpperCase()}</strong> - 
                                        ${activity.details}
                                    </span>
                                    <small class="text-muted float-end">${activity.timestamp}</small>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#recentActivities').html(html);
                    } else {
                        $('#recentActivities').html('<div class="text-center py-3 text-muted">No recent activities</div>');
                    }
                }
            });
        }
    </script>
</body>
</html>
