<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/database.php';

$feedback = readData('feedback.json');
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="fas fa-envelope me-2"></i>Manage Feedback</h2>
    </div>
    
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All User Feedback</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Date</th>
                                <th>Meal</th>
                                <th>Rating</th>
                                <th>Comments</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($feedback)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No feedback found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach(array_reverse($feedback) as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['user_name'] ?? 'User'); ?></td>
                                    <td><?php echo $item['date']; ?></td>
                                    <td><?php echo ucfirst($item['meal_type']); ?></td>
                                    <td>
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <?php if($i <= $item['rating']): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-warning"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['comments']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $item['status'] == 'pending' ? 'warning' : 'success'; ?>">
                                            <?php echo ucfirst($item['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success mark-read" data-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-check"></i> Mark Read
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('.mark-read').on('click', function() {
    let id = $(this).data('id');
    alert('Feedback marked as read (demo)');
    $(this).closest('tr').find('.badge').removeClass('bg-warning').addClass('bg-success').text('Read');
    $(this).remove();
});
</script>

<?php include '../includes/footer.php'; ?>
