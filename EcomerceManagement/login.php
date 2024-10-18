<?php
session_start();
require 'vendor/autoload.php'; 

$uri = 'mongodb+srv://trongngo:trong123@cluster0.jlqp3va.mongodb.net/';
$client = new MongoDB\Client($uri);
$database = $client->selectDatabase('test');
$collection = $database->selectCollection('users');

$err = [];

// Kiểm tra nếu form đăng nhập đã được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['signin-email'] ?? '';
    $password = $_POST['signin-password'] ?? '';

    // Kiểm tra input
    if (empty($email)) {
        $err['email'] = 'Bạn chưa nhập email!';
    }
    if (empty($password)) {
        $err['password'] = 'Bạn chưa nhập mật khẩu!';
    }

    // Kiểm tra nếu không có lỗi
    if (empty($err)) {
        $user = $collection->findOne(['email' => $email]);

        if ($user) {
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password'])) {
                // Đăng nhập thành công
                $_SESSION['user_id'] = (string) $user['_id']; 
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['type']; 

        
        	if ($user['type'] === 'admin') {
            	header("Location: index.php"); 
        	} else {
            	header("Location: trangchu.php"); 
        	}
        	exit;
            } else {
                $err['password'] = 'Mật khẩu không đúng';
            }
        } else {
            $err['email'] = 'Email không tồn tại';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Log In</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Log In">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">    
    <link rel="shortcut icon" href="favicon.ico"> 
    <script defer src="assets/plugins/fontawesome/js/all.min.js"></script>
    <link id="theme-style" rel="stylesheet" href="assets/css/portal.css">
	<style>
		.has-error {
			color: red;
		}
	</style>
</head> 

<body class="app app-login p-0">    	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				    <div class="app-auth-branding mb-4"><a class="app-logo" href="index.html"><img class="logo-icon me-2" src="assets/images/app-logo.svg" alt="logo"></a></div>
					<h2 class="auth-heading text-center mb-5">Log in</h2>
			        <div class="auth-form-container text-start">
						<!-- Form đăng nhập -->
						<form class="auth-form login-form" method="POST" action="">         
							<div class="email mb-3">
								<label class="sr-only" for="signin-email">Email</label>
								<input id="signin-email" name="signin-email" type="email" class="form-control signin-email" placeholder="Email address" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>">
								<div class="has-error">
									<span><?php echo $err['email'] ?? ''; ?></span>
								</div>
							</div><!--//form-group-->
							<div class="password mb-3">
								<label class="sr-only" for="signin-password">Password</label>
								<input id="signin-password" name="signin-password" type="password" class="form-control signin-password" placeholder="Password">
								<div class="has-error">
									<span><?php echo $err['password'] ?? ''; ?></span>
								</div>
								<div class="extra mt-3 row justify-content-between">
									<div class="col-6">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="RememberPassword">
											<label class="form-check-label" for="RememberPassword">
											Remember me
											</label>
										</div>
									</div><!--//col-6-->
									<div class="col-6">
										<div class="forgot-password text-end">
											<a href="resetPassword.php">Forgot password?</a>
										</div>
									</div><!--//col-6-->
								</div><!--//extra-->
							</div><!--//form-group-->
							<div class="text-center">
								<button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Log In</button>
							</div>
						</form>
						
						<div class="auth-option text-center pt-5">No Account? Sign up <a class="text-link" href="signup.php">here</a>.</div>
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
