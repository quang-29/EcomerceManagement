<?php
require 'vendor/autoload.php'; 

$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test'); // Thay 'test' bằng tên cơ sở dữ liệu của bạn
$collection = $database->selectCollection('users'); // Sửa thành 'users'
$collection1 = $database->selectCollection('branches'); // Sửa thành 'branches'

// Lấy ID chi nhánh từ URL và chuyển đổi thành ObjectId

$id = new MongoDB\BSON\ObjectId($_GET['id']);

// Tìm chi nhánh trong MongoDB bằng ObjectId
$branch = $collection1->findOne(['_id' => $id]); // Tìm trong collection 'branches'
$user = $collection->findOne(['_id' => $id]);
if (!$branch) {
    echo "Chi nhánh không tồn tại.";
    exit;
}

if (isset($_POST['btnBack'])){
    header("Location: ./quanlichinhanh.php"); // Chuyển hướng về trang danh sách chi nhánh
    exit;
}

if (isset($_POST['btnUpdate'])) {
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $branchCode = $_POST['code'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $established = new MongoDB\BSON\UTCDateTime(new DateTime($_POST['establish'])); // Chuyển đổi thành UTCDateTime

    // Cập nhật chi nhánh trong MongoDB
    $updateData = [
        'name' => $name,
        'code' => $branchCode,
        'address' => $address,
        'phoneNumber' => $phoneNumber,
        'email' => $email,
        'establish' => $established,
    ];
    
    // Cập nhật trong cả hai collection
    $collection->updateOne(['_id' => $id], ['$set' => $updateData]); // Cập nhật collection 'users'
    $collection1->updateOne(['_id' => $id], ['$set' => $updateData]); // Cập nhật collection 'branches'

    header("Location: ./quanlichinhanh.php");
    exit;
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
                            <h1 class="h4 text-gray-900 mb-4">Cập nhật chi nhánh</h1>
                        </div>
                        <form class="user" method="post" action="#" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="form-label">Tên chi nhánh:</label>
                                <input type="text" class="form-control form-control-user"
                                       id="name" name="name" placeholder="Tên chi nhánh"
                                       value="<?= htmlspecialchars($branch['name']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Mã chi nhánh:</label>
                                <input type="text" class="form-control form-control-user"
                                       id="code" name="code" placeholder="Mã chi nhánh"
                                       value="<?= htmlspecialchars($branch['code']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Địa chỉ:</label>
                                <input type="text" class="form-control form-control-user" id="address" name="address"
                                       placeholder="Địa chỉ" value="<?= htmlspecialchars($branch['address']) ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Số điện thoại:</label>
                                <input type="text" class="form-control form-control-user" id="phoneNumber" name="phoneNumber"
                                       placeholder="Số điện thoại" value="<?= htmlspecialchars($branch['phoneNumber']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email:</label>
                                <input type="text" class="form-control form-control-user" id="email" name="email"
                                       placeholder="Email" value="<?= htmlspecialchars($branch['email']) ?>" required>
                            </div>
                            <button class="btn btn-success" name="btnUpdate" type="submit">Cập nhật</button>
                            <button class="btn btn-danger" name="btnBack" type="submit">Trở về</button>
                        </form>
                        <hr>
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
