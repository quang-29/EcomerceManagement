 <?php 
require('includes/header.php');
?> 

<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tên Danh Mục</th>
                        <th>Số Loại Sản Phẩm</th>
                        <th>Số Lượng Tất Cả</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    require 'vendor/autoload.php';
                    use MongoDB\Client;

                    // Kết nối tới MongoDB
                    $uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
                    $client = new MongoDB\Client($uri);
                    $database = $client->selectDatabase('test');
                    $productCollection = $database->selectCollection('products');

                    // Nhóm các sản phẩm theo danh mục và tính tổng số lượng sản phẩm cho từng danh mục
                    $categories = $productCollection->aggregate([
                        [
                            '$group' => [
                                '_id' => '$category',
                                'productCount' => ['$sum' => 1], // Đếm số lượng loại sản phẩm
                                'totalQuantity' => ['$sum' => '$quantity'] // Tổng số lượng tất cả các sản phẩm
                            ]
                        ]
                    ]);

                    foreach ($categories as $category) {
                        $categoryName = $category['_id']; // Tên danh mục từ product['category']
                        $productCount = $category['productCount'];
                        $totalQuantity = $category['totalQuantity'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($categoryName) ?></td>
                            <td><?= htmlspecialchars($productCount) ?></td>
                            <td><?= htmlspecialchars($totalQuantity) ?></td>
                            <td>
                                <a class="btn btn-success" href="categorydetail.php?name=<?= urlencode($categoryName) ?>">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php 
                    } 
                    ?>                                
                </tbody>
            </table>
        <button class="btn btn-primary" onclick="window.location.href='index.php'">Quay lại</button>

        </div>
    </div>
</div>

<?php
require('includes/footer.php');
?>
