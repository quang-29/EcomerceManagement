<?php
    require 'includes/header.php';
    require 'vendor/autoload.php';

    $uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
    $client = new MongoDB\Client($uri);
    $database = $client->selectDatabase('test');
    $productCollection = $database->selectCollection('products');
    $branchCollection = $database->selectCollection('branches');

    // Get products with quantity 0
    
    $products = $productCollection->find([
        'quantity' => [
            '$gt' => 0, 
            '$lte' => 100  
        ]
    ]);
    
    $productsArray = iterator_to_array($products); 
    
    // Fetch all branches and map them by branchId
    $branchesCursor = $branchCollection->find();
    $branchesMap = [];
    
    function anhdaidien($arr, $height) {
        if (!empty($arr) && isset($arr[0])) {
            return "<img src='$arr[0]' height='$height' />";
        }
        return "<img src='default.jpg' height='$height' />";
    }


    foreach ($branchesCursor as $branch) {
        $branchesMap[(string)$branch['_id']] = $branch['name']; // Use string for ObjectId comparison
    }
?>

<div class="card-body">
    <h4 class="h4 text-gray-900 mb-4">Các sản phẩm gần hết hàng</h4>
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Images</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Branch</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productsArray as $product): 
                    $branchName = isset($branchesMap[(string)$product['branchId']]) ? $branchesMap[(string)$product['branchId']] : 'Unknown';
                ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= anhdaidien($product['images'], "100px")?></td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td><?= htmlspecialchars($product['quantity']) ?></td>
                    <td><?= htmlspecialchars($product['price']) ?></td>
                    <td><?= htmlspecialchars($branchName) ?></td>
                    <td>
                        <a class="btn btn-warning" href="editproduct.php?id=<?= $product['_id'] ?>">Edit</a>
                        <a class="btn btn-danger" href="deleteproduct.php?id=<?= $product['_id'] ?>" onclick="return confirm('Bạn chắc chắn xóa sản phẩm này?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-primary" onclick="window.location.href='index.php'">Quay lại</button>
    </div>
</div>

<?php
    require 'includes/footer.php';
?>
