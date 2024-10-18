<?php
require 'vendor/autoload.php'; // Composer autoload
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test'); // Replace 'test' with your database name
$collection = $database->selectCollection('products');

$delid = $_GET['id'];
$collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($delid)]);


// Quay lại trang danh sách sản phẩm
header("Location: listsanpham.php");
exit();
