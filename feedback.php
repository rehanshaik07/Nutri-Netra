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
                <h5 class="mb-0"><i class="fas fa-star me-2"></i>Submit Feedback</h5>
            </div>
            <div class="card-body">
                <form id="feedbackForm">
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
                        <label class="form-label">Rating</label>
                        <div class="rating">
                            <i class="far fa-star" data-rating="1"></i>
                            <i class="far fa-star" data-rating="2"></i>
                            <i class="far fa-star" data-rating="3"></i>
                            <i class="far fa-star" data-rating="4"></i>
                            <i class="far fa-star" data-rating="5"></i>
                        </div>
                        <input type="hidden" id="rating" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comments</label>
                        <textarea class="form-control" id="comments" rows="4" placeholder="Share your experience with the food..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Your Recent Feedback</h5>
            </div>
            <div class="card-body" id="recentFeedback">
                <p class="text-muted text-center">Loading...</p>
            </div>
        </div>
    </div>
</div>

<style>
.rating i {
    font-size: 30px;
    cursor: pointer;
    color: #ddd;
    margin-right: 10px;
    transition: all 0.3s;
}
.rating i:hover,
.rating i.active {
    color: #ffc107;
}
</style>

<script>
// Rating stars functionality
$('.rating i').on('click', function() {
    let rating = $(this).data('rating');
    $('#rating').val(rating);
    $('.rating i').removeClass('fas active').addClass('far');
    for(let i = 1; i <= rating; i++) {
        $(`.rating i[data-rating="${i}"]`).removeClass('far').addClass('fas active');
    }
});

$('#feedbackForm').on('submit', function(e) {
    e.preventDefault();
    let rating = $('#rating').val();
    if(rating == 0) {
        alert('Please select a rating');
        return;
    }
    
    $.ajax({
        url: '../api/save_feedback.php',
        method: 'POST',
        data: {
            date: $('#date').val(),
            meal_type: $('#meal_type').val(),
            rating: rating,
            comments: $('#comments').val()
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('✓ ' + response.message);
                $('#feedbackForm')[0].reset();
                $('#rating').val(0);
                $('.rating i').removeClass('fas active').addClass('far');
                loadRecentFeedback();
            } else {
                alert('✗ ' + response.message);
            }
        }
    });
});

function loadRecentFeedback() {
    $.ajax({
        url: '../api/get_stats.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            // For demo, we'll show sample data
            let html = '<div class="list-group">';
            html += '<div class="list-group-item"><strong>Today\'s Lunch</strong><br>⭐ Rating: 4/5<br><small class="text-muted">Food was delicious!</small></div>';
            html += '<div class="list-group-item"><strong>Yesterday\'s Dinner</strong><br>⭐ Rating: 5/5<br><small class="text-muted">Excellent quality</small></div>';
            html += '</div>';
            $('#recentFeedback').html(html);
        }
    });
}

loadRecentFeedback();
</script>

<?php include '../includes/footer.php'; ?>
