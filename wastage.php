<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-trash-alt me-2"></i>Report Food Wastage</h5>
            </div>
            <div class="card-body">
                <form id="wastageForm">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meal Type</label>
                        <select class="form-control" id="meal_type" required>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="item_name" required placeholder="e.g., Rice, Chapati, Dal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity Wasted (kg)</label>
                        <input type="number" step="0.01" class="form-control" id="quantity" required placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason (Optional)</label>
                        <textarea class="form-control" id="reason" rows="3" placeholder="Why was food wasted?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-upload me-2"></i>Report Wastage
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Wastage Statistics</h5>
            </div>
            <div class="card-body">
                <canvas id="wastageStatsChart" height="300"></canvas>
                <hr>
                <div id="wastageTips">
                    <h6><i class="fas fa-lightbulb text-warning"></i> Tips to Reduce Wastage:</h6>
                    <ul class="text-muted small">
                        <li>Take only what you can eat</li>
                        <li>Use smaller portions first</li>
                        <li>Share extra food with friends</li>
                        <li>Provide feedback on food quality</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('#wastageForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '../api/save_wastage.php',
        method: 'POST',
        data: {
            date: $('#date').val(),
            meal_type: $('#meal_type').val(),
            item_name: $('#item_name').val(),
            quantity: $('#quantity').val(),
            reason: $('#reason').val()
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('✓ ' + response.message);
                $('#wastageForm')[0].reset();
                $('#date').val(new Date().toISOString().split('T')[0]);
                loadWastageChart();
            } else {
                alert('✗ ' + response.message);
            }
        }
    });
});

function loadWastageChart() {
    $.ajax({
        url: '../api/get_stats.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.wastage_by_meal) {
                new Chart($('#wastageStatsChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Breakfast', 'Lunch', 'Dinner'],
                        datasets: [{
                            label: 'Wastage (kg)',
                            data: response.wastage_by_meal,
                            backgroundColor: ['#ffc107', '#28a745', '#dc3545'],
                            borderRadius: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });
            }
        }
    });
}

loadWastageChart();
</script>

<?php include '../includes/footer.php'; ?>
