<?php
    require 'vendor/autoload.php'; 
    $uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
    
    $client = new MongoDB\Client($uri);
    // Kết nối đến cơ sở dữ liệu và collection orders
    $database = $client->selectDatabase('test'); 
    $collection = $database->selectCollection('orders');  
    $collection1 = $database->selectCollection('products'); 
    $userCollection = $database->selectCollection('users');

    if (!isset($_SESSION['user_email'])) {
        // Show a message and redirect to login page
        echo "<script>
            alert('Vui lòng đăng nhập để tiếp tục');
            window.location.href = 'login.php';
        </script>";
        exit();
      }
        
        $username = '';
        $userEmail = $_SESSION['user_email'];
        $user = $userCollection->findOne(['email' => $userEmail]);
        $username = $user['name'];
    
    // Đếm tổng số user
    $totalUsers = $userCollection->countDocuments(['type' => 'user']); 
    
    // Đếm tổng số order
    $orderCount = $collection->countDocuments();
    
    
    // Truy vấn doanh thu theo tháng
    $pipeline = [
      [
          '$group' => [
              '_id' => [
                  'month' => [ '$month' => [ '$toDate' => ['$multiply' => ['$orderedAt', 1000]] ] ] // Chỉ nhóm theo tháng
              ],
              'totalEarnings' => [ '$sum' => '$totalPrice' ]
          ]
      ],
      [ '$sort' => [ '_id.month' => 1 ] ] // Sắp xếp theo tháng
    ];
    
    
    
    // Đếm tổng số sản phẩm gần hết hàng
    $totalAlmostOutOfStock = $collection1->countDocuments(['quantity' => ['$lte' => 100]]);
    
    
    // Đếm số sản phẩm hết hàng
    $totalOutOfStock = $collection1->countDocuments(['quantity' => ['$lte' => 0]]);
    
    $aggregation = $collection->aggregate([
        [
            '$group' => [
                '_id' => null, 
                'totalSales' => ['$sum' => '$totalPrice'] // Tính tổng totalPrice
            ]
        ]
    ]);
    
    
    $totalSales = 0;
    foreach ($aggregation as $doc) {
        if (isset($doc['totalSales'])) {
            $totalSales = $doc['totalSales'];
        }
    }
    
    
    $totalQuantity = $collection1->aggregate([
        [
            '$group' => [
                '_id' => null, 
                'totalQuantity' => ['$sum' => '$quantity'] // Tính tổng quantity
            ]
        ]
    ]);
    
    
    $totalQuantityValue = 0;
    foreach ($totalQuantity as $doc) {
        if (isset($doc['totalQuantity'])) {
            $totalQuantityValue = $doc['totalQuantity'];
        }
    }
    
            // Nhóm các sản phẩm theo danh mục và tính tổng số lượng danh mục
            $categories = $collection1->aggregate([
                [
                    '$group' => [
                        '_id' => '$category',
                    ]
                ]
            ]);
    
            // Biến đếm tổng số lượng danh mục
            $totalCategories = 0;
    
            foreach ($categories as $category) {
                // Tăng biến đếm danh mục cho mỗi danh mục tìm được
                $totalCategories++;
            }
?>