<?php 
require('includes/header.php');
require 'vendor/autoload.php';

// Kết nối MongoDB
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test');
$branchcollection = $database->selectCollection('branches');

// Lấy danh sách chi nhánh
$branches = iterator_to_array($branchcollection->find()); // Chuyển cursor thành mảng
?>

<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Thêm mới sản phẩm</h1>
                        </div>
                        <form class="user" method="post" action="addproduct.php" enctype="multipart/form-data">                        
                            <div class="form-group">
                                <label class="form-label">Tên sản phẩm:</label>
                                <input type="text" class="form-control form-control-user" id="name" name="name" placeholder="Tên sản phẩm" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Hình ảnh sản phẩm</label>
                                <input type="file" class="form-control form-control-user" id="anhs" name="anhs[]" multiple required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Mô tả:</label>
                                <textarea name="description" class="form-control" placeholder="Nhập mô tả..." required></textarea>
                            </div>
                            
                            <div class="form-group" style="color: black;">
    <label class="form-label">Chi nhánh:</label>
    <select class="form-control" name="branch" id="branchSelect" required>
        <?php foreach ($branches as $branch): ?>
            <option value="<?= (string)$branch['_id'] ?>" data-branch-name="<?= $branch['name'] ?>"><?php echo $branch['name'] ?></option>
        <?php endforeach; ?>
    </select>
</div>

                            <div class="form-group row">
                                <div class="col-sm-4 mb-sm-0">
                                    <label class="form-label">Số lượng:</label>
                                    <input type="text" class="form-control form-control-user" id="quantity" name="quantity" placeholder="Số lượng" required> 
                                </div>
                                <div class="col-sm-8 mb-sm-0">
                                    <label class="form-label">Giá:</label>
                                    <input type="text" class="form-control form-control-user" id="price" name="price" placeholder="Giá" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Danh mục:</label>
                                <input type="text" class="form-control form-control-user" id="category" name="category" placeholder="Danh mục" required>
                            </div>

                            <button class="btn btn-success" type="submit">Tạo mới</button>
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
?>
