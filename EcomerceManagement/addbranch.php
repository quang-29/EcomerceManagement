<?php
require 'vendor/autoload.php'; // Composer autoload

$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test'); // Thay 'test' bằng tên cơ sở dữ liệu của bạn
$collection = $database->selectCollection('users'); // Thay 'products' bằng 'users' hoặc tên collection chi nhánh
$collection1 = $database->selectCollection('branches');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Thêm dòng này để kiểm tra dữ liệu gửi lên
    print_r($_POST); 

    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $branchCode = $_POST['branchCode'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phoneNumber'];

    // Chuyển đổi ngày thành lập về UTC
    $establishedDate = new DateTime($_POST['established'], new DateTimeZone('Asia/Ho_Chi_Minh')); // Thay đổi múi giờ phù hợp
    $established = new MongoDB\BSON\UTCDateTime($establishedDate->setTimezone(new DateTimeZone('UTC'))->getTimestamp() * 1000); // Chuyển đổi thành UTCDateTime

    $branchId = new MongoDB\BSON\ObjectId();

    $branchData = [
        '_id' => $branchId,
        'name' => $name,
        'code' => $branchCode,
        'address' => $address,
        'phoneNumber' => $phoneNumber,
        'establish' => $established,
        'openHour' => "06:00:00",
        'closeHour' => "22:00:00",
        'status' => 'active',
        'email' => $email,
        'managerId' => null, 
        '__v' => 1
    ];
    
    // Tạo chi nhánh trong collection 'users' với cùng ObjectId
    $userData = [
        '_id' => $branchId,
        'name' => $name,
        'email' => $email,
        'password' => password_hash('123', PASSWORD_BCRYPT), 
        'phoneNumber' => $phoneNumber,
        'type' => 'branch', 
        'status' => 'active',
        'code' => $branchCode,
        'establish' => $established,
        'cart' => [],
        '__v' => 0,
        'address' => $address,
    ];
    
    // Chèn dữ liệu vào collection
    $collection1->insertOne($branchData); // Sửa từ $database->branches->insertOne
    $collection->insertOne($userData); // Sửa từ $database->users->insertOne

    header("location: ./quanlichinhanh.php");
    exit;
}

require('includes/header.php');
?>

<!-- Nội dung thêm chi nhánh ở đây -->

<?php
require('includes/footer.php');
?>
