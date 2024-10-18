<?php
ob_start(); // Bắt đầu buffer
require 'includes/header.php';
require 'vendor/autoload.php';

// Kết nối MongoDB
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test');
$productCollection = $database->selectCollection('products');
$branchCollection = $database->selectCollection('branches');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = (int)$_POST['quantity'];
    $price = (int)$_POST['price'];
    $category = $_POST['category'];
    $branchId = $_POST['branch'];

    // Xử lý hình ảnh
    $images = [];
    if (isset($_FILES['anhs'])) {
        foreach ($_FILES['anhs']['tmp_name'] as $key => $tmpName) {
            $fileName = $_FILES['anhs']['name'][$key];
            $filePath = 'uploads/' . $fileName; // Đường dẫn lưu hình ảnh
            move_uploaded_file($tmpName, $filePath);
            $images[] = $filePath; // Lưu đường dẫn vào mảng
        }
    }

    // Tạo sản phẩm
    $product = [
        'name' => $name,
        'description' => $description,
        'images' => $images,
        'quantity' => $quantity,
        'price' => $price,
        'category' => $category,
        'branchId' => $branchId,
        'rating' => [],
        '__v'=> 1
    ];

    $productId = $productCollection->insertOne($product)->getInsertedId();

    $branchCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($branchId)],
        [
            '$addToSet' => [
                'products' => $product
            ],
            '$inc' => ['totalQuantity' => $quantity] // Cập nhật tổng số lượng sản phẩm
        ]
    );

    // Chuyển hướng về trang danh sách sản phẩm sau khi thêm
    header('Location: listsanpham.php');
    exit;
}

ob_end_flush(); // Kết thúc buffer
?>
