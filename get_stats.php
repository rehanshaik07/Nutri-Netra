<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['role'] == 'admin';

// Comprehensive statistics
$stats = [];

// 1. User Statistics
if($is_admin) {
    $users = readData('users.json');
    $total_users = count($users);
    $active_users = count(array_filter($users, function($u) {
        return isset($u['last_login']) && strtotime($u['last_login']) > strtotime('-30 days');
    }));
    $new_users_this_month = count(array_filter($users, function($u) {
        return isset($u['created_at']) && date('Y-m', strtotime($u['created_at'])) == date('Y-m');
    }));
    $stats['total_users'] = $total_users;
    $stats['active_users'] = $active_users;
    $stats['new_users_this_month'] = $new_users_this_month;
}

// 2. Attendance Statistics
$attendance = readData('attendance.json');
$user_attendance = array_filter($attendance, function($item) use ($user_id, $is_admin) {
    return $is_admin ? true : $item['user_id'] == $user_id;
});

// Monthly attendance
$current_month = date('Y-m');
$monthly_attendance = array_filter($user_attendance, function($item) use ($current_month) {
    return substr($item['date'], 0, 7) == $current_month;
});
$stats['monthly_attendance_count'] = count($monthly_attendance);

// Weekly attendance
$weekly_attendance = [];
for($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $weekly_attendance['labels'][] = date('D, M d', strtotime($date));
    $count = 0;
    foreach($user_attendance as $record) {
        if($record['date'] == $date) {
            $count++;
        }
    }
    $weekly_attendance['data'][] = $count;
}
$stats['weekly_attendance'] = $weekly_attendance;

// Attendance by meal type
$attendance_by_meal = ['breakfast' => 0, 'lunch' => 0, 'dinner' => 0];
foreach($user_attendance as $record) {
    if($record['status'] == 'present') {
        $attendance_by_meal[$record['meal_type']]++;
    }
}
$stats['attendance_by_meal'] = $attendance_by_meal;

// 3. Wastage Statistics
$wastage = readData('wastage.json');
$user_wastage = array_filter($wastage, function($item) use ($user_id, $is_admin) {
    return $is_admin ? true : $item['user_id'] == $user_id;
});

$stats['total_wastage'] = round(array_sum(array_column($user_wastage, 'quantity')), 2);

// Wastage by meal
$wastage_by_meal = ['breakfast' => 0, 'lunch' => 0, 'dinner' => 0];
foreach($user_wastage as $record) {
    $wastage_by_meal[$record['meal_type']] += $record['quantity'];
}
$stats['wastage_by_meal'] = array_map(function($v) { return round($v, 2); }, $wastage_by_meal);

// Top wasted items
$item_wastage = [];
foreach($user_wastage as $record) {
    $item = $record['item_name'];
    if(!isset($item_wastage[$item])) {
        $item_wastage[$item] = 0;
    }
    $item_wastage[$item] += $record['quantity'];
}
arsort($item_wastage);
$stats['top_wasted_items'] = array_slice($item_wastage, 0, 5, true);

// Daily wastage trend
$daily_wastage = [];
for($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $total = 0;
    foreach($user_wastage as $record) {
        if($record['date'] == $date) {
            $total += $record['quantity'];
        }
    }
    $stats['daily_wastage']['labels'][] = date('M d', strtotime($date));
    $stats['daily_wastage']['data'][] = round($total, 2);
}

// 4. Feedback Statistics
$feedback = readData('feedback.json');
$user_feedback = array_filter($feedback, function($item) use ($user_id, $is_admin) {
    return $is_admin ? true : $item['user_id'] == $user_id;
});

$ratings = array_column($user_feedback, 'rating');
$stats['total_feedback'] = count($user_feedback);
$stats['avg_rating'] = !empty($ratings) ? round(array_sum($ratings) / count($ratings), 1) : 0;
$stats['rating_distribution'] = [
    1 => count(array_filter($ratings, fn($r) => $r == 1)),
    2 => count(array_filter($ratings, fn($r) => $r == 2)),
    3 => count(array_filter($ratings, fn($r) => $r == 3)),
    4 => count(array_filter($ratings, fn($r) => $r == 4)),
    5 => count(array_filter($ratings, fn($r) => $r == 5))
];

// Feedback by meal
$feedback_by_meal = ['breakfast' => ['count' => 0, 'total_rating' => 0],
                      'lunch' => ['count' => 0, 'total_rating' => 0],
                      'dinner' => ['count' => 0, 'total_rating' => 0]];
foreach($user_feedback as $record) {
    $feedback_by_meal[$record['meal_type']]['count']++;
    $feedback_by_meal[$record['meal_type']]['total_rating'] += $record['rating'];
}
foreach($feedback_by_meal as $meal => $data) {
    $stats['feedback_by_meal'][$meal] = $data['count'] > 0 ? round($data['total_rating'] / $data['count'], 1) : 0;
}

// 5. User performance (for individual users)
if(!$is_admin) {
    // Attendance streak
    $dates = array_unique(array_column($user_attendance, 'date'));
    sort($dates);
    $current_streak = 0;
    $max_streak = 0;
    $last_date = null;
    
    foreach($dates as $date) {
        if($last_date && strtotime($date) == strtotime($last_date . ' +1 day')) {
            $current_streak++;
        } else {
            $current_streak = 1;
        }
        $max_streak = max($max_streak, $current_streak);
        $last_date = $date;
    }
    $stats['attendance_streak'] = $max_streak;
    
    // Wastage reduction (last 30 days vs previous 30)
    $last_30_days = array_filter($user_wastage, function($item) {
        return strtotime($item['date']) >= strtotime('-30 days');
    });
    $prev_30_days = array_filter($user_wastage, function($item) {
        return strtotime($item['date']) >= strtotime('-60 days') && strtotime($item['date']) < strtotime('-30 days');
    });
    
    $last_total = array_sum(array_column($last_30_days, 'quantity'));
    $prev_total = array_sum(array_column($prev_30_days, 'quantity'));
    $stats['wastage_reduction'] = $prev_total > 0 ? round((($prev_total - $last_total) / $prev_total) * 100) : 0;
}

// 6. Overall rating (for dashboard)
$stats['attendance_rate'] = !empty($user_attendance) ? round((count($user_attendance) / 90) * 100) : 0;

echo json_encode([
    'success' => true,
    'stats' => $stats,
    'user' => [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['username'],
        'role' => $_SESSION['role']
    ]
]);
?>
