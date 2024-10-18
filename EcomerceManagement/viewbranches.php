<?php 
require 'includes/header.php';
require 'vendor/autoload.php';

$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test');
$collection = $database->selectCollection('branches');
$productCollection = $database->selectCollection('products');

// Lấy ID chi nhánh từ URL
$branchId = $_GET['id'];

// Tìm chi nhánh trong MongoDB
$branch = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($branchId)]);
$productsCursor = $productCollection->find(['branchId' => $branchId]);

// Chuyển con trỏ MongoDB thành mảng để sử dụng nhiều lần
$products = iterator_to_array($productsCursor);

if (!$branch) {
    echo "Không tìm thấy chi nhánh.";
    exit;
}

?>

<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-4">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Chi tiết chi nhánh</h1>
                        </div>
                        <div class="row" style="font-size:0.9rem;">
                            
                        <p><strong>Mã chi nhánh:</strong> <?= htmlspecialchars($branch['code']) ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($branch['address']) ?></p>
                        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($branch['phoneNumber']) ?></p>
                        <p><strong>Ngày thành lập:</strong> 
                            <?= htmlspecialchars($branch['establish']->toDateTime()->format('d-m-Y')) ?>
                        </p>
                        <p><strong>Giờ mở cửa:</strong> 
                            <?= htmlspecialchars(substr($branch['openHour'], 0, 2)) . ' AM' ?>
                        </p>
                        <p><strong>Giờ đóng cửa:</strong> 
                            <?= htmlspecialchars(substr($branch['closeHour'], 0, 2)) . ' PM' ?>
                        </p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($branch['email']) ?></p>

                        
                        </div>
                        <div class="row">
                            <p><strong>Sản phẩm của chi nhánh</strong></p>
                        </div>
                        <div class="row">
                                <table class="table">
                                    <tr>
                                        <th>STT</th>
                                        <th>Sản phẩm</th>
                                        <th>Hình ảnh</th>
                                        <th>Danh mục</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                    </tr>
                                    <?php
                                    if (count($products) === 0) {
                                        echo "<tr><td colspan='6'>Chi nhánh này hiện chưa có sản phẩm nào.</td></tr>";
                                    } else {
                                        $stt = 0;
                                        foreach ($products as $product) {
                                            $stt++;
                                            ?>
                                            <tr>
                                                <td><?= $stt ?></td>
                                                <td><?= htmlspecialchars($product['name']) ?></td>
                                                <td>
                                                    <img src="<?= htmlspecialchars($product['images'][0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="50" height="50">
                                                </td>
                                                <td><?= htmlspecialchars($product['category']) ?></td>
                                                <td><?= number_format($product['price'], 0, '', '.') ?> VNĐ</td>
                                                <td><?= $product['quantity'] ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                        </div>

                        <div class="row">
                            
                            <div class="col-6">
                                <a href="quanlichinhanh.php" class="btn btn-primary">Quay lại danh sách chi nhánh</a>

                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require 'includes/footer.php';
?>
