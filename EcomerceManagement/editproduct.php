<?php
require 'vendor/autoload.php'; 

$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test'); // Replace 'test' with your database name
$productCollection = $database->selectCollection('products');
$branchCollection = $database->selectCollection('branches');

// Lấy ID sản phẩm từ URL và chuyển đổi thành ObjectId
$id = new MongoDB\BSON\ObjectId($_GET['id']);

// Tìm sản phẩm trong MongoDB bằng ObjectId
$product = $productCollection->findOne(['_id' => $id]);

if (!$product) {
    echo "Sản phẩm không tồn tại.";
    exit;
}

// Lấy danh sách các chi nhánh
$branches = $branchCollection->find()->toArray();

if (isset($_POST['btnBack'])){
    header("location: ./listsanpham.php");
    exit;
}

if (isset($_POST['btnUpdate'])) {
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $category = $_POST['category'];
    $branchId = $_POST['branch']; // Lấy branchId từ form

    // Xử lý upload hình ảnh
    $countfiles = count($_FILES['anhs']['name']);
    $imgs = [];

    if (!empty($_FILES['anhs']['name'][0])) { // Nếu có hình ảnh mới được upload
        // Xóa ảnh cũ
        if (!empty($product['images'])) {
            foreach ($product['images'] as $img) {
                if (file_exists($img)) {
                    unlink($img); // Xóa file trên server
                }
            }
        }

        // Upload ảnh mới
        for ($i = 0; $i < $countfiles; $i++) {
            $filename = $_FILES['anhs']['name'][$i];
            $location = "uploads/" . uniqid() . $filename;
            $extension = strtolower(pathinfo($location, PATHINFO_EXTENSION));

            $valid_extensions = array("jpg", "jpeg", "png");

            if (in_array($extension, $valid_extensions)) {
                if (move_uploaded_file($_FILES['anhs']['tmp_name'][$i], $location)) {
                    $imgs[] = $location; // Thêm đường dẫn ảnh vào mảng
                }
            }
        }
    }

    // Cập nhật sản phẩm trong MongoDB
    $updateData = [
        'name' => $name,
        'description' => $description,
        'quantity' => $quantity,
        'price' => $price,
        'category' => $category,
        'branchId' => $branchId, // Lưu branchId
        'images' => !empty($imgs) ? $imgs : $product['images'], // Sử dụng hình ảnh mới hoặc giữ lại hình ảnh cũ
    ];

    // Thực hiện cập nhật
    $productCollection->updateOne(['_id' => $id], ['$set' => $updateData]);

    // Cập nhật sản phẩm trong chi nhánh tương ứng
    $branchCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($branchId)],
        ['$push' => ['products' => $updateData]] // Thêm sản phẩm vào mảng products của chi nhánh
    );

    // Chuyển hướng về trang danh sách sản phẩm
    header("location: ./listsanpham.php");
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
                            <h1 class="h4 text-gray-900 mb-4">Cập nhật sản phẩm</h1>
                        </div>
                        <form class="user" method="post" action="#" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="form-label">Product Name:</label>
                                <input type="text" class="form-control form-control-user"
                                       id="name" name="name" placeholder="Tên sản phẩm"
                                       value="<?= htmlspecialchars($product['name']) ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Product Images:</label>
                                <input type="file" class="form-control form-control-user" id="anhs" name="anhs[]" multiple>
                                <br>Các ảnh hiện tại:
                                <?php
                                if (!empty($product['images'])) {
                                    foreach ($product['images'] as $img) {
                                        echo "<img src='$img' height='100px' />";
                                    }
                                }
                                ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description:</label>
                                <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4 mb-sm-0">
                                    <label class="form-label">Quantity:</label>
                                    <input type="text" class="form-control form-control-user" id="quantity" name="quantity"
                                           placeholder="Nhập số lượng" value="<?= htmlspecialchars($product['quantity']) ?>">
                                </div>
                                <div class="col-sm-4 mb-sm-0">
                                    <label class="form-label">Price:</label>
                                    <input type="text" class="form-control form-control-user" id="price" name="price"
                                           placeholder="Nhập giá" value="<?= htmlspecialchars($product['price']) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Category:</label>
                                <input type="text" class="form-control form-control-user" id="category" name="category"
                                       placeholder="Nhập danh mục" value="<?= htmlspecialchars($product['category']) ?>">
                            </div>

                            <div class="form-group">
                                    <label class="form-label">Branch:</label>
                                    <select id="branch-select" name="branch" class="form-control">
                                        <?php foreach ($branches as $branch): ?>
                                            <option style="color: black;" value="<?= $branch['_id'] ?>" <?= ($branch['_id'] == $product['branchId']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($branch['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                            </div>


                            <button class="btn btn-success" name="btnUpdate">Cập nhật</button>
                            <button class="btn btn-danger" name="btnBack">Trở về</button>

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
