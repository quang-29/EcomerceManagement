<?php
require 'vendor/autoload.php'; // Nạp thư viện MongoDB

// Kết nối đến MongoDB
$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test'); // Thay 'test' bằng tên cơ sở dữ liệu của bạn
$collection = $database->selectCollection('users'); // Thay 'users' bằng tên collection của bạn

// Xử lý yêu cầu đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $name = $_POST['signup-name'] ?? '';
    $email = $_POST['signup-email'] ?? '';
    $password = $_POST['signup-password'] ?? '';

    // Kiểm tra xem các trường có bị trống không
    if (empty($name) || empty($email) || empty($password)) {
        echo "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Kiểm tra xem email đã tồn tại trong database chưa
        $existingUser = $collection->findOne(['email' => $email]);
        
        if ($existingUser) {
            echo "Email đã tồn tại. Vui lòng sử dụng một email khác.";
        } else {
            // Mã hóa mật khẩu bằng bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Thêm người dùng mới vào MongoDB
			// Thêm người dùng mới vào MongoDB
		$collection->insertOne([
    		'name' => $name,
			'email' => $email,
    		'password' => $hashedPassword,  
    		'address' =>  '',  
    		'type' => isset($type) ? $type : 'user',  
    		'cart' => []  
		]);


		echo "<script>alert('Đăng ký tài khoản thành công!');
				window.location.href = 'login.php';
	  		</script>";
		exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Sign up</title>
    
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="description" content="Portal - Bootstrap 5 Admin Dashboard Template For Developers">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">    
    <link rel="shortcut icon" href="favicon.ico"> 
    
    <!-- FontAwesome JS-->
    <script defer src="assets/plugins/fontawesome/js/all.min.js"></script>
    
    <!-- App CSS -->  
    <link id="theme-style" rel="stylesheet" href="assets/css/portal.css">
</head> 

<body class="app app-signup p-0">    	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				    <div class="app-auth-branding mb-4"><a class="app-logo" href="index.html"><img class="logo-icon me-2" src="assets/images/app-logo.svg" alt="logo"></a></div>
					<h2 class="auth-heading text-center mb-4">Sign up</h2>					
	
					<div class="auth-form-container text-start mx-auto">
						<!-- Form Đăng Ký -->
						<form class="auth-form auth-signup-form" method="POST" action="">         
							<div class="mb-3">
								<label class="sr-only" for="signup-name">Your Name</label>
								<input id="signup-name" name="signup-name" type="text" class="form-control signup-name" placeholder="Full name" required="required">
							</div>
							<div class="mb-3">
								<label class="sr-only" for="signup-email">Your Email</label>
								<input id="signup-email" name="signup-email" type="email" class="form-control signup-email" placeholder="Email" required="required">
							</div>
							<div class="mb-3">
								<label class="sr-only" for="signup-password">Password</label>
								<input id="signup-password" name="signup-password" type="password" class="form-control signup-password" placeholder="Create a password" required="required">
							</div>
							<div class="mb-3">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="RememberPassword" required>
									<label class="form-check-label" for="RememberPassword">
									I agree to the <a href="#" class="app-link">Terms of Service</a> and <a href="#" class="app-link">Privacy Policy</a>.
									</label>
								</div>
							</div><!--//extra-->
							
							<div class="text-center">
								<button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Sign Up</button>
							</div>
						</form><!--//auth-form-->
						
						<div class="auth-option text-center pt-5">Already have an account? <a class="text-link" href="login.php">Log in</a></div>
					</div><!--//auth-form-container-->	
			    </div><!--//auth-body-->
		    </div><!--//flex-column-->   
	    </div><!--//auth-main-col-->
	    
	    <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
		    <div class="auth-background-holder">			    
		    </div>
		    <div class="auth-background-mask"></div>
		    <div class="auth-background-overlay p-3 p-lg-5">
			    <div class="d-flex flex-column align-content-end h-100">
				    <div class="h-100"></div>
				</div>
		    </div><!--//auth-background-overlay-->
	    </div><!--//auth-background-col-->
    </div><!--//row-->
</body>
</html>
