<?php
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function getMenuByDay($pdo, $day) {
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE day = ?");
    $stmt->execute([$day]);
    return $stmt->fetchAll();
}

function getUserAttendance($pdo, $user_id, $date, $meal_type) {
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = ? AND meal_type = ?");
    $stmt->execute([$user_id, $date, $meal_type]);
    return $stmt->fetch();
}

function getTodayWastage($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM wastage WHERE user_id = ? AND date = CURDATE()");
    $stmt->execute([$user_id]);
    return $stmt->fetch()['total'] ?? 0;
}
?>