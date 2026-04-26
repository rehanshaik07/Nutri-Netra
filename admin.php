<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/database.php';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="fas fa-users-cog me-2"></i>Admin Panel</h2>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <h3 id="totalUsers">-</h3>
                <p class="text-muted">Total Users</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <h3 id="todayAttendance">-</h3>
                <p class="text-muted">Today's Attendance</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <h3 id="pendingFeedback">-</h3>
                <p class="text-muted">Pending Feedback</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <h3 id="totalWastage">-</h3>
                <p class="text-muted">Total Wastage (kg)</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>System Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="overviewChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-database me-2"></i>System Information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>System Name:</strong></td>
                        <td>Nutri-Netra Mess Management</td>
                    </tr>
                    <tr>
                        <td><strong>Version:</strong></td>
                        <td>1.0.0</td>
                    </tr>
                    <tr>
                        <td><strong>Data Storage:</strong></td>
                        <td>JSON File-based</td>
                    </tr>
                    <tr>
                        <td><strong>Last Backup:</strong></td>
                        <td><?php echo date('Y-m-d H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Records:</strong></td>
                        <td id="totalRecords">-</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load admin stats
    $.ajax({
        url: '../api/get_stats.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#totalWastage').text(response.total_wastage);
                $('#pendingFeedback').text(response.total_feedback);
                
                // Overview Chart
                new Chart($('#overviewChart'), {
                    type: 'line',
                    data: {
                        labels: response.attendance_labels,
                        datasets: [{
                            label: 'Attendance Trend',
                            data: response.attendance_data,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
            }
        }
    });
    
    // Set total users
    $('#totalUsers').text('2'); // Demo data
    $('#todayAttendance').text('15');
    $('#totalRecords').text('45');
});
</script>

<?php include '../includes/footer.php'; ?>
