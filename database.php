<?php
// File-based database configuration
define('DATA_DIR', __DIR__ . '/../data/');

// Create data directory if not exists
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Initialize data files
function initDataFile($filename, $defaultData = []) {
    $filepath = DATA_DIR . $filename;
    if (!file_exists($filepath)) {
        file_put_contents($filepath, json_encode($defaultData, JSON_PRETTY_PRINT));
    }
    return $filepath;
}

// Read data from JSON file
function readData($filename) {
    $filepath = DATA_DIR . $filename;
    if (!file_exists($filepath)) {
        return [];
    }
    $content = file_get_contents($filepath);
    return json_decode($content, true) ?: [];
}

// Write data to JSON file
function writeData($filename, $data) {
    $filepath = DATA_DIR . $filename;
    return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
}

// Initialize all data files
initDataFile('users.json', [
    ['id' => 1, 'username' => 'admin', 'email' => 'admin@nutri-netra.com', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'admin', 'created_at' => date('Y-m-d H:i:s')],
    ['id' => 2, 'username' => 'john_doe', 'email' => 'john@example.com', 'password' => password_hash('user123', PASSWORD_DEFAULT), 'role' => 'user', 'created_at' => date('Y-m-d H:i:s')]
]);

initDataFile('menu.json', [
    ['day' => 'Monday', 'meal_type' => 'breakfast', 'items' => 'Idli, Sambhar, Chutney'],
    ['day' => 'Monday', 'meal_type' => 'lunch', 'items' => 'Rice, Dal Fry, Mix Veg, Chapati'],
    ['day' => 'Monday', 'meal_type' => 'dinner', 'items' => 'Chapati, Paneer Butter Masala, Salad'],
    ['day' => 'Tuesday', 'meal_type' => 'breakfast', 'items' => 'Dosa, Sambhar, Coconut Chutney'],
    ['day' => 'Tuesday', 'meal_type' => 'lunch', 'items' => 'Jeera Rice, Rajma, Salad'],
    ['day' => 'Tuesday', 'meal_type' => 'dinner', 'items' => 'Chapati, Aloo Gobi, Dal Tadka'],
    ['day' => 'Wednesday', 'meal_type' => 'breakfast', 'items' => 'Poha, Jalebi'],
    ['day' => 'Wednesday', 'meal_type' => 'lunch', 'items' => 'Biryani, Raita, Salad'],
    ['day' => 'Wednesday', 'meal_type' => 'dinner', 'items' => 'Chapati, Chole, Rice'],
    ['day' => 'Thursday', 'meal_type' => 'breakfast', 'items' => 'Upma, Vada, Sambhar'],
    ['day' => 'Thursday', 'meal_type' => 'lunch', 'items' => 'Rice, Sambhar, Rasam, Papad'],
    ['day' => 'Thursday', 'meal_type' => 'dinner', 'items' => 'Chapati, Palak Paneer, Salad'],
    ['day' => 'Friday', 'meal_type' => 'breakfast', 'items' => 'Paratha, Curd, Pickle'],
    ['day' => 'Friday', 'meal_type' => 'lunch', 'items' => 'Fried Rice, Manchurian, Soup'],
    ['day' => 'Friday', 'meal_type' => 'dinner', 'items' => 'Chapati, Dal Makhani, Jeera Rice'],
    ['day' => 'Saturday', 'meal_type' => 'breakfast', 'items' => 'Poori, Bhaji, Halwa'],
    ['day' => 'Saturday', 'meal_type' => 'lunch', 'items' => 'Thali (Special)'],
    ['day' => 'Saturday', 'meal_type' => 'dinner', 'items' => 'Chapati, Kadai Paneer, Naan'],
    ['day' => 'Sunday', 'meal_type' => 'breakfast', 'items' => 'Bread, Butter, Jam, Cornflakes'],
    ['day' => 'Sunday', 'meal_type' => 'lunch', 'items' => 'Rice, Fish/Chicken Curry, Veg'],
    ['day' => 'Sunday', 'meal_type' => 'dinner', 'items' => 'Chapati, Egg Curry, Salad']
]);

initDataFile('attendance.json', []);
initDataFile('wastage.json', []);
initDataFile('feedback.json', []);

// Helper functions
function getNextId($data) {
    return empty($data) ? 1 : max(array_column($data, 'id')) + 1;
}

function getUserByUsername($username) {
    $users = readData('users.json');
    foreach ($users as $user) {
        if ($user['username'] === $username || $user['email'] === $username) {
            return $user;
        }
    }
    return null;
}

function getUserById($id) {
    $users = readData('users.json');
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            return $user;
        }
    }
    return null;
}

function getMenuByDay($day) {
    $menu = readData('menu.json');
    $result = [];
    foreach ($menu as $item) {
        if ($item['day'] === $day) {
            $result[] = $item;
        }
    }
    return $result;
}

function getUserAttendance($user_id, $date, $meal_type) {
    $attendance = readData('attendance.json');
    foreach ($attendance as $record) {
        if ($record['user_id'] == $user_id && $record['date'] === $date && $record['meal_type'] === $meal_type) {
            return $record;
        }
    }
    return null;
}
?>
