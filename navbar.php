<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="text-center py-4">
        <h3 class="text-white mb-2">🥗 Nutri-Netra</h3>
        <p class="text-white-50 mb-0 small">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>
    <hr class="bg-light mx-3">
    <nav class="nav flex-column">
        <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="../dashboard.php">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a class="nav-link <?php echo $current_page == 'menu.php' ? 'active' : ''; ?>" href="menu.php">
            <i class="fas fa-utensils"></i> Weekly Menu
        </a>
        <a class="nav-link <?php echo $current_page == 'attendance.php' ? 'active' : ''; ?>" href="attendance.php">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a class="nav-link <?php echo $current_page == 'wastage.php' ? 'active' : ''; ?>" href="wastage.php">
            <i class="fas fa-trash-alt"></i> Food Wastage
        </a>
        <a class="nav-link <?php echo $current_page == 'feedback.php' ? 'active' : ''; ?>" href="feedback.php">
            <i class="fas fa-comment"></i> Feedback
        </a>
        <?php if($_SESSION['role'] == 'admin'): ?>
        <hr class="bg-light mx-3">
        <a class="nav-link <?php echo $current_page == 'admin.php' ? 'active' : ''; ?>" href="admin.php">
            <i class="fas fa-users-cog"></i> Admin Panel
        </a>
        <a class="nav-link <?php echo $current_page == 'admin-feedback.php' ? 'active' : ''; ?>" href="admin-feedback.php">
            <i class="fas fa-envelope"></i> Manage Feedback
        </a>
        <a class="nav-link <?php echo $current_page == 'admin-menu.php' ? 'active' : ''; ?>" href="admin-menu.php">
            <i class="fas fa-edit"></i> Edit Menu
        </a>
        <?php endif; ?>
        <hr class="bg-light mx-3">
        <a class="nav-link" href="../api/logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>
<div class="main-content">
    <div class="top-bar">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><?php echo ucfirst(str_replace('.php', '', $current_page)); ?></h4>
            <div>
                <span class="text-muted me-3">
                    <i class="far fa-calendar-alt"></i> <?php echo date('l, F j, Y'); ?>
                </span>
                <span class="badge bg-primary">
                    <i class="fas fa-user"></i> <?php echo ucfirst($_SESSION['role']); ?>
                </span>
            </div>
        </div>
    </div>
    <div class="px-4">
        