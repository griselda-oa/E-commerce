<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home - E-Commerce Platform</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
		* {
			box-sizing: border-box;
		}
		
		body {
			font-family: 'Inter', 'Poppins', sans-serif;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			position: relative;
			overflow-x: hidden;
		}
		
		/* Animated background particles */
		body::before {
			content: '';
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: 
				radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
				radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
				radial-gradient(circle at 40% 80%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
			animation: backgroundShift 20s ease-in-out infinite;
			z-index: -1;
		}
		
		@keyframes backgroundShift {
			0%, 100% { transform: translateX(0) translateY(0); }
			25% { transform: translateX(-20px) translateY(-10px); }
			50% { transform: translateX(20px) translateY(10px); }
			75% { transform: translateX(-10px) translateY(20px); }
		}
		
		/* Floating elements */
		body::after {
			content: '';
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-image: 
				radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.3), transparent),
				radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.2), transparent),
				radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.4), transparent),
				radial-gradient(1px 1px at 130px 80px, rgba(255,255,255,0.3), transparent);
			background-repeat: repeat;
			background-size: 200px 200px;
			animation: float 20s linear infinite;
			z-index: -1;
		}
		
		@keyframes float {
			0% { transform: translateY(0px); }
			100% { transform: translateY(-200px); }
		}
		
		.navbar {
			background: rgba(255, 255, 255, 0.1) !important;
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
			transition: all 0.3s ease;
		}
		
		.navbar:hover {
			background: rgba(255, 255, 255, 0.15) !important;
			box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
		}
		
		.navbar-brand {
			color: #ffffff !important;
			font-size: 1.8rem;
			font-weight: 800;
			text-shadow: 0 2px 4px rgba(0,0,0,0.3);
			transition: all 0.3s ease;
			position: relative;
		}
		
		.navbar-brand:hover {
			color: #ffd700 !important;
			transform: scale(1.05);
			text-shadow: 0 4px 8px rgba(255, 215, 0, 0.4);
		}
		
		.navbar-brand i {
			animation: pulse 2s infinite;
		}
		
		@keyframes pulse {
			0% { transform: scale(1); }
			50% { transform: scale(1.1); }
			100% { transform: scale(1); }
		}
		
		.nav-link {
			color: rgba(255, 255, 255, 0.9) !important;
			font-weight: 600;
			font-size: 0.95rem;
			transition: all 0.3s ease;
			position: relative;
			padding: 0.75rem 1rem !important;
			border-radius: 8px;
			margin: 0 0.25rem;
		}
		
		.nav-link:hover {
			color: #ffffff !important;
			background: rgba(255, 255, 255, 0.1);
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
		}
		
		.nav-link::after {
			content: '';
			position: absolute;
			width: 0;
			height: 2px;
			bottom: 5px;
			left: 50%;
			background: linear-gradient(90deg, #ffd700, #ff6b6b);
			transition: all 0.3s ease;
			transform: translateX(-50%);
		}
		
		.nav-link:hover::after {
			width: 80%;
		}
		
		/* 🎨 PREMIUM DROPDOWN STYLING 🎨 */
		.dropdown-menu {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 16px;
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
			padding: 0.5rem 0;
			margin-top: 0.5rem;
			animation: slideDown 0.3s ease;
		}
		
		@keyframes slideDown {
			from { opacity: 0; transform: translateY(-10px); }
			to { opacity: 1; transform: translateY(0); }
		}
		
		.dropdown-item {
			padding: 0.75rem 1.5rem;
			transition: all 0.3s ease;
			border-radius: 8px;
			margin: 0.25rem 0.5rem;
			font-weight: 500;
		}
		
		.dropdown-item:hover {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			transform: translateX(5px);
		}
		
		.dropdown-header {
			font-size: 0.75rem;
			font-weight: 700;
			color: #6c757d;
			text-transform: uppercase;
			letter-spacing: 1px;
			padding: 0.5rem 1.5rem;
		}
		
		/* 🔥 ENHANCED BUTTONS 🔥 */
		.btn-custom {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: none;
			color: white;
			font-weight: 700;
			padding: 15px 30px;
			border-radius: 50px;
			box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
			transition: all 0.3s ease;
			position: relative;
			overflow: hidden;
			text-transform: uppercase;
			letter-spacing: 1px;
			font-size: 0.9rem;
		}
		
		.btn-custom::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
			transition: left 0.5s;
		}
		
		.btn-custom:hover::before {
			left: 100%;
		}
		
		.btn-custom:hover {
			transform: translateY(-3px);
			box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
			background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
		}
		
		.btn-custom:active {
			transform: translateY(-1px);
		}
		
		/* 🎨 PREMIUM CARD STYLING 🎨 */
		.welcome-card {
			background: rgba(255, 255, 255, 0.1);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			color: white;
			border-radius: 24px;
			padding: 3rem;
			margin: 2rem 0;
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
			transition: all 0.3s ease;
			position: relative;
			overflow: hidden;
		}
		
		.welcome-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #ffd700, #ff6b6b, #667eea, #764ba2);
			background-size: 400% 100%;
			animation: gradientMove 3s ease infinite;
		}
		
		@keyframes gradientMove {
			0%, 100% { background-position: 0% 50%; }
			50% { background-position: 100% 50%; }
		}
		
		.welcome-card:hover {
			transform: translateY(-10px);
			box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
			background: rgba(255, 255, 255, 0.15);
		}
		
		.user-info {
			background: rgba(255, 255, 255, 0.1);
			border-radius: 16px;
			padding: 1.5rem;
			margin: 1.5rem 0;
			border: 1px solid rgba(255, 255, 255, 0.2);
			backdrop-filter: blur(10px);
			transition: all 0.3s ease;
		}
		
		.user-info:hover {
			background: rgba(255, 255, 255, 0.15);
			transform: scale(1.02);
		}
		
		/* 🚀 HERO SECTION - NEXT LEVEL 🚀 */
		.hero-section {
			background: rgba(255, 255, 255, 0.1);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 32px;
			padding: 4rem 3rem;
			margin: 3rem 0;
			box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
			text-align: center;
			position: relative;
			overflow: hidden;
			transition: all 0.3s ease;
		}
		
		.hero-section::before {
			content: '';
			position: absolute;
			top: -50%;
			left: -50%;
			width: 200%;
			height: 200%;
			background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
			animation: rotate 20s linear infinite;
		}
		
		@keyframes rotate {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
		
		.hero-section:hover {
			transform: translateY(-5px);
			box-shadow: 0 35px 70px rgba(0, 0, 0, 0.15);
		}
		
		.hero-section h1 {
			color: white;
			font-weight: 800;
			font-size: 3.5rem;
			text-shadow: 0 4px 8px rgba(0,0,0,0.3);
			margin-bottom: 1.5rem;
			position: relative;
			z-index: 1;
			animation: glow 2s ease-in-out infinite alternate;
		}
		
		@keyframes glow {
			from { text-shadow: 0 4px 8px rgba(0,0,0,0.3), 0 0 20px rgba(255,255,255,0.2); }
			to { text-shadow: 0 4px 8px rgba(0,0,0,0.3), 0 0 30px rgba(255,255,255,0.4); }
		}
		
		.hero-section .lead {
			color: rgba(255, 255, 255, 0.9);
			font-size: 1.3rem;
			font-weight: 500;
			position: relative;
			z-index: 1;
		}
		
		/* 🎨 FEATURE CARDS - PREMIUM 🎨 */
		.card {
			background: rgba(255, 255, 255, 0.1);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 20px;
			box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
			transition: all 0.3s ease;
			overflow: hidden;
			position: relative;
		}
		
		.card::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
			transition: left 0.6s;
		}
		
		.card:hover::before {
			left: 100%;
		}
		
		.card:hover {
			transform: translateY(-10px) scale(1.02);
			box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
			background: rgba(255, 255, 255, 0.15);
		}
		
		.card-body {
			padding: 2rem;
			position: relative;
			z-index: 1;
		}
		
		.card-body i {
			transition: all 0.3s ease;
		}
		
		.card:hover .card-body i {
			transform: scale(1.2) rotate(5deg);
			color: #ffd700;
		}
		
		/* 🎨 BADGE STYLING 🎨 */
		.badge {
			border-radius: 50px;
			padding: 8px 16px;
			font-weight: 700;
			font-size: 0.8rem;
			text-transform: uppercase;
			letter-spacing: 1px;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
		}
		
		/* 🚀 SPECIAL CATEGORY BUTTON STYLING 🚀 */
		.category-btn {
			position: relative;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: 2px solid rgba(255, 255, 255, 0.3);
			font-weight: 800;
			font-size: 1rem;
			padding: 18px 25px;
			border-radius: 15px;
			box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
			transition: all 0.4s ease;
			overflow: hidden;
		}
		
		.category-btn::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
			transition: left 0.6s;
		}
		
		.category-btn:hover::before {
			left: 100%;
		}
		
		.category-btn:hover {
			background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
			transform: translateY(-5px) scale(1.05);
			box-shadow: 0 20px 40px rgba(102, 126, 234, 0.7);
			border-color: rgba(255, 255, 255, 0.6);
		}
		
		.category-btn i {
			font-size: 1.2rem;
			margin-right: 10px;
			animation: bounce 2s infinite;
		}
		
		@keyframes bounce {
			0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
			40% { transform: translateY(-5px); }
			60% { transform: translateY(-3px); }
		}
		
		.btn-badge {
			position: absolute;
			top: -8px;
			right: -8px;
			background: linear-gradient(135deg, #ff6b6b, #ee5a24);
			color: white;
			font-size: 0.7rem;
			font-weight: 900;
			padding: 4px 8px;
			border-radius: 12px;
			box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
			animation: pulse 2s infinite;
		}
		
		/* 🎨 ENHANCED QUICK ACTIONS CARD 🎨 */
		.card-header.bg-primary {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
			border: none;
			padding: 20px;
		}
		
		.card-header h5 {
			font-weight: 800;
			font-size: 1.3rem;
			text-shadow: 0 2px 4px rgba(0,0,0,0.2);
		}
		
		.card-header i {
			animation: rotate 3s linear infinite;
		}
		
		@keyframes rotate {
			from { transform: rotate(0deg); }
			to { transform: rotate(360deg); }
		}
		
		/* 🎨 IMPROVED BUTTON LAYOUT 🎨 */
		.card-body {
			padding: 25px;
		}
		
		.card-body .btn {
			position: relative;
			font-weight: 600;
			padding: 12px 20px;
			border-radius: 10px;
			transition: all 0.3s ease;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			font-size: 0.9rem;
		}
		
		.card-body .btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
		}
		
		.card-body .btn i {
			margin-right: 8px;
		}
		
		/* 🎨 LOGOUT BUTTON 🎨 */
		.btn-outline-danger {
			border: 2px solid rgba(220, 53, 69, 0.5);
			color: rgba(220, 53, 69, 0.9);
			background: rgba(220, 53, 69, 0.1);
			backdrop-filter: blur(10px);
			font-weight: 600;
			padding: 8px 20px;
			border-radius: 50px;
			transition: all 0.3s ease;
		}
		
		.btn-outline-danger:hover {
			background: linear-gradient(135deg, #dc3545, #c82333);
			border-color: #dc3545;
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
		}
		
		/* 🎨 RESPONSIVE DESIGN 🎨 */
		@media (max-width: 768px) {
			.navbar-brand {
				font-size: 1.4rem;
			}
			
			.container {
				padding-top: 120px !important;
			}
			
			.hero-section h1 {
				font-size: 2.5rem;
			}
			
			.hero-section {
				padding: 2rem 1.5rem;
			}
			
			.welcome-card {
				padding: 2rem 1.5rem;
			}
		}
		
		/* 🎨 SCROLL ANIMATIONS 🎨 */
		.fade-in {
			opacity: 0;
			transform: translateY(30px);
			animation: fadeInUp 0.8s ease forwards;
		}
		
		@keyframes fadeInUp {
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		/* 🎨 LOADING EFFECTS 🎨 */
		.loading-shimmer {
			background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
			background-size: 200% 100%;
			animation: shimmer 2s infinite;
		}
		
		@keyframes shimmer {
			0% { background-position: -200% 0; }
			100% { background-position: 200% 0; }
		}
	</style>
</head>
<body>

	<!-- Enhanced Navigation Menu -->
	<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
		<div class="container">
			<a class="navbar-brand fw-bold" href="index.php">
				<i class="fa fa-shopping-bag text-primary"></i> E-Commerce Store
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
							<i class="fa fa-store"></i> Products
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">
							<i class="fa fa-shopping-cart"></i> Cart
						</a>
					</li>
					<?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 1): ?>
					<li class="nav-item">
						<a class="nav-link" href="admin/category.php">
							<i class="fa fa-tags"></i> Categories
						</a>
					</li>
					<?php endif; ?>
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
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-user-circle"></i> 
								<?php echo htmlspecialchars($_SESSION['user_name']); ?>
								<?php if ($_SESSION['user_role'] == 1): ?>
									<span class="badge bg-warning ms-1"><i class="fa fa-crown"></i> Owner</span>
								<?php else: ?>
									<span class="badge bg-info ms-1"><i class="fa fa-user"></i> Customer</span>
								<?php endif; ?>
							</a>
							<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
								<li><h6 class="dropdown-header">My Account</h6></li>
								<li><a class="dropdown-item" href="#"><i class="fa fa-user"></i> Profile</a></li>
								<li><a class="dropdown-item" href="#"><i class="fa fa-shopping-bag"></i> My Orders</a></li>
								<li><a class="dropdown-item" href="#"><i class="fa fa-heart"></i> Favorites</a></li>
								<?php if ($_SESSION['user_role'] == 1): ?>
									<li><hr class="dropdown-divider"></li>
									<li><h6 class="dropdown-header">Store Admin</h6></li>
									<li><a class="dropdown-item" href="admin/category.php"><i class="fa fa-tags"></i> Manage Categories</a></li>
									<li><a class="dropdown-item" href="#"><i class="fa fa-plus"></i> Add Product</a></li>
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
						<!-- User-friendly logout button -->
						<li class="nav-item">
							<a class="btn btn-outline-danger btn-sm ms-2" href="login/logout.php" style="margin-top: 2px;">
								<i class="fa fa-sign-out-alt"></i> Logout
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
							<i class="fa fa-shopping-bag text-primary"></i> Welcome Back!
						</h1>
						<p class="lead text-muted">Ready to explore our amazing products and deals?</p>
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
									echo '<span class="badge bg-warning"><i class="fa fa-crown"></i> Store Admin</span>';
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
							<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
								<!-- Store Admin Actions -->
								<div class="alert alert-info mb-3" style="background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.3); color: #667eea;">
									<strong><i class="fa fa-info-circle"></i> CRUD Operations Available</strong><br>
									<small>Click below to manage your product categories with full Create, Read, Update, Delete functionality</small>
								</div>
								<a href="admin/category.php" class="btn btn-custom w-100 mb-3 category-btn">
									<i class="fa fa-tags"></i> Manage Categories
									<span class="btn-badge">CRUD</span>
								</a>
								<a href="#" class="btn btn-warning w-100 mb-2">
									<i class="fa fa-plus"></i> Add New Product
								</a>
								<a href="#" class="btn btn-info w-100 mb-2">
									<i class="fa fa-chart-bar"></i> View Analytics
								</a>
								<a href="#" class="btn btn-success w-100">
									<i class="fa fa-list"></i> Manage Products
								</a>
							<?php else: ?>
								<!-- Customer Actions -->
								<div class="alert alert-warning mb-3" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); color: #856404;">
									<strong><i class="fa fa-info-circle"></i> Want to test CRUD operations?</strong><br>
									<small>Click "Make Me Admin" below to access category management features</small>
								</div>
								<a href="make_admin.php" class="btn btn-custom w-100 mb-3 category-btn">
									<i class="fa fa-user-shield"></i> Make Me Admin
									<span class="btn-badge">TEST</span>
								</a>
								<a href="#" class="btn btn-primary w-100 mb-2">
									<i class="fa fa-store"></i> Browse Products
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
							<i class="fa fa-shopping-bag text-primary"></i> E-Commerce Store
						</h1>
						<p class="lead text-muted">Your one-stop shop for amazing products and great deals</p>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-8 mx-auto">
					<div class="welcome-card text-center">
						<h3><i class="fa fa-star"></i> Get Started Today!</h3>
						<p class="mb-4">Join our community and discover amazing products at great prices</p>
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
							<i class="fa fa-store fa-3x text-primary mb-3"></i>
							<h5>Browse Products</h5>
							<p class="text-muted">Explore our wide range of quality products and brands</p>
						</div>
					</div>
				</div>
				<div class="col-md-4 mb-4">
					<div class="card text-center h-100">
						<div class="card-body">
							<i class="fa fa-shopping-cart fa-3x text-success mb-3"></i>
							<h5>Easy Shopping</h5>
							<p class="text-muted">Add products to cart and checkout with just a few clicks</p>
						</div>
					</div>
				</div>
				<div class="col-md-4 mb-4">
					<div class="card text-center h-100">
						<div class="card-body">
							<i class="fa fa-heart fa-3x text-danger mb-3"></i>
							<h5>Save Favorites</h5>
							<p class="text-muted">Keep track of your favorite products and wishlist</p>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		// 🚀 ULTIMATE JAVASCRIPT - PREMIUM INTERACTIONS 🚀
		
		document.addEventListener('DOMContentLoaded', function() {
			// Initialize Bootstrap dropdowns
			var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
			var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
				return new bootstrap.Dropdown(dropdownToggleEl);
			});
			
			// Enhanced dropdown interactions
			document.getElementById('userDropdown')?.addEventListener('click', function(e) {
				e.preventDefault();
				var dropdown = bootstrap.Dropdown.getInstance(this);
				if (dropdown) {
					dropdown.toggle();
				}
			});
			
			// 🎨 SCROLL ANIMATIONS 🎨
			const observerOptions = {
				threshold: 0.1,
				rootMargin: '0px 0px -50px 0px'
			};
			
			const observer = new IntersectionObserver(function(entries) {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.classList.add('fade-in');
					}
				});
			}, observerOptions);
			
			// Observe all cards and sections
			document.querySelectorAll('.card, .hero-section, .welcome-card').forEach(el => {
				observer.observe(el);
			});
			
			// 🎨 ENHANCED NAVBAR SCROLL EFFECT 🎨
			let lastScrollTop = 0;
			window.addEventListener('scroll', function() {
				const navbar = document.querySelector('.navbar');
				const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
				
				if (scrollTop > lastScrollTop && scrollTop > 100) {
					// Scrolling down
					navbar.style.transform = 'translateY(-100%)';
					navbar.style.transition = 'transform 0.3s ease';
				} else {
					// Scrolling up
					navbar.style.transform = 'translateY(0)';
				}
				
				// Add blur effect based on scroll
				if (scrollTop > 50) {
					navbar.style.background = 'rgba(255, 255, 255, 0.2)';
					navbar.style.backdropFilter = 'blur(25px)';
				} else {
					navbar.style.background = 'rgba(255, 255, 255, 0.1)';
					navbar.style.backdropFilter = 'blur(20px)';
				}
				
				lastScrollTop = scrollTop;
			});
			
			// 🎨 PARALLAX BACKGROUND EFFECT 🎨
			window.addEventListener('scroll', function() {
				const scrolled = window.pageYOffset;
				const parallax = document.querySelector('body::before');
				if (parallax) {
					parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
				}
			});
			
			// 🎨 ENHANCED BUTTON INTERACTIONS 🎨
			document.querySelectorAll('.btn-custom').forEach(button => {
				button.addEventListener('mouseenter', function() {
					this.style.transform = 'translateY(-3px) scale(1.05)';
					this.style.boxShadow = '0 20px 40px rgba(102, 126, 234, 0.6)';
				});
				
				button.addEventListener('mouseleave', function() {
					this.style.transform = 'translateY(0) scale(1)';
					this.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.4)';
				});
				
				// Ripple effect on click
				button.addEventListener('click', function(e) {
					const ripple = document.createElement('span');
					const rect = this.getBoundingClientRect();
					const size = Math.max(rect.width, rect.height);
					const x = e.clientX - rect.left - size / 2;
					const y = e.clientY - rect.top - size / 2;
					
					ripple.style.width = ripple.style.height = size + 'px';
					ripple.style.left = x + 'px';
					ripple.style.top = y + 'px';
					ripple.style.position = 'absolute';
					ripple.style.borderRadius = '50%';
					ripple.style.background = 'rgba(255, 255, 255, 0.6)';
					ripple.style.transform = 'scale(0)';
					ripple.style.animation = 'ripple 0.6s linear';
					ripple.style.pointerEvents = 'none';
					
					this.appendChild(ripple);
					
					setTimeout(() => {
						ripple.remove();
					}, 600);
				});
			});
			
			// 🎨 CARD HOVER ENHANCEMENTS 🎨
			document.querySelectorAll('.card').forEach(card => {
				card.addEventListener('mouseenter', function() {
					this.style.transform = 'translateY(-15px) scale(1.03)';
					this.style.boxShadow = '0 30px 60px rgba(0, 0, 0, 0.25)';
					
					// Animate icons
					const icon = this.querySelector('i');
					if (icon) {
						icon.style.transform = 'scale(1.3) rotate(10deg)';
						icon.style.color = '#ffd700';
					}
				});
				
				card.addEventListener('mouseleave', function() {
					this.style.transform = 'translateY(0) scale(1)';
					this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.1)';
					
					// Reset icons
					const icon = this.querySelector('i');
					if (icon) {
						icon.style.transform = 'scale(1) rotate(0deg)';
						icon.style.color = '';
					}
				});
			});
			
			// 🎨 TYPING ANIMATION FOR HERO TEXT 🎨
			const heroText = document.querySelector('.hero-section h1');
			if (heroText && !document.querySelector('.hero-section .lead').textContent.includes('Welcome Back')) {
				const text = heroText.textContent;
				heroText.textContent = '';
				heroText.style.borderRight = '2px solid white';
				heroText.style.animation = 'blink 1s infinite';
				
				let i = 0;
				const typeWriter = () => {
					if (i < text.length) {
						heroText.textContent += text.charAt(i);
						i++;
						setTimeout(typeWriter, 100);
					} else {
						heroText.style.borderRight = 'none';
						heroText.style.animation = 'none';
					}
				};
				
				setTimeout(typeWriter, 1000);
			}
			
			// 🎨 LOADING ANIMATION 🎨
			document.querySelectorAll('.card, .welcome-card').forEach(el => {
				el.classList.add('loading-shimmer');
				setTimeout(() => {
					el.classList.remove('loading-shimmer');
				}, 2000);
			});
			
			// 🎨 RANDOM PARTICLE GENERATION 🎨
			function createParticle() {
				const particle = document.createElement('div');
				particle.style.position = 'fixed';
				particle.style.width = '4px';
				particle.style.height = '4px';
				particle.style.background = 'rgba(255, 255, 255, 0.6)';
				particle.style.borderRadius = '50%';
				particle.style.pointerEvents = 'none';
				particle.style.zIndex = '1';
				particle.style.left = Math.random() * window.innerWidth + 'px';
				particle.style.top = window.innerHeight + 'px';
				particle.style.animation = 'floatUp 8s linear forwards';
				
				document.body.appendChild(particle);
				
				setTimeout(() => {
					particle.remove();
				}, 8000);
			}
			
			// Generate particles every 2 seconds
			setInterval(createParticle, 2000);
		});
		
		// Additional CSS animations for JavaScript effects
		const style = document.createElement('style');
		style.textContent = `
			@keyframes ripple {
				to {
					transform: scale(4);
					opacity: 0;
				}
			}
			
			@keyframes blink {
				0%, 50% { border-color: transparent; }
				51%, 100% { border-color: white; }
			}
			
			@keyframes floatUp {
				to {
					transform: translateY(-100vh);
					opacity: 0;
				}
			}
			
			.navbar {
				transition: transform 0.3s ease, background 0.3s ease, backdrop-filter 0.3s ease !important;
			}
		`;
		document.head.appendChild(style);
	</script>
</body>
</html>
