<?php
require 'includes/header.php';
require 'vendor/autoload.php';
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test');
$collection = $database->selectCollection('products');
$usercollection = $database->selectCollection('users');
$branchcollection = $database->selectCollection('branches');

if (isset($_POST['btnBack'])) {
    echo "<script>window.location.href='./listsanpham.php';</script>";
    exit;
}

function anhdaidien($arr, $height) {
    if (!empty($arr) && isset($arr[0])) {
        return "<img src='$arr[0]' height='$height' />";
    }
    return "<img src='default.jpg' height='$height' />";
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$selectedBranch = isset($_GET['branch']) ? $_GET['branch'] : ''; // Lấy chi nhánh từ dropdown
$filter = [];

if ($search) {
    $filter['name'] = ['$regex' => $search, '$options' => 'i'];
}

if ($selectedBranch) {
    $filter['branchId'] = new MongoDB\BSON\ObjectId($selectedBranch); // Lọc theo chi nhánh
}

$limit = 8; // Hiển thị 8 sản phẩm mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Trang hiện tại
$skip = ($page - 1) * $limit; // Số sản phẩm cần bỏ qua

// Đếm tổng số sản phẩm
$productCount = $collection->countDocuments($filter);

// Tìm sản phẩm dựa trên filter, skip và limit, và chuyển đổi Cursor thành mảng
$productsCursor = $collection->find($filter, ['skip' => $skip, 'limit' => $limit]);
$products = iterator_to_array($productsCursor); // Chuyển con trỏ thành mảng

$totalPages = ceil($productCount / $limit); // Tổng số trang



?>

<div class="card shadow mb-10 mt-10">
    <div class="form d-flex">
        <form class="d-none d-sm-inline-block form-inline mr-sm-4 ml-md-3 my-2 mw-100 navbar-search flex-grow-1 flex-shrink-1 flex-basis-auto" method="GET" action="listsanpham.php">
            <div class="input-group">
                <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Tìm kiếm sản phẩm..." aria-label="Search" aria-describedby="basic-addon2" value="<?= htmlspecialchars($search) ?>" />
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
                        <th>Product name</th>
                        <th>Product image</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Branch</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
<?php
// Lấy danh sách branchId duy nhất từ sản phẩm
$branchIds = array_unique(array_map(function($product) {
    return $product['branchId'];
}, $products));

// Truy vấn tất cả các chi nhánh và ánh xạ chúng với branchId
$branchesCursor = $branchcollection->find(); 
$branchesMap = [];

foreach ($branchesCursor as $branch) {
    $branchesMap[(string)$branch['_id']] = $branch['name'];
}


foreach ($products as $product) {
    $branchName = isset($branchesMap[(string)$product['branchId']]) ? $branchesMap[(string)$product['branchId']] : 'Unknown';
?>
    <tr>
        <td><?=$product['name']?></td>
        <td><?=anhdaidien($product['images'], "100px")?></td>
        <td><?=$product['category']?></td>
        <td><?=$product['price']?></td>
        <td><?=$product['quantity']?></td>
        <td><?=$branchName?></td>

        <td>
            <a class="btn btn-warning" href="editproduct.php?id=<?=$product['_id']?>">Edit</a>
            <a class="btn btn-danger" href="deleteproduct.php?id=<?=$product['_id']?>" onclick="return confirm('Bạn chắc chắn xóa sản phẩm này?');">Delete</a>
        </td>
    </tr>
<?php
}
?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>&branch=<?= htmlspecialchars($selectedBranch) ?>" style="color: black;">Previous</a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>&branch=<?= htmlspecialchars($selectedBranch) ?>" style="color: black;"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>&branch=<?= htmlspecialchars($selectedBranch) ?>" style="color: black;">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?php
require 'includes/footer.php';
?>
