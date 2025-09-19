<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home - Taste of Africa</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<style>
		.menu-tray {
			position: fixed;
			top: 16px;
			right: 16px;
			background: rgba(255,255,255,0.95);
			border: 1px solid #e6e6e6;
			border-radius: 8px;
			padding: 6px 10px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.06);
			z-index: 1000;
		}
		.menu-tray a { margin-left: 8px; }
		.btn-custom {
			background-color: #D19C97;
			border-color: #D19C97;
			color: #fff;
		}
		.btn-custom:hover {
			background-color: #b77a7a;
			border-color: #b77a7a;
			color: #fff;
		}
		.welcome-card {
			background: linear-gradient(135deg, #D19C97, #b77a7a);
			color: white;
			border-radius: 15px;
			padding: 2rem;
			margin: 2rem 0;
		}
		.user-info {
			background: rgba(255,255,255,0.1);
			border-radius: 10px;
			padding: 1rem;
			margin: 1rem 0;
		}
	</style>
</head>
<body>

	<div class="menu-tray">
		<?php if (isset($_SESSION['user_id'])): ?>
			<!-- User is logged in -->
			<span class="me-2">
				<i class="fa fa-user"></i> 
				Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
			</span>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">
				<i class="fa fa-sign-out-alt"></i> Logout
			</a>
		<?php else: ?>
			<!-- User is not logged in -->
			<span class="me-2">Menu:</span>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">
				<i class="fa fa-user-plus"></i> Register
			</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">
				<i class="fa fa-sign-in-alt"></i> Login
			</a>
		<?php endif; ?>
	</div>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">
			<h1><i class="fa fa-utensils"></i> Taste of Africa</h1>
			<p class="text-muted">Your gateway to authentic African cuisine</p>
			
			<?php if (isset($_SESSION['user_id'])): ?>
				<!-- User dashboard -->
				<div class="welcome-card">
					<h3><i class="fa fa-home"></i> Welcome Back!</h3>
					<div class="user-info">
						<p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
						<p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
						<p><strong>Role:</strong> 
							<?php 
							if ($_SESSION['user_role'] == 1) {
								echo '<span class="badge bg-warning"><i class="fa fa-crown"></i> Restaurant Owner</span>';
							} else {
								echo '<span class="badge bg-info"><i class="fa fa-user"></i> Customer</span>';
							}
							?>
						</p>
					</div>
					<p>You are successfully logged in! More features coming soon...</p>
				</div>
			<?php else: ?>
				<!-- Guest message -->
				<div class="welcome-card">
					<h3>Get Started Today!</h3>
					<p>Join our community and discover amazing African dishes</p>
					<div class="mt-3">
						<a href="login/register.php" class="btn btn-light me-2">
							<i class="fa fa-user-plus"></i> Create Account
						</a>
						<a href="login/login.php" class="btn btn-custom">
							<i class="fa fa-sign-in-alt"></i> Login
						</a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
