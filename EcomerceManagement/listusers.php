<?php 
require('includes/header.php');
?>

<div>
    <div class="card shadow mb-4">
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        require 'vendor/autoload.php'; 
                        $uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
                        $client = new MongoDB\Client($uri);
                        $database = $client->selectDatabase('test'); 
                        $collection = $database->selectCollection('users');
                        
                        // Fetch all users from the collection
                        $users = $collection->find();
                        
                        // Loop through each user and display their information in the table
                        foreach ($users as $user) {
                            echo "<tr>";
                            echo "<td>{$user['name']}</td>";
                            echo "<td>{$user['email']}</td>";
                            echo "<td>{$user['address']}</td>";
                            echo "<td>{$user['type']}</td>";
                            echo "</tr>";
                        }
                        ?>                                
                    </tbody>
                </table>
            </div>
        <button class="btn btn-primary" onclick="window.location.href='index.php'">Quay lại</button>

        </div>
    </div>
</div>

<?php
require('includes/footer.php');
?>
