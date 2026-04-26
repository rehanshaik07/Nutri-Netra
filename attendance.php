<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Mark Attendance</h5>
            </div>
            <div class="card-body">
                <form id="attendanceForm">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meal Type</label>
                        <select class="form-control" id="meal_type" required>
                            <option value="breakfast">🍳 Breakfast (7:00 AM - 9:00 AM)</option>
                            <option value="lunch">🍛 Lunch (12:00 PM - 2:00 PM)</option>
                            <option value="dinner">🍽️ Dinner (7:00 PM - 9:00 PM)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="status" required>
                            <option value="present">✅ Present</option>
                            <option value="absent">❌ Absent</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Submit Attendance
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Attendance Records</h5>
            </div>
            <div class="card-body">
                <div id="recentAttendance">
                    <p class="text-muted text-center">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadRecentAttendance() {
    $.ajax({
        url: '../api/get_attendance.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) {
                let html = '<div class="table-responsive">';
                html += '<table class="table table-hover">';
                html += '<thead><tr><th>Date</th><th>Meal</th><th>Status</th><th>Time</th></tr></thead><tbody>';
                response.data.forEach(item => {
                    let statusClass = item.status === 'present' ? 'success' : 'danger';
                    let statusIcon = item.status === 'present' ? '✅' : '❌';
                    html += `<tr>
                        <td>${item.date}</td>
                        <td>${item.meal_type}</td>
                        <td><span class="badge bg-${statusClass}">${statusIcon} ${item.status}</span></td>
                        <td>${item.marked_at.split(' ')[1]}</td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                $('#recentAttendance').html(html);
            } else {
                $('#recentAttendance').html('<p class="text-muted text-center">No attendance records found</p>');
            }
        }
    });
}

$('#attendanceForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '../api/save_attendance.php',
        method: 'POST',
        data: {
            date: $('#date').val(),
            meal_type: $('#meal_type').val(),
            status: $('#status').val()
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('✓ ' + response.message);
                loadRecentAttendance();
                $('#attendanceForm')[0].reset();
                $('#date').val(new Date().toISOString().split('T')[0]);
            } else {
                alert('✗ ' + response.message);
            }
        }
    });
});

loadRecentAttendance();
</script>

<?php include '../includes/footer.php'; ?>
