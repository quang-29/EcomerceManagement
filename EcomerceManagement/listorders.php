<?php 
require('includes/header.php');
require 'vendor/autoload.php';
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';

try {
    $client = new MongoDB\Client($uri);
    $database = $client->selectDatabase('test'); // Thay 'test' bằng tên cơ sở dữ liệu của bạn
    $collection = $database->selectCollection('orders'); // Thay 'orders' bằng tên collection của bạn
} catch (Exception $e) {
    die("Không thể kết nối tới MongoDB: " . $e->getMessage());
}

// Trả về chuỗi trạng thái
function getStatusText($status) {
    switch($status) {
        case 0:
            return 'Đơn hàng đã được đặt';
        case 1:
            return 'Sẵn sàng để vận chuyển';
        case 2:
            return 'Đã lấy';
        case 3:
            return 'Đang trên đường giao';
        case 4:
            return 'Tiến hành giao hàng';
        case 5:
            return 'Đã giao';
        default:
            return 'Unknown';
    }
}


function getStatusClass($status) {
    switch($status) {
        case 0:
            return 'Ordered';
        case 1:
            return 'ReadyToTransport';
        case 2:
            return 'IsTaken';
        case 3:
            return 'Transporting';
        case 4:
            return 'Shipping';
        case 5:
            return 'Shipped';
        default:
            return 'Unknown';
    }
}


function formatDate($timestamp) {
    $date = new DateTime('@' . ($timestamp / 1000)); 
    return $date->format('Y-m-d H:i:s');
}

if (isset($_POST['btnBack'])) {
    header("Location: ./listorders.php");
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = [];
if ($search) {
    try {
        $objectId = new MongoDB\BSON\ObjectId($search);
        $filter['_id'] = $objectId;
    } catch (Exception $e) {
        
        $filter['_id'] = new MongoDB\BSON\Regex($search, 'i');
    }
}

try {
    
    $orders = $collection->find($filter, ['sort' => ['orderedAt' => -1]]);
    $orderCount = $orders->toArray(); 
} catch (Exception $e) {
    die("Có lỗi xảy ra khi truy xuất dữ liệu: " . $e->getMessage());
}


    
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$skip = ($page - 1) * $limit; 
try {
    
    $totalOrders = $collection->countDocuments($filter);

    // Lấy dữ liệu đơn hàng với bộ lọc, giới hạn và bỏ qua theo trang
    $orders = $collection->find($filter, [
        'sort' => ['orderedAt' => -1],
        'limit' => $limit,
        'skip' => $skip
    ]);
    $orderCount = $orders->toArray(); // Chuyển dữ liệu thành mảng
} catch (Exception $e) {
    die("Có lỗi xảy ra khi truy xuất dữ liệu: " . $e->getMessage());
}
// Tính tổng số trang
$totalPages = ceil($totalOrders / $limit);


?>
<style>
    .Ordered, .ReadyToTransport, .IsTaken, .Transporting, .Shipping, .Shipped {
        display: block;
        padding: 5px 10px;
        border-radius: 5px;
        color: white;
    }
    .Ordered {
        background-color: #8000ff; 
    }
    .ReadyToTransport {
        background-color: #9acd32; 
    }
    .IsTaken {
        background-color: #ff7f50; 
    }
    .Transporting {
        background-color: #32cd32;
    }
    .Shipping {
        background-color: #ff6347; 
    }
    .Shipped {
        background-color: #ffa500; 
    }
</style>
<div>
    <div class="card shadow mb-4">

    <div class="form d-flex">
    <form class="d-none d-sm-inline-block form-inline mr-sm-4 ml-md-3 my-2 mw-100 navbar-search flex-grow-1 flex-shrink-1 flex-basis-auto" method="GET" action="listorders.php">
        <div class="input-group">
            <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Tìm kiếm mã đơn hàng..." aria-label="Search" aria-describedby="basic-addon2" value="<?= htmlspecialchars($search) ?>" />
            <div class="input-group-append">
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>
    <!-- Form Trở về -->
    <form action="./listorders.php" method="POST">
        <button type="submit" class="btn btn-danger d-none d-sm-inline-block form-inline mr-sm-4 ml-md-3 my-2 mw-100">Trở về</button>
    </form>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày giờ đặt</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>                   
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($orderCount) === 0) {
                        echo "<tr><td colspan='4'>Không tìm thấy kết quả nào cho mã đơn hàng đã nhập.</td></tr>";
                    } else {
                        foreach ($orderCount as $order) {
                            $statusText = getStatusText($order['status']);
                            $statusClass = getStatusClass($order['status']);
                            $formattedDate = formatDate($order['orderedAt']);
                            $orderNumber = (string)$order['_id']; // Chuyển đổi ObjectId thành chuỗi
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($orderNumber) ?></td>
                                <td><?= htmlspecialchars($formattedDate) ?></td>
                                <td>
                                    <span class='btn <?= htmlspecialchars($statusClass) ?>'><?= htmlspecialchars($statusText) ?></span>
                                </td>
                                <td><?php echo number_format($order['totalPrice']) . ' VND'; ?></td>
                                <td>
                                    <a class="btn btn-success" href="vieworders.php?id=<?= htmlspecialchars($orderNumber) ?>">Xem chi tiết</a>  
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>                                  
                </tbody>
            </table>
        </div>

        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?> " style="color: black;">Previous</a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>" style="color: black;"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>" style="color: black;">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <button class="btn btn-primary" onclick="window.location.href='index.php'">Quay lại</button>

    </div>

    

</div>

<?php
require('includes/footer.php');
?>