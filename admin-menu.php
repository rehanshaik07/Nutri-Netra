<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/database.php';

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$meal_types = ['breakfast', 'lunch', 'dinner'];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="fas fa-edit me-2"></i>Edit Weekly Menu</h2>
    </div>
    
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Menu Editor</h5>
            </div>
            <div class="card-body">
                <form id="menuForm">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Day</label>
                            <select class="form-control" id="day" required>
                                <?php foreach($days as $day): ?>
                                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Meal Type</label>
                            <select class="form-control" id="meal_type" required>
                                <?php foreach($meal_types as $meal): ?>
                                <option value="<?php echo $meal; ?>"><?php echo ucfirst($meal); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label>Menu Items</label>
                            <textarea class="form-control" id="items" rows="3" required placeholder="Enter menu items separated by commas"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Menu
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Current Menu Preview</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Day</th>
                                <th>Breakfast</th>
                                <th>Lunch</th>
                                <th>Dinner</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($days as $day): 
                                $menu_items = [];
                                $items = getMenuByDay($day);
                                foreach($items as $item) {
                                    $menu_items[$item['meal_type']] = $item['items'];
                                }
                            ?>
                            <tr>
                                <td><strong><?php echo $day; ?></strong></td>
                                <td><?php echo $menu_items['breakfast'] ?? '-'; ?></td>
                                <td><?php echo $menu_items['lunch'] ?? '-'; ?></td>
                                <td><?php echo $menu_items['dinner'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('#menuForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '../api/update_menu.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            day: $('#day').val(),
            meal_type: $('#meal_type').val(),
            items: $('#items').val()
        }),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('✓ ' + response.message);
                location.reload();
            } else {
                alert('✗ ' + response.message);
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
