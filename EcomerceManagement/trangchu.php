

<?php

    session_start();
    $useremail = $_SESSION['user_email'];
    require 'vendor/autoload.php'; 
    $uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
    $client = new MongoDB\Client($uri);
    $database = $client->selectDatabase('test'); 
    $collection = $database->selectCollection('users'); 
    $user = $collection->findOne(['email' => $useremail]);
    $username = $user['name'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Xin chào <?php echo $username?></h1>
</body>
</html>