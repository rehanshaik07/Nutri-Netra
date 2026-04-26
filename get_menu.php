<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$day = isset($_GET['day']) ? $_GET['day'] : date('l');
$meal_type = isset($_GET['meal_type']) ? $_GET['meal_type'] : null;

$menu_items = getMenuByDay($day);

$response = [
    'success' => true,
    'day' => $day,
    'menu' => []
];

foreach($menu_items as $item) {
    if($meal_type === null || $item['meal_type'] === $meal_type) {
        $response['menu'][$item['meal_type']] = [
            'items' => $item['items'],
            'last_updated' => $item['updated_at'] ?? 'Not recorded'
        ];
    }
}

// Get menu change history (for admin)
if($_SESSION['role'] == 'admin') {
    $menu_logs = readData('menu_logs.json');
    $response['menu_history'] = array_slice(array_reverse($menu_logs), 0, 10);
}

// Get nutritional info (simulated)
$response['nutrition_tip'] = [
    'breakfast' => 'A healthy breakfast should include protein, fiber, and healthy fats.',
    'lunch' => 'Balance your plate with proteins, complex carbs, and vegetables.',
    'dinner' => 'Keep dinner light but nutritious for better sleep.'
];

echo json_encode($response);
?>
