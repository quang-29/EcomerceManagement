<?php
// Kết nối MongoDB
require 'vendor/autoload.php';
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test');
$ordersCollection = $database->selectCollection('orders');
$usersCollection = $database->selectCollection('users');
$branchcollection = $database->selectCollection('branches');

function formatDate($timestamp) {
    $date = new DateTime('@' . ($timestamp / 1000)); // Chia cho 1000 để chuyển từ mili giây sang giây
    return $date->format('Y-m-d H:i:s');
}

// Lấy ID đơn hàng từ URL
$id = $_GET['id'];

// Tìm đơn hàng trong MongoDB
$order = $ordersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);

if (!$order) {
    echo "Không tìm thấy đơn hàng.";
    exit;
}

// Tìm người dùng từ MongoDB
$user = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($order['userId'])]);

if (!$user) {
    echo "Không tìm thấy người dùng.";
    exit;
}

// Tìm chi nhánh từ MongoDB dựa trên branchId trong đơn hàng
$branch = $branchcollection->findOne(['_id' => new MongoDB\BSON\ObjectId($order['branchId'])]);

if (!$branch) {
    echo "Không tìm thấy chi nhánh.";
    exit;
}

if (isset($_POST['btnBack'])) {
    // Điều hướng lại danh sách đơn hàng
    header("Location: ./listorders.php");
    exit;
}

if (isset($_POST['btnUpdate'])) {
    // Lấy dữ liệu từ form
    $status = $_POST['status'];

    // Cập nhật trạng thái đơn hàng
    $ordersCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($id)],
        ['$set' => ['status' => $status]]
    );

    // Chuyển hướng về trang danh sách đơn hàng
    header("location: ./listorders.php");
} else {
    require('includes/header.php');
    ?>
    
<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Chi tiết đơn hàng</h1>
                        </div>
                        <div class="row">
                            <div class="col-md-5" style="font-size:0.9rem;">
                                <form class="user" method="post" action="#">
                                    <div class="row">
                                        <div class="col-md-3">Người đặt:</div>
                                        <div class="col-md-9">
                                            <?= htmlspecialchars($user['name']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">Thời gian đặt:</div>
                                        <div class="col-md-9">
                                            <?= formatDate($order['orderedAt']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">Địa chỉ:</div>
                                        <div class="col-md-9">
                                            <?= htmlspecialchars($order['address']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">Chi nhánh:</div>
                                        <div class="col-md-9">
                                            <?= htmlspecialchars($branch['name']) ?>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-3">Trạng thái:</div>
                                        <div class="col-md-9">
                                            <select name="status">
                                                <option value="0" <?= $order['status'] == 0 ? 'selected' : '' ?>>Đơn hàng đã được đặt</option>
                                                <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>Sẵn sàng để vận chuyển</option>
                                                <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>Đã lấy</option>
                                                <option value="3" <?= $order['status'] == 3 ? 'selected' : '' ?>>Đang trên đường giao</option>
                                                <option value="4" <?= $order['status'] == 4 ? 'selected' : '' ?>>Tiến hành giao hàng</option>
                                                <option value="5" <?= $order['status'] == 5 ? 'selected' : '' ?>>Đã giao</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button class="btn btn-primary w-100" name="btnUpdate">Cập nhật</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-danger w-100" name="btnBack">Trở về</button>  
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-7">
    <table class="table">
        <tr>
            <th>STT</th>
            <th>Sản phẩm</th>
            <th>Hình ảnh</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
        </tr>
        <?php
        $stt = 0;
        $tongtien = 0;
        foreach ($order['products'] as $product) {
            $stt++;
            $productName = $product['product']['name'];
            $price = $product['product']['price'];
            $quantity = $product['quantity'];
            $imageUrl = $product['product']['images'][0]; 
            $total = $price * $quantity;
            $tongtien += $total;
            ?>
            <tr>
                <td><?= $stt ?></td>
                <td><?= htmlspecialchars($productName) ?></td>
                <td>
                    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($productName) ?>" width="50" height="50">
                </td>
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
            Tổng tiền:
            <?= number_format($tongtien, 0, '', '.') . " VNĐ" ?>
        </h5>
    </div>
</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    require('includes/footer.php');
}
?>
