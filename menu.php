<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="fas fa-utensils me-2"></i>Weekly Menu</h2>
    </div>
    
    <?php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    foreach($days as $day):
        $menu_items = [];
        $items = getMenuByDay($day);
        foreach($items as $item) {
            $menu_items[$item['meal_type']] = $item['items'];
        }
    ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <?php echo $day; ?>
                    <?php if(date('l') == $day): ?>
                        <span class="badge bg-warning text-dark ms-2">Today</span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong class="text-primary">🍳 Breakfast</strong>
                    <p class="mb-0 text-muted"><?php echo $menu_items['breakfast'] ?? 'Not specified'; ?></p>
                </div>
                <div class="mb-3">
                    <strong class="text-success">🍛 Lunch</strong>
                    <p class="mb-0 text-muted"><?php echo $menu_items['lunch'] ?? 'Not specified'; ?></p>
                </div>
                <div class="mb-3">
                    <strong class="text-danger">🍽️ Dinner</strong>
                    <p class="mb-0 text-muted"><?php echo $menu_items['dinner'] ?? 'Not specified'; ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>
