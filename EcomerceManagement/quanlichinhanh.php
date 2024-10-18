<?php 
require 'includes/header.php';
require 'vendor/autoload.php';
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test');
$collection = $database->selectCollection('branches');

$branches = $collection->find();


?>


<div class="card shadow mb-10 mt-10">
    <div class="form d-flex">
        <form class="d-none d-sm-inline-block form-inline mr-sm-4 ml-md-3 my-2 mw-100 navbar-search flex-grow-1 flex-shrink-1 flex-basis-auto" method="GET" action="listsanpham.php">
            <div class="input-group">
                <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Tìm kiếm chi nhánh..." aria-label="Search" aria-describedby="basic-addon2" value="" />
                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
        </form>
        <form action="" method="POST">
            <button type="submit" name="btnBack" class="btn btn-danger d-none d-sm-inline-block form-inline mr-sm-4 ml-md-3 my-2 mw-100">Trở về</button>
        </form>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Address</th>
                        <th>PhoneNumber</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // if ($productCount === 0) {
                //     echo "<tr><td colspan='6'>Không tìm thấy sản phẩm nào.</td></tr>";
                // } else {
                    foreach ($branches as $branch) {
                ?>
                    <tr>
                        <td><?=$branch['name']?></td>
                        <td><?=$branch['code']?></td>
                        <td><?=$branch['address']?></td>
                        <td><?=$branch['phoneNumber']?></td>
                        <td>
                            <a class="btn btn-warning" href="editbranch.php?id=<?=$branch['_id']?>">Edit</a>
                            <a class="btn btn-success" href="viewbranches.php?id=<?=$branch['_id']?>" >Xem chi tiết</a>
                            <a class="btn btn-danger" href="deletebranch.php?id=<?=$branch['_id']?>" onclick="return confirm('Bạn chắc chắn xóa sản phẩm này?');">Delete</a>

                        </td>
                    </tr>
                <?php
                    }
                // }
                ?>
                </tbody>
            </table>
        <button class="btn btn-primary" onclick="window.location.href='index.php'">Quay lại</button>

        </div>

<?php
require 'includes/footer.php';
?>