<?php
require 'vendor/autoload.php'; 
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);

$database = $client->selectDatabase('test'); 
$ordersCollection = $database->selectCollection('orders');

// Lấy branch_id từ yêu cầu AJAX
$branch_id = $_POST['branch_id'];
// $branch_id = "6707a5ff85ab70de4b9092c3";

// Hàm format lại timestamp
function formatDate($timestamp) {
    $date = new DateTime('@' . ($timestamp / 1000)); // Chia timestamp cho 1000 vì nó có thể là mili giây
    return $date->format('Y-m-d H:i:s');
}

// Giả sử doanh thu được lưu theo chi nhánh trong collection orders
// Truy vấn đơn giản để lấy tổng doanh thu theo chi nhánh
$orders = $ordersCollection->find(['branchId' => $branch_id]);

// Dữ liệu doanh thu giả sử dựa trên tháng
$revenueData = [];
foreach ($orders as $order) {
    if (is_double($order['orderedAt'])) {
        $timestamp = $order['orderedAt'];
    } else {
        continue; // Bỏ qua đơn hàng này
    }

    $formattedDate = formatDate($timestamp);
    $month = date('n', strtotime($formattedDate));

    if (!isset($revenueData[$month])) {
        $revenueData[$month] = 0;
    }
    $revenueData[$month] += $order['totalPrice'];
}

// Chuẩn bị dữ liệu để trả về
$labels = [];
$revenue = [];

// Mảng tên tháng
$monthNames = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];

for ($i = 1; $i <= 12; $i++) {
    $labels[] = $monthNames[$i]; // Thay đổi từ số tháng sang tên tháng
    $revenue[] = isset($revenueData[$i]) ? $revenueData[$i] : 0;
}

echo json_encode([
    'labels' => $labels,
    'revenue' => $revenue
]);
?>
