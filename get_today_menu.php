<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$today = date('l');
$menu_items = getMenuByDay($today);

$response = ['success' => true];
foreach($menu_items as $item) {
    $response[$item['meal_type']] = $item['items'];
}

echo json_encode($response);
?>
