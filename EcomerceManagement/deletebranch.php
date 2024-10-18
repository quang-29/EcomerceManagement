<?php
require 'vendor/autoload.php'; // Composer autoload

$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test'); // Thay 'test' bằng tên cơ sở dữ liệu của bạn
$collection = $database->selectCollection('users');
$collection1 = $database->selectCollection('branches');

// Lấy ID từ URL và kiểm tra tính hợp lệ
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $delid = $_GET['id'];

    // Xóa người dùng trong collection 'users'
    $resultUser = $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($delid)]);
    
    // Xóa chi nhánh trong collection 'branches'
    $resultBranch = $collection1->deleteOne(['_id' => new MongoDB\BSON\ObjectId($delid)]);

    // Kiểm tra xem có xóa thành công hay không
    if ($resultUser->getDeletedCount() > 0 || $resultBranch->getDeletedCount() > 0) {
        // Thông báo xóa thành công
        echo "Đã xóa thành công.";
    } else {
        // Thông báo nếu không tìm thấy chi nhánh hoặc người dùng
        echo "Không tìm thấy chi nhánh hoặc người dùng.";
    }
} else {
    echo "ID không hợp lệ.";
}

// Quay lại trang danh sách chi nhánh
header("Location: quanlichinhanh.php");
exit();
?>
