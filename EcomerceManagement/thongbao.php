<?php
require 'includes/header.php';
require 'vendor/autoload.php';

$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);

$database = $client->selectDatabase('test');
$collection = $database->selectCollection('orders');

// Tìm tất cả các đơn hàng có status là '0'
$orders = $collection->find(['status' => '0']);
?>

<body>
    <h4 style="color: #1cc88a;">Thông báo Đơn Hàng</h4>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; text-align: left;">Thông báo</th>
                <th style="padding: 10px; text-align: left;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <?php
                    $orderId = (string)$order['_id'];
                    $userId = isset($order['userId']) ? $order['userId'] : 'Chưa rõ';
                    $totalPrice = isset($order['totalPrice']) ? number_format($order['totalPrice'], 0, ',', '.') : '0';
                    
                ?>
                <tr>
                    <td style="padding: 10px;">
                        Người dùng có ID: <?= htmlspecialchars($userId) ?> đã đặt đơn hàng có mã: <?= htmlspecialchars($orderId) ?> 
                        với tổng tiền là: <?= htmlspecialchars($totalPrice) ?>
                    </td>
                    <td style="padding: 10px;">
                        <a class="btn btn-success" href="vieworders.php?id=<?= htmlspecialchars($orderId) ?>" 
                            style="padding: 5px 10px; text-decoration: none;">Xem chi tiết</a>
                        
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

<?php
require 'includes/footer.php';
?>
