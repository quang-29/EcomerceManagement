<?php 

require 'includes/header.php';

// Kết nối MongoDB
require 'vendor/autoload.php';
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test');
$productCollection = $database->selectCollection('products');

// Lấy tên danh mục từ URL
$categoryName = $_GET['name'] ?? '';

// Tìm sản phẩm thuộc danh mục đó
$products = $productCollection->find(['category' => $categoryName]);

if (!$products) {
    echo "Không tìm thấy sản phẩm trong danh mục này.";
    exit;
}


?>

<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Chi tiết danh mục: <?= htmlspecialchars($categoryName) ?></h1>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="text-success">Sản phẩm trong danh mục</h4>
                                <table class="table">
                                    <tr>
                                        <th>STT</th>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Tổng tiền</th>
                                    </tr>
                                    <?php
                                    $stt = 0;
                                    $tongtien = 0;
                                    foreach ($products as $product) {
                                        $stt++;
                                        $productName = $product['name'];
                                        $price = $product['price'];
                                        $quantity = $product['quantity'];
                                        $total = $price * $quantity;
                                        $tongtien += $total;

                                        // Lấy ảnh sản phẩm (chỉnh sửa nếu trường 'image' khác)
                                        $imageUrl = !empty($product['images'][0]) ? $product['images'][0] : 'path/to/default-image.jpg'; // Đặt đường dẫn mặc định nếu không có ảnh
                                        ?>
                                        <tr>
                                            <td><?= $stt ?></td>
                                            <td>
                                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($productName) ?>" width="80" height="80">
                                            </td>
                                            <td><?= htmlspecialchars($productName) ?></td>
                                            <td><?= number_format($price, 0, '', '.') . " VNĐ" ?></td>
                                            <td><?= $quantity ?></td>
                                            <td><?= number_format($total, 0, '', '.') . " VNĐ" ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <div class="tongtien">
                                    <h5>
                                        Tổng tiền tất cả sản phẩm trong danh mục:
                                        <?= number_format($tongtien, 0, '', '.') . " VNĐ" ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <a href="listcats.php" class="btn btn-success">Trở về danh sách danh mục</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require('includes/footer.php');
?>
