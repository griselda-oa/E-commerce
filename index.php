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
		/* Custom navbar styling */
		.navbar-brand {
			color: #D19C97 !important;
			font-size: 1.5rem;
		}
		.navbar-brand:hover {
			color: #b77a7a !important;
		}
		.nav-link {
			color: #333 !important;
			font-weight: 500;
			transition: color 0.3s;
		}
		.nav-link:hover {
			color: #D19C97 !important;
		}
		.navbar-toggler {
			border: none;
		}
		.navbar-toggler:focus {
			box-shadow: none;
		}
		
		/* Main content styling */
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
		
		/* Responsive adjustments */
		@media (max-width: 768px) {
			.navbar-brand {
				font-size: 1.2rem;
			}
			.container {
				padding-top: 100px !important;
			}
		}
		
		/* Hero section styling */
		.hero-section {
			background: linear-gradient(135deg, #f8f9fa, #e9ecef);
			border-radius: 20px;
			padding: 3rem 2rem;
			margin: 2rem 0;
		}
	</style>
</head>
<body>

	<!-- Enhanced Navigation Menu -->
	<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
		<div class="container">
			<a class="navbar-brand fw-bold" href="index.php">
				<i class="fa fa-utensils text-primary"></i> Taste of Africa
			</a>
			
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav me-auto">
					<li class="nav-item">
						<a class="nav-link" href="index.php">
							<i class="fa fa-home"></i> Home
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">
							<i class="fa fa-book"></i> Menu
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">
							<i class="fa fa-shopping-cart"></i> Order
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">
							<i class="fa fa-info-circle"></i> About
						</a>
					</li>
				</ul>
				
				<ul class="navbar-nav">
					<?php if (isset($_SESSION['user_id'])): ?>
						<!-- User is logged in -->
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
								<i class="fa fa-user-circle"></i> 
								<?php echo htmlspecialchars($_SESSION['user_name']); ?>
								<?php if ($_SESSION['user_role'] == 1): ?>
									<span class="badge bg-warning ms-1"><i class="fa fa-crown"></i> Owner</span>
								<?php else: ?>
									<span class="badge bg-info ms-1"><i class="fa fa-user"></i> Customer</span>
								<?php endif; ?>
							</a>
							<ul class="dropdown-menu dropdown-menu-end">
								<li><h6 class="dropdown-header">My Account</h6></li>
								<li><a class="dropdown-item" href="#"><i class="fa fa-user"></i> Profile</a></li>
								<li><a class="dropdown-item" href="#"><i class="fa fa-shopping-bag"></i> My Orders</a></li>
								<li><a class="dropdown-item" href="#"><i class="fa fa-heart"></i> Favorites</a></li>
								<?php if ($_SESSION['user_role'] == 1): ?>
									<li><hr class="dropdown-divider"></li>
									<li><h6 class="dropdown-header">Restaurant Owner</h6></li>
									<li><a class="dropdown-item" href="#"><i class="fa fa-plus"></i> Add Dish</a></li>
									<li><a class="dropdown-item" href="#"><i class="fa fa-chart-bar"></i> Analytics</a></li>
								<?php endif; ?>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item text-danger" href="login/logout.php">
									<i class="fa fa-sign-out-alt"></i> Logout
								</a></li>
							</ul>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">
								<i class="fa fa-shopping-cart"></i> Cart <span class="badge bg-primary">0</span>
							</a>
						</li>
					<?php else: ?>
						<!-- User is not logged in -->
						<li class="nav-item">
							<a class="nav-link" href="login/register.php">
								<i class="fa fa-user-plus"></i> Register
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="login/login.php">
								<i class="fa fa-sign-in-alt"></i> Login
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Main Content -->
	<div class="container" style="padding-top: 100px;">
		<?php if (isset($_SESSION['user_id'])): ?>
			<!-- User Dashboard -->
			<div class="row">
				<div class="col-12">
					<div class="hero-section text-center">
						<h1 class="display-4 mb-4">
							<i class="fa fa-utensils text-primary"></i> Welcome Back!
						</h1>
						<p class="lead text-muted">Ready to explore more delicious African cuisine?</p>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-6">
					<div class="welcome-card">
						<h3><i class="fa fa-user-circle"></i> Your Profile</h3>
						<div class="user-info">
							<p><strong><i class="fa fa-user"></i> Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
							<p><strong><i class="fa fa-envelope"></i> Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
							<p><strong><i class="fa fa-id-badge"></i> Role:</strong> 
								<?php 
								if ($_SESSION['user_role'] == 1) {
									echo '<span class="badge bg-warning"><i class="fa fa-crown"></i> Restaurant Owner</span>';
								} else {
									echo '<span class="badge bg-info"><i class="fa fa-user"></i> Customer</span>';
								}
								?>
							</p>
						</div>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="card">
						<div class="card-header bg-primary text-white">
							<h5 class="mb-0"><i class="fa fa-rocket"></i> Quick Actions</h5>
						</div>
						<div class="card-body">
							<?php if ($_SESSION['user_role'] == 1): ?>
								<!-- Restaurant Owner Actions -->
								<a href="#" class="btn btn-warning w-100 mb-2">
									<i class="fa fa-plus"></i> Add New Dish
								</a>
								<a href="#" class="btn btn-info w-100 mb-2">
									<i class="fa fa-chart-bar"></i> View Analytics
								</a>
								<a href="#" class="btn btn-success w-100">
									<i class="fa fa-list"></i> Manage Menu
								</a>
							<?php else: ?>
								<!-- Customer Actions -->
								<a href="#" class="btn btn-primary w-100 mb-2">
									<i class="fa fa-book"></i> Browse Menu
								</a>
								<a href="#" class="btn btn-info w-100 mb-2">
									<i class="fa fa-shopping-cart"></i> My Cart
								</a>
								<a href="#" class="btn btn-success w-100">
									<i class="fa fa-history"></i> Order History
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<!-- Guest Landing Page -->
			<div class="row">
				<div class="col-12">
					<div class="hero-section text-center">
						<h1 class="display-4 mb-4">
							<i class="fa fa-utensils text-primary"></i> Taste of Africa
						</h1>
						<p class="lead text-muted">Your gateway to authentic African cuisine</p>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-8 mx-auto">
					<div class="welcome-card text-center">
						<h3><i class="fa fa-star"></i> Get Started Today!</h3>
						<p class="mb-4">Join our community and discover amazing African dishes from restaurants near you</p>
						<div class="row">
							<div class="col-md-6 mb-3">
								<a href="login/register.php" class="btn btn-light btn-lg w-100">
									<i class="fa fa-user-plus"></i> Create Account
								</a>
							</div>
							<div class="col-md-6 mb-3">
								<a href="login/login.php" class="btn btn-custom btn-lg w-100">
									<i class="fa fa-sign-in-alt"></i> Login
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Features Section -->
			<div class="row mt-5">
				<div class="col-md-4 mb-4">
					<div class="card text-center h-100">
						<div class="card-body">
							<i class="fa fa-book fa-3x text-primary mb-3"></i>
							<h5>Browse Menu</h5>
							<p class="text-muted">Explore authentic African dishes from local restaurants</p>
						</div>
					</div>
				</div>
				<div class="col-md-4 mb-4">
					<div class="card text-center h-100">
						<div class="card-body">
							<i class="fa fa-shopping-cart fa-3x text-success mb-3"></i>
							<h5>Easy Ordering</h5>
							<p class="text-muted">Order your favorite dishes with just a few clicks</p>
						</div>
					</div>
				</div>
				<div class="col-md-4 mb-4">
					<div class="card text-center h-100">
						<div class="card-body">
							<i class="fa fa-heart fa-3x text-danger mb-3"></i>
							<h5>Save Favorites</h5>
							<p class="text-muted">Keep track of your favorite dishes and restaurants</p>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
