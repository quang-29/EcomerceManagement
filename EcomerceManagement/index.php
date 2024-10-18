
<?php
require 'includes/header.php';
require 'vendor/autoload.php'; 
    $uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
    $client = new MongoDB\Client($uri);
    
    $database = $client->selectDatabase('test'); 
    $collection = $database->selectCollection('orders');  
    $collection1 = $database->selectCollection('products'); 
    $userCollection = $database->selectCollection('users');
    $branchCollection = $database->selectCollection('branches');

    $orders = $collection->find();
    $dailyRevenue = array_fill(0, 30, 0);


foreach ($orders as $order) {
  
  $timestampInSeconds = (int)($order['orderedAt'] / 1000);
  $date = new DateTime("@$timestampInSeconds"); 
  $day = (int) $date->format('d'); 
    if ($day > 0 && $day <= 30) {
        $dailyRevenue[$day - 1] += $order['totalPrice'];
    }
}


    if (!isset($_SESSION['user_email'])) {
        echo "<script>
            alert('Vui lòng đăng nhập để tiếp tục');
            window.location.href = 'login.php';
        </script>";
        exit();
      }
        
        // $username = '';
        // $userEmail = $_SESSION['user_email'];
        // $user = $userCollection->findOne(['email' => $userEmail]);
        // $username = $user['name'];
    
    // Đếm tổng số user
    $totalUsers = $userCollection->countDocuments(['type' => 'user']); 
    
    // Đếm tổng số order
    $orderCount = $collection->countDocuments();
    
    
    // Truy vấn doanh thu theo tháng
    $pipeline = [
      [
          '$group' => [
              '_id' => [
                  'month' => [ '$month' => [ '$toDate' => ['$multiply' => ['$orderedAt', 1000]] ] ] // Chỉ nhóm theo tháng
              ],
              'totalEarnings' => [ '$sum' => '$totalPrice' ]
          ]
      ],
      [ '$sort' => [ '_id.month' => 1 ] ] // Sắp xếp theo tháng
    ];
    
    
    
    // Đếm tổng số sản phẩm gần hết hàng
    $totalAlmostOutOfStock = $collection1->countDocuments(['quantity' => ['$lte' => 100]]);
    
    
    // Đếm số sản phẩm hết hàng
    $totalOutOfStock = $collection1->countDocuments(['quantity' => ['$lte' => 0]]);
    
    $aggregation = $collection->aggregate([
        [
            '$group' => [
                '_id' => null, 
                'totalSales' => ['$sum' => '$totalPrice'] // Tính tổng totalPrice
            ]
        ]
    ]);
    
    
    $totalSales = 0;
    foreach ($aggregation as $doc) {
        if (isset($doc['totalSales'])) {
            $totalSales = $doc['totalSales'];
        }
    }
    
    
    $totalQuantity = $collection1->aggregate([
        [
            '$group' => [
                '_id' => null, 
                'totalQuantity' => ['$sum' => '$quantity'] // Tính tổng quantity
            ]
        ]
    ]);
    
    // Tính tổng số chi nhánh
    $totalBranch = $branchCollection->countDocuments();


    // Tính tổng số sản phẩm đã bán ra
    $aggregation = $collection->aggregate([
      [
          '$unwind' => '$products'  // Tách các sản phẩm trong đơn hàng thành từng bản ghi riêng biệt
      ],
      [
          '$group' => [
              '_id' => null,
              'totalQuantitySold' => ['$sum' => '$products.quantity']  // Tính tổng số lượng sản phẩm bán ra
          ]
      ]
  ]);
  
  $totalQuantitySold = $aggregation->toArray()[0]['totalQuantitySold'] ?? 0;




    // Tính tổng số lượng danh mục
    $totalQuantityValue = 0;
    foreach ($totalQuantity as $doc) {
        if (isset($doc['totalQuantity'])) {
            $totalQuantityValue = $doc['totalQuantity'];
        }
    }
    
            // Nhóm các sản phẩm theo danh mục và tính tổng số lượng danh mục
            $categories = $collection1->aggregate([
                [
                    '$group' => [
                        '_id' => '$category',
                    ]
                ]
            ]);
    
            // Biến đếm tổng số lượng danh mục
            $totalCategories = 0;
    
            foreach ($categories as $category) {
                // Tăng biến đếm danh mục cho mỗi danh mục tìm được
                $totalCategories++;
            }
        
?>

            
            <div
              class="d-sm-flex align-items-center justify-content-between mb-4"
            >
              <h1 class="h3 mb-0 text-gray-800">DashBoard</h1>
              <a
                href="#"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                ><i class="fas fa-download fa-sm text-white-50"></i> In báo cáo</a
              >
            </div>

            
            <div class="row">
              
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row  align-items-center">
                      <div class="col mr-2">
                        <a href="statisticRevenue.php"
                          class="text-xs font-weight-bold text-info text-uppercase mb-1"
                        >
                          Tổng doanh thu (VND)
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                    
                        <?= number_format($totalSales, 0) ?>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                      </div>
                    
                    </div>
                  </div>
                </div>
              </div>

              
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col mr-2">
                        <a href="listorders.php"
                          class="text-xs font-weight-bold text-success text-uppercase mb-1"
                        >
                          Tổng số lượng đơn hàng
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $orderCount ?>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Earnings (Monthly) Card Example -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row  align-items-center">
                      <div class="col mr-2">
                        <a href="#"
                          class="text-xs font-weight-bold text-info text-uppercase mb-1"
                        >
                          Tỉ lệ đánh giá sản phẩm
                        </a>
                        <div class="row align-items-center">
                          <div class="col-auto">
                            <div
                              class="h5 mb-0 mr-3 font-weight-bold text-gray-800"
                            >
                              90%
                            </div>
                          </div>
                          <div class="col">
                            <div class="progress progress-sm mr-2">
                              <div
                                class="progress-bar bg-info"
                                role="progressbar"
                                style="width: 90%"
                                aria-valuenow="50"
                                aria-valuemin="0"
                                aria-valuemax="100"
                              ></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i
                          class="fas fa-clipboard-list fa-2x text-gray-300"
                        ></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pending Requests Card Example -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col mr-2">
                        <a href="listusers.php"
                          class="text-xs font-weight-bold text-warning text-uppercase mb-1"
                        >
                          Tổng số lượng người dùng
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalUsers ?>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col mr-2">
                        <a href="listcats.php" 
                          class="text-xs font-weight-bold text-info text-uppercase mb-1"
                        >
                          Tổng danh mục 
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                    
                        <?= $totalCategories ?>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                      </div>
                    
                    </div>
                  </div>
                </div>
              </div>

              
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col mr-2">
                        <a href="productnearoutofstock.php"
                          class="text-xs font-weight-bold text-success text-uppercase mb-1"
                        >
                          Sản phẩm gần hết hàng
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalAlmostOutOfStock ?>
                        </div>
                      </div>
                      <div class="col-auto">
                      <i class="fa-solid fa-battery-quarter fa-2x text-gray-300"></i>
                        <!-- <i class="fas fa-calendar fa-2x text-gray-300"></i> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              

              <!-- Pending Requests Card Example -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col mr-2">
                        <a href="productoutofstock.php"
                          class="text-xs font-weight-bold text-info text-uppercase mb-1"
                        >
                          Sản phẩm hết hàng
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalOutOfStock ?>
                        </div>
                      </div>
                      <div class="col-auto">
                      <i class="fa-solid fa-battery-empty fa-2x text-gray-300"></i>
                        <!-- <i class="fas fa-comments fa-2x text-gray-300"></i> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>


              <!-- Pending Requests Card Example -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col mr-2">
                        <a href="#"
                          class="text-xs font-weight-bold text-warning text-uppercase mb-1"
                        >
                          Tổng số lượng tất cả sản phẩm
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalQuantityValue ?>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fab fa-product-hunt fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col mr-2">
                        <a href="quanlichinhanh.php"
                          class="text-xs font-weight-bold text-info text-uppercase mb-1"
                        >
                          Tổng số chi nhánh
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalBranch ?>
                        </div>
                      </div>
                      <div class="col-auto">
                      <i class="fa-solid fa-code-branch fa-2x text-gray-300"></i>
                        <!-- <i class="fas fa-calendar fa-2x text-gray-300"></i> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col mr-2">
                        <a href=""
                          class="text-xs font-weight-bold text-success text-uppercase mb-1"
                        >
                          Tổng số lượng sản phẩm đã bán ra
                        </a>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?= $totalQuantitySold ?>
                        </div>
                      </div>
                      <div class="col-auto">
                      <i class="fa-solid fa-cart-shopping fa-2x text-gray-300"></i>
                        <!-- <i class="fas fa-calendar fa-2x text-gray-300"></i> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              

            
 
            </div>

            
         

<?php
require('includes/footer.php');
?>
