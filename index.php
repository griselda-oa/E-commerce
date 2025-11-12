<?php
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<meta name="version" content="<?php echo time(); ?>">
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
			background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
			min-height: 100vh;
			position: relative;
			overflow-x: hidden;
		}
		
		/* Enhanced animated background */
		body::before {
			content: '';
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: 
				radial-gradient(circle at 20% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%),
				radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.15) 0%, transparent 50%),
				radial-gradient(circle at 40% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
				radial-gradient(circle at 60% 40%, rgba(168, 85, 247, 0.1) 0%, transparent 50%);
			animation: backgroundShift 25s ease-in-out infinite;
			z-index: -1;
		}
		
		/* Floating particles effect */
		body::after {
			content: '';
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-image: 
				radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.1), transparent),
				radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.1), transparent),
				radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.1), transparent),
				radial-gradient(1px 1px at 130px 80px, rgba(255,255,255,0.1), transparent),
				radial-gradient(2px 2px at 160px 30px, rgba(255,255,255,0.1), transparent);
			background-repeat: repeat;
			background-size: 200px 100px;
			animation: float 20s linear infinite;
			z-index: -1;
		}
		
		@keyframes backgroundShift {
			0%, 100% { transform: translateX(0) translateY(0); }
			25% { transform: translateX(-20px) translateY(-10px); }
			50% { transform: translateX(20px) translateY(10px); }
			75% { transform: translateX(-10px) translateY(20px); }
		}
		
		@keyframes float {
			0% { transform: translateY(0px); }
			50% { transform: translateY(-20px); }
			100% { transform: translateY(0px); }
		}
		
		
		.navbar {
			background: rgba(255, 255, 255, 0.95) !important;
			backdrop-filter: blur(25px);
			border: 1px solid rgba(79, 70, 229, 0.15);
			box-shadow: 0 10px 40px rgba(79, 70, 229, 0.08);
			transition: all 0.3s ease;
			border-radius: 0 0 20px 20px;
		}
		
		.navbar-brand {
			font-weight: 700;
			font-size: 1.5rem;
			background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
			transition: all 0.3s ease;
		}
		
		.navbar-brand:hover {
			transform: scale(1.05);
		}
		
		.navbar:hover {
			background: rgba(255, 255, 255, 0.95) !important;
			box-shadow: 0 12px 40px rgba(79, 70, 229, 0.2);
		}
		
		.navbar-brand {
			color: #1e293b !important;
			font-size: 1.8rem;
			font-weight: 800;
			text-shadow: 0 1px 2px rgba(255,255,255,0.8);
			transition: all 0.3s ease;
			position: relative;
		}
		
		.navbar-brand:hover {
			color: #4f46e5 !important;
			transform: scale(1.05);
			text-shadow: 0 2px 4px rgba(79, 70, 229, 0.4);
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
			color: #1e293b !important;
			font-weight: 600;
			font-size: 0.95rem;
			transition: all 0.3s ease;
			position: relative;
			padding: 0.75rem 1rem !important;
			border-radius: 8px;
			margin: 0 0.25rem;
			text-shadow: 0 1px 2px rgba(255,255,255,0.6);
		}
		
		.nav-link:hover {
			color: #4f46e5 !important;
			background: rgba(79, 70, 229, 0.1);
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
		}
		
		.nav-link::after {
			content: '';
			position: absolute;
			width: 0;
			height: 2px;
			bottom: 5px;
			left: 50%;
			background: linear-gradient(90deg, #4f46e5, #06b6d4);
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
			background: linear-gradient(135deg, #4f46e5, #06b6d4);
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
			background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
			border: none;
			color: white;
			font-weight: 700;
			padding: 15px 30px;
			border-radius: 50px;
			box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
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
			box-shadow: 0 15px 35px rgba(79, 70, 229, 0.6);
			background: linear-gradient(135deg, #06b6d4 0%, #4f46e5 100%);
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
			background: linear-gradient(90deg, #ffd700, #ff6b6b, #4f46e5, #06b6d4);
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
			color: #1e293b;
			font-weight: 800;
			font-size: 3.5rem;
			text-shadow: 0 2px 4px rgba(255,255,255,0.8), 0 0 20px rgba(255,255,255,0.3);
			margin-bottom: 1.5rem;
			position: relative;
			z-index: 1;
			animation: glow 2s ease-in-out infinite alternate;
		}
		
		@keyframes glow {
			from { text-shadow: 0 2px 4px rgba(255,255,255,0.8), 0 0 20px rgba(79, 70, 229, 0.3); }
			to { text-shadow: 0 2px 4px rgba(255,255,255,0.8), 0 0 30px rgba(6, 182, 212, 0.5); }
		}
		
		@keyframes blink {
			0%, 50% { border-color: #4f46e5; }
			51%, 100% { border-color: transparent; }
		}
		
		.hero-section .lead {
			color: #334155;
			font-size: 1.3rem;
			font-weight: 600;
			position: relative;
			z-index: 1;
			text-shadow: 0 1px 2px rgba(255,255,255,0.6);
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
			background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
			border: 2px solid rgba(255, 255, 255, 0.3);
			font-weight: 800;
			font-size: 1rem;
			padding: 18px 25px;
			border-radius: 15px;
			box-shadow: 0 10px 30px rgba(79, 70, 229, 0.5);
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
			background: linear-gradient(135deg, #06b6d4 0%, #4f46e5 100%);
			transform: translateY(-5px) scale(1.05);
			box-shadow: 0 20px 40px rgba(79, 70, 229, 0.7);
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
			background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%) !important;
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
		
		/* SCROLL ANIMATIONS */
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
		
		/* AI-ENHANCED FEATURES */
		.ai-chat-widget {
			position: fixed;
			bottom: 20px;
			right: 20px;
			width: 60px;
			height: 60px;
			background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
			transition: all 0.3s ease;
			z-index: 1000;
		}

		.ai-chat-widget:hover {
			transform: scale(1.1);
			box-shadow: 0 12px 35px rgba(79, 70, 229, 0.6);
		}

		.ai-chat-widget i {
			color: white;
			font-size: 1.5rem;
			animation: pulse 2s infinite;
		}

		/* SMART SEARCH BAR */
		.smart-search {
			position: relative;
			max-width: 500px;
			margin: 0 auto;
		}

		.smart-search input {
			width: 100%;
			padding: 15px 50px 15px 20px;
			border: 2px solid rgba(255, 255, 255, 0.4);
			border-radius: 50px;
			background: rgba(255, 255, 255, 0.9);
			backdrop-filter: blur(10px);
			color: #1e293b;
			font-size: 1rem;
			font-weight: 500;
			transition: all 0.3s ease;
		}

		.smart-search input:focus {
			outline: none;
			border-color: #4f46e5;
			background: rgba(255, 255, 255, 0.95);
			box-shadow: 0 0 20px rgba(79, 70, 229, 0.4);
		}

		.smart-search input::placeholder {
			color: #64748b;
		}

		.smart-search button {
			position: absolute;
			right: 5px;
			top: 50%;
			transform: translateY(-50%);
			background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
			border: none;
			border-radius: 50%;
			width: 40px;
			height: 40px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: white;
			cursor: pointer;
			transition: all 0.3s ease;
		}

		.smart-search button:hover {
			transform: translateY(-50%) scale(1.1);
		}

		/* ANALYTICS DASHBOARD */
		.analytics-card {
			background: rgba(255, 255, 255, 0.1);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 20px;
			padding: 2rem;
			margin: 1rem 0;
			transition: all 0.3s ease;
		}

		.analytics-card:hover {
			background: rgba(255, 255, 255, 0.15);
			transform: translateY(-5px);
		}

		.stat-item {
			text-align: center;
			padding: 1rem;
		}

		.stat-number {
			font-size: 2.5rem;
			font-weight: 800;
			color: #1e293b;
			text-shadow: 0 2px 4px rgba(255,255,255,0.8);
		}

		.stat-label {
			font-size: 0.9rem;
			color: #334155;
			text-transform: uppercase;
			letter-spacing: 1px;
			font-weight: 600;
			text-shadow: 0 1px 2px rgba(255,255,255,0.6);
		}

		/* MODERN CARD GRID */
		.feature-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
			gap: 2rem;
			margin: 2rem 0;
		}

		.feature-card {
			background: rgba(255, 255, 255, 0.15);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.3);
			border-radius: 20px;
			padding: 2rem;
			text-align: center;
			transition: all 0.3s ease;
			position: relative;
			overflow: hidden;
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
		}

		.feature-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
			transition: left 0.6s;
		}

		.feature-card:hover::before {
			left: 100%;
		}

		.feature-card:hover {
			transform: translateY(-10px);
			background: rgba(255, 255, 255, 0.25);
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
		}

		.feature-icon {
			font-size: 3rem;
			color: #06b6d4;
			margin-bottom: 1rem;
			transition: all 0.3s ease;
			text-shadow: 0 2px 4px rgba(0,0,0,0.3);
		}

		.feature-card:hover .feature-icon {
			transform: scale(1.2) rotate(5deg);
			color: #4f46e5;
			text-shadow: 0 4px 8px rgba(0,0,0,0.5);
		}

		/* NOTIFICATION SYSTEM */
		.notification {
			position: fixed;
			top: 100px;
			right: 20px;
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 15px;
			padding: 1rem 1.5rem;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
			transform: translateX(400px);
			transition: all 0.3s ease;
			z-index: 1000;
			max-width: 300px;
		}

		.notification.show {
			transform: translateX(0);
		}

		.notification.success {
			border-left: 4px solid #10b981;
		}

		.notification.info {
			border-left: 4px solid #06b6d4;
		}

		.notification.warning {
			border-left: 4px solid #f59e0b;
		}

		/* LOADING STATES */
		.loading-skeleton {
			background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
			background-size: 200% 100%;
			animation: shimmer 2s infinite;
			border-radius: 10px;
		}

		@keyframes shimmer {
			0% { background-position: -200% 0; }
			100% { background-position: 200% 0; }
		}

		/* ADVANCED ANIMATIONS & EFFECTS */
		.parallax-container {
			position: relative;
			overflow: hidden;
		}

		.parallax-element {
			position: absolute;
			will-change: transform;
		}

		.floating-element {
			animation: float 6s ease-in-out infinite;
		}

		@keyframes float {
			0%, 100% { transform: translateY(0px) rotate(0deg); }
			50% { transform: translateY(-20px) rotate(2deg); }
		}

		.pulse-glow {
			animation: pulseGlow 2s ease-in-out infinite alternate;
		}

		@keyframes pulseGlow {
			from { box-shadow: 0 0 20px rgba(79, 70, 229, 0.4); }
			to { box-shadow: 0 0 40px rgba(79, 70, 229, 0.8), 0 0 60px rgba(6, 182, 212, 0.4); }
		}

		.morphing-shape {
			animation: morph 8s ease-in-out infinite;
		}

		@keyframes morph {
			0%, 100% { border-radius: 20px; }
			25% { border-radius: 50px 20px 50px 20px; }
			50% { border-radius: 20px 50px 20px 50px; }
			75% { border-radius: 50px; }
		}

		/* ADVANCED CARD EFFECTS */
		.advanced-card {
			position: relative;
			background: rgba(255, 255, 255, 0.1);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 24px;
			overflow: hidden;
			transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
		}

		.advanced-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
			transform: translateX(-100%);
			transition: transform 0.6s;
		}

		.advanced-card:hover::before {
			transform: translateX(100%);
		}

		.advanced-card:hover {
			transform: translateY(-15px) scale(1.02);
			box-shadow: 0 25px 50px rgba(79, 70, 229, 0.3);
			border-color: rgba(79, 70, 229, 0.4);
		}

		/* PREMIUM BUTTON EFFECTS */
		.premium-btn {
			position: relative;
			background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
			border: none;
			border-radius: 50px;
			padding: 15px 30px;
			color: white;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: 1px;
			overflow: hidden;
			transition: all 0.3s ease;
			box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
		}

		.premium-btn::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
			transition: left 0.5s;
		}

		.premium-btn:hover::before {
			left: 100%;
		}

		.premium-btn:hover {
			transform: translateY(-3px) scale(1.05);
			box-shadow: 0 15px 35px rgba(79, 70, 229, 0.6);
		}

		.premium-btn:active {
			transform: translateY(-1px) scale(1.02);
		}

		/* INTERACTIVE ELEMENTS */
		.interactive-element {
			cursor: pointer;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		}

		.interactive-element:hover {
			transform: scale(1.05);
		}

		.interactive-element:active {
			transform: scale(0.95);
		}

		/* GRADIENT TEXT EFFECTS */
		.gradient-text {
			background: linear-gradient(135deg, #4f46e5, #06b6d4, #8b5cf6);
			background-size: 200% 200%;
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
			animation: gradientShift 3s ease infinite;
		}

		@keyframes gradientShift {
			0%, 100% { background-position: 0% 50%; }
			50% { background-position: 100% 50%; }
		}

		/* 3D TRANSFORM EFFECTS */
		.transform-3d {
			transform-style: preserve-3d;
			transition: transform 0.3s ease;
		}

		.transform-3d:hover {
			transform: rotateY(10deg) rotateX(5deg);
		}

		/* PARTICLE SYSTEM */
		.particle {
			position: absolute;
			width: 4px;
			height: 4px;
			background: rgba(79, 70, 229, 0.6);
			border-radius: 50%;
			pointer-events: none;
			animation: particleFloat 8s linear infinite;
		}

		@keyframes particleFloat {
			0% {
				transform: translateY(100vh) rotate(0deg);
				opacity: 0;
			}
			10% {
				opacity: 1;
			}
			90% {
				opacity: 1;
			}
			100% {
				transform: translateY(-100px) rotate(360deg);
				opacity: 0;
			}
		}

		/* ADVANCED LOADING STATES */
		.skeleton-loader {
			background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
			background-size: 200% 100%;
			animation: skeleton 1.5s infinite;
		}

		@keyframes skeleton {
			0% { background-position: -200% 0; }
			100% { background-position: 200% 0; }
		}

		/* GLASSMORPHISM ENHANCEMENTS */
		.glass-card {
			background: rgba(255, 255, 255, 0.1);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 20px;
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
		}

		.glass-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
			border-radius: inherit;
			pointer-events: none;
		}

		/* ADVANCED HOVER EFFECTS */
		.hover-lift {
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		}

		.hover-lift:hover {
			transform: translateY(-8px);
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
		}

		.hover-glow:hover {
			box-shadow: 0 0 30px rgba(79, 70, 229, 0.5);
		}

		/* ADVANCED GRADIENTS */
		.rainbow-gradient {
			background: linear-gradient(45deg, #4f46e5, #06b6d4, #8b5cf6, #ec4899, #f59e0b, #10b981);
			background-size: 400% 400%;
			animation: rainbow 4s ease infinite;
		}
		
		/* CUSTOMER DASHBOARD STYLES */
		.stat-card {
			background: rgba(255, 255, 255, 0.1);
			border-radius: 10px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			transition: all 0.3s ease;
		}
		
		.stat-card:hover {
			background: rgba(255, 255, 255, 0.2);
			transform: translateY(-2px);
		}
		
		.bg-gradient-primary {
			background: linear-gradient(135deg, #4f46e5, #06b6d4) !important;
		}
		
		.bg-gradient-danger {
			background: linear-gradient(135deg, #ef4444, #f97316) !important;
		}
		
		.bg-gradient-success {
			background: linear-gradient(135deg, #10b981, #059669) !important;
		}
		
		.bg-gradient-info {
			background: linear-gradient(135deg, #06b6d4, #3b82f6) !important;
		}
		
		.product-preview-card {
			background: rgba(255, 255, 255, 0.1);
			border-radius: 15px;
			padding: 15px;
			text-align: center;
			transition: all 0.3s ease;
			border: 1px solid rgba(255, 255, 255, 0.2);
		}
		
		.product-preview-card:hover {
			background: rgba(255, 255, 255, 0.2);
			transform: translateY(-5px);
			box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
		}
		
		.product-image-placeholder {
			height: 120px;
			background: rgba(255, 255, 255, 0.1);
			border-radius: 10px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-bottom: 10px;
		}
		
		.product-info h6 {
			color: #1e293b;
			font-weight: 600;
			margin-bottom: 5px;
		}
		
		.product-info p {
			color: #4f46e5;
			font-weight: 700;
			font-size: 1.1rem;
		}
		
		/* PROFESSIONAL DASHBOARD STYLES */
		.professional-card {
			border: none;
			border-radius: 15px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
			transition: all 0.3s ease;
		}
		
		.professional-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
		}
		
		.bg-gradient-secondary {
			background: linear-gradient(135deg, #6c757d, #495057) !important;
		}
		
		.action-card {
			display: block;
			text-decoration: none;
			color: inherit;
			background: rgba(255, 255, 255, 0.1);
			border-radius: 12px;
			padding: 20px;
			text-align: center;
			transition: all 0.3s ease;
			border: 1px solid rgba(255, 255, 255, 0.2);
		}
		
		.action-card:hover {
			background: rgba(255, 255, 255, 0.2);
			transform: translateY(-3px);
			text-decoration: none;
			color: inherit;
		}
		
		/* Professional Stat Cards */
		.stat-card-professional {
			display: flex;
			align-items: center;
			padding: 20px;
			background: white;
			border-radius: 12px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
			transition: all 0.3s ease;
			height: 100%;
		}
		
		.stat-card-professional:hover {
			transform: translateY(-3px);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
		}
		
		.stat-card-professional .stat-icon {
			width: 50px;
			height: 50px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 20px;
			color: white;
			margin-right: 15px;
			flex-shrink: 0;
		}
		
		.stat-card-professional .stat-content {
			flex: 1;
		}
		
		.stat-card-professional .stat-number {
			font-size: 1.8rem;
			font-weight: 700;
			color: #1f2937;
			margin-bottom: 2px;
		}
		
		.stat-card-professional .stat-label {
			color: #6b7280;
			font-size: 0.9rem;
			font-weight: 500;
		}
		
		.action-icon {
			width: 60px;
			height: 60px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 15px;
			font-size: 24px;
			color: white;
		}
		
		.action-content h6 {
			font-weight: 600;
			margin-bottom: 5px;
			color: #1e293b;
		}
		
		.stat-card-professional {
			display: flex;
			align-items: center;
			background: rgba(255, 255, 255, 0.1);
			border-radius: 12px;
			padding: 20px;
			transition: all 0.3s ease;
			border: 1px solid rgba(255, 255, 255, 0.2);
		}
		
		.stat-card-professional:hover {
			background: rgba(255, 255, 255, 0.2);
			transform: translateY(-2px);
		}
		
		.stat-icon {
			width: 50px;
			height: 50px;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-right: 15px;
			font-size: 20px;
			color: white;
		}
		
		.stat-content .stat-number {
			font-size: 1.5rem;
			font-weight: 700;
			color: #1e293b;
			margin-bottom: 5px;
		}
		
		.stat-content .stat-label {
			font-size: 0.9rem;
			color: #64748b;
			font-weight: 500;
		}
		
		.profile-card {
			height: fit-content;
		}
		
		.profile-avatar {
			width: 80px;
			height: 80px;
			background: rgba(79, 70, 229, 0.1);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto;
		}
		
		.profile-name {
			font-weight: 600;
			color: #1e293b;
			margin-bottom: 5px;
		}
		
		.profile-email {
			font-size: 0.9rem;
			margin-bottom: 10px;
		}
		
		.profile-badge {
			font-size: 0.8rem;
			padding: 5px 10px;
		}

		@keyframes rainbow {
			0%, 100% { background-position: 0% 50%; }
			50% { background-position: 100% 50%; }
		}

		/* TEXT VISIBILITY IMPROVEMENTS */
		.feature-card h5 {
			color: #1e293b !important;
			font-weight: 700;
			text-shadow: 0 1px 2px rgba(255,255,255,0.8);
			margin-bottom: 1rem;
		}

		.feature-card p {
			color: #334155 !important;
			text-shadow: 0 1px 2px rgba(255,255,255,0.6);
			font-weight: 500;
			line-height: 1.6;
		}

		.analytics-card {
			background: rgba(255, 255, 255, 0.15);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.3);
			border-radius: 20px;
			padding: 2rem;
			margin: 1rem 0;
			transition: all 0.3s ease;
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
		}

		.analytics-card:hover {
			background: rgba(255, 255, 255, 0.2);
			transform: translateY(-5px);
		}

		/* IMPROVED CARD TEXT CONTRAST */
		.card h3, .card h5 {
			color: #1e293b !important;
			text-shadow: 0 1px 2px rgba(255,255,255,0.8);
		}

		.card p {
			color: #334155 !important;
			text-shadow: 0 1px 2px rgba(255,255,255,0.6);
		}

		.welcome-card h3 {
			color: #1e293b !important;
			text-shadow: 0 1px 2px rgba(255,255,255,0.8);
		}

		.welcome-card p {
			color: #334155 !important;
			text-shadow: 0 1px 2px rgba(255,255,255,0.6);
		}

		.user-info p {
			color: #334155 !important;
			text-shadow: 0 1px 2px rgba(255,255,255,0.6);
		}

		.user-info strong {
			color: #1e293b !important;
			text-shadow: 0 1px 2px rgba(255,255,255,0.8);
		}
		
		/* LOADING EFFECTS */
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
						<a class="nav-link" href="all_product.php">
							<i class="fa fa-shopping-bag"></i> Products
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="all_product.php">
							<i class="fa fa-info-circle"></i> About
						</a>
					</li>
				</ul>
				
				<ul class="navbar-nav">
					<?php if (is_logged_in()): ?>
						<!-- User is logged in -->
						<?php if (is_admin()): ?>
							<!-- Admin user -->
							<li class="nav-item">
								<a class="nav-link" href="admin/category.php">
									<i class="fa fa-tags"></i> Category
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="admin/brand.php">
									<i class="fa fa-industry"></i> Brand
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="admin/product.php">
									<i class="fa fa-shopping-bag"></i> Add Product
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="actions/logout_action.php">
									<i class="fa fa-sign-out-alt"></i> Logout
								</a>
							</li>
						<?php else: ?>
							<!-- Regular user -->
							<li class="nav-item">
								<a class="nav-link" href="cart.php">
									<i class="fa fa-shopping-cart"></i> Cart
									<span id="cartBadge" class="badge bg-danger ms-1" style="display: none;">0</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="actions/logout_action.php">
									<i class="fa fa-sign-out-alt"></i> Logout
								</a>
							</li>
						<?php endif; ?>
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
		<?php if (is_logged_in()): ?>
			
			<!-- User Dashboard -->
			<div class="row">
				<div class="col-12">
					<div class="hero-section text-center">
						<h1 class="display-4 mb-4">
							<i class="fa fa-shopping-bag text-primary"></i> Welcome Back, <?php echo htmlspecialchars(get_user_first_name()); ?>!
						</h1>
						<p class="lead text-muted">Ready to explore our amazing products and deals?</p>
						<?php if (is_admin()): ?>
                        <div class="mt-3">
                            <a href="admin/category.php" class="btn btn-warning btn-lg me-2">
                                <i class="fa fa-tags"></i> Manage Categories
                            </a>
                            <a href="admin/brand.php" class="btn btn-info btn-lg me-2">
                                <i class="fa fa-star"></i> Manage Brands
                            </a>
                            <a href="admin/product.php" class="btn btn-success btn-lg">
                                <i class="fa fa-plus"></i> Add Product
                            </a>
                        </div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			
			<!-- Customer Stats Overview -->
			<div class="row mb-4">
				<div class="col-12">
					<div class="card professional-card">
						<div class="card-header bg-gradient-info text-white">
							<h5 class="mb-0"><i class="fa fa-chart-line"></i> Your Shopping Overview</h5>
						</div>
						<div class="card-body">
							<div class="row text-center">
								<div class="col-md-3 col-6 mb-3">
									<div class="stat-card-professional">
										<div class="stat-icon bg-primary">
											<i class="fa fa-shopping-cart"></i>
										</div>
										<div class="stat-content">
											<div class="stat-number" id="cart-count">3</div>
											<div class="stat-label">Items in Cart</div>
										</div>
									</div>
								</div>
								<div class="col-md-3 col-6 mb-3">
									<div class="stat-card-professional">
										<div class="stat-icon bg-success">
											<i class="fa fa-check-circle"></i>
										</div>
										<div class="stat-content">
											<div class="stat-number" id="orders-count">7</div>
											<div class="stat-label">Orders Placed</div>
										</div>
									</div>
								</div>
								<div class="col-md-3 col-6 mb-3">
									<div class="stat-card-professional">
										<div class="stat-icon bg-warning">
											<i class="fa fa-heart"></i>
										</div>
										<div class="stat-content">
											<div class="stat-number" id="wishlist-count">12</div>
											<div class="stat-label">Wishlist Items</div>
										</div>
									</div>
								</div>
								<div class="col-md-3 col-6 mb-3">
									<div class="stat-card-professional">
										<div class="stat-icon bg-info">
											<i class="fa fa-star"></i>
										</div>
										<div class="stat-content">
											<div class="stat-number" id="reviews-count">5</div>
											<div class="stat-label">Reviews Given</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					</div>
				</div>
				
			<div class="row">
				<div class="col-12">
					<div class="card professional-card">
						<div class="card-header bg-gradient-primary text-white">
							<h5 class="mb-0"><i class="fa fa-rocket"></i> Quick Actions</h5>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-3 mb-3">
									<a href="all_product.php" class="action-card">
										<div class="action-icon bg-primary">
											<i class="fa fa-store"></i>
										</div>
										<div class="action-content">
											<h6>Browse Products</h6>
											<p class="text-muted">15+ artisan items</p>
										</div>
									</a>
								</div>
								<div class="col-md-3 mb-3">
									<a href="all_product.php" class="action-card">
										<div class="action-icon bg-info">
											<i class="fa fa-shopping-cart"></i>
										</div>
										<div class="action-content">
											<h6>My Cart</h6>
											<p class="text-muted">3 items</p>
										</div>
									</a>
								</div>
								<div class="col-md-3 mb-3">
									<a href="all_product.php" class="action-card">
										<div class="action-icon bg-success">
											<i class="fa fa-history"></i>
										</div>
										<div class="action-content">
											<h6>Order History</h6>
											<p class="text-muted">7 orders</p>
										</div>
									</a>
								</div>
								<div class="col-md-3 mb-3">
									<a href="all_product.php" class="action-card">
										<div class="action-icon bg-warning">
											<i class="fa fa-heart"></i>
										</div>
										<div class="action-content">
											<h6>Wishlist</h6>
											<p class="text-muted">12 items</p>
										</div>
									</a>
								</div>
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
							<i class="fa fa-shopping-bag"></i> E-Commerce Store
						</h1>
						<p class="lead text-muted">Your one-stop shop for amazing products and great deals</p>
						
						<!-- Search Bar -->
						<div class="smart-search mt-4">
							<input type="text" placeholder="Search products, categories, or brands..." id="smart-search">
							<button type="button" onclick="performSearch()">
								<i class="fa fa-search"></i>
							</button>
						</div>
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
			
			<!-- Analytics Dashboard -->
			<div class="analytics-card">
				<div class="row text-center">
					<div class="col-md-3 col-6">
						<div class="stat-item">
							<div class="stat-number">1,250+</div>
							<div class="stat-label">Products</div>
						</div>
					</div>
					<div class="col-md-3 col-6">
						<div class="stat-item">
							<div class="stat-number">50+</div>
							<div class="stat-label">Categories</div>
						</div>
					</div>
					<div class="col-md-3 col-6">
						<div class="stat-item">
							<div class="stat-number">10K+</div>
							<div class="stat-label">Happy Customers</div>
						</div>
					</div>
					<div class="col-md-3 col-6">
						<div class="stat-item">
							<div class="stat-number">99.9%</div>
							<div class="stat-label">Uptime</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Features Section -->
			<div class="feature-grid">
				<div class="feature-card">
					<div class="feature-icon">
						<i class="fa fa-store"></i>
						</div>
					<h5 class="text-white mb-3">Browse Products</h5>
					<p class="text-white-50">Explore our wide range of quality products and brands</p>
					</div>
				<div class="feature-card">
					<div class="feature-icon">
						<i class="fa fa-shopping-cart"></i>
				</div>
					<h5 class="text-white mb-3">Smart Shopping</h5>
					<p class="text-white-50">Easy cart management and checkout experience</p>
						</div>
				<div class="feature-card">
					<div class="feature-icon">
						<i class="fa fa-heart"></i>
					</div>
					<h5 class="text-white mb-3">Personalized Lists</h5>
					<p class="text-white-50">Create and manage your wishlists</p>
				</div>
				<div class="feature-card">
					<div class="feature-icon">
						<i class="fa fa-robot"></i>
						</div>
					<h5 class="text-white mb-3">AI Assistant</h5>
					<p class="text-white-50">Get help with our intelligent shopping assistant</p>
					</div>
				<div class="feature-card">
					<div class="feature-icon">
						<i class="fa fa-shipping-fast"></i>
					</div>
					<h5 class="text-white mb-3">Fast Delivery</h5>
					<p class="text-white-50">Quick delivery with real-time tracking</p>
				</div>
				<div class="feature-card">
					<div class="feature-icon">
						<i class="fa fa-shield-alt"></i>
					</div>
					<h5 class="text-white mb-3">Secure Payments</h5>
					<p class="text-white-50">Bank-level security with multiple payment options</p>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<!-- AI Chat Widget -->
	<div class="ai-chat-widget" onclick="openAIChat()">
		<i class="fa fa-robot"></i>
	</div>

	<!-- Notification Container -->
	<div id="notification-container"></div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<?php if (is_logged_in() && !is_admin()): ?>
	<script src="js/cart.js"></script>
	<?php endif; ?>
	<script>
		// 🚀 ULTIMATE JAVASCRIPT - PREMIUM INTERACTIONS 🚀
		
		document.addEventListener('DOMContentLoaded', function() {
			// Initialize Bootstrap dropdowns
			var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
			var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
				return new bootstrap.Dropdown(dropdownToggleEl);
			});
			
			// Animate shopping overview numbers
			animateShoppingNumbers();
			
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

			// 🤖 AI-ENHANCED FEATURES 🤖
			initializeAIFeatures();
		});

		// 🚀 AI-ENHANCED FUNCTIONS 🚀
		function initializeAIFeatures() {
			// Smart search functionality
			const searchInput = document.getElementById('smart-search');
			if (searchInput) {
				searchInput.addEventListener('input', debounce(handleSmartSearch, 300));
			}

			// Show welcome notification
			setTimeout(() => {
				showNotification('Welcome to our AI-powered store!', 'info');
			}, 2000);

			// Initialize analytics animations
			animateCounters();
		}

		function performSearch() {
			const searchTerm = document.getElementById('smart-search').value;
			if (searchTerm.trim()) {
				showNotification(`Searching for "${searchTerm}"...`, 'info');
				// Simulate AI search
				setTimeout(() => {
					showNotification(`Found 15 results for "${searchTerm}"`, 'success');
				}, 1500);
			}
		}

		function handleSmartSearch(event) {
			const query = event.target.value;
			if (query.length > 2) {
				// Simulate AI-powered search suggestions
				console.log('AI Search Query:', query);
				// Here you would typically make an API call to your search endpoint
			}
		}

		function openAIChat() {
			showNotification('AI Assistant is coming soon! Chat with our intelligent shopping assistant.', 'info');
			// Here you would open a chat modal or redirect to chat page
		}

		function showNotification(message, type = 'info') {
			const container = document.getElementById('notification-container');
			const notification = document.createElement('div');
			notification.className = `notification ${type}`;
			notification.innerHTML = `
				<div class="d-flex align-items-center">
					<i class="fa fa-${getNotificationIcon(type)} me-2"></i>
					<span>${message}</span>
					<button class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
				</div>
			`;
			
			container.appendChild(notification);
			
			// Show notification
			setTimeout(() => {
				notification.classList.add('show');
			}, 100);
			
			// Auto remove after 5 seconds
			setTimeout(() => {
				notification.classList.remove('show');
				setTimeout(() => {
					if (notification.parentNode) {
						notification.remove();
					}
				}, 300);
			}, 5000);
		}

		function getNotificationIcon(type) {
			const icons = {
				'success': 'check-circle',
				'info': 'info-circle',
				'warning': 'exclamation-triangle',
				'error': 'times-circle'
			};
			return icons[type] || 'info-circle';
		}

		function animateCounters() {
			const counters = document.querySelectorAll('.stat-number');
			counters.forEach(counter => {
				const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
				const suffix = counter.textContent.replace(/[\d]/g, '');
				let current = 0;
				const increment = target / 50;
				
				const timer = setInterval(() => {
					current += increment;
					if (current >= target) {
						counter.textContent = target + suffix;
						clearInterval(timer);
					} else {
						counter.textContent = Math.floor(current) + suffix;
					}
				}, 30);
			});
		}

		function debounce(func, wait) {
			let timeout;
			return function executedFunction(...args) {
				const later = () => {
					clearTimeout(timeout);
					func(...args);
				};
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
			};
		}

		// 🎯 ADVANCED INTERACTIONS 🎯
		function addToCart(productId) {
			showNotification('Product added to cart!', 'success');
			// Here you would add the product to cart
		}

		function addToWishlist(productId) {
			showNotification('Added to wishlist!', 'success');
			// Here you would add the product to wishlist
		}

		// 🎨 ENHANCED SCROLL EFFECTS 🎨
		function initScrollEffects() {
			const observerOptions = {
				threshold: 0.1,
				rootMargin: '0px 0px -50px 0px'
			};

			const observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.classList.add('fade-in');
					}
				});
			}, observerOptions);

			// Observe all feature cards
			document.querySelectorAll('.feature-card, .analytics-card').forEach(el => {
				observer.observe(el);
			});
		}

		// Initialize scroll effects
		initScrollEffects();

		// TYPING ANIMATIONS - SIMPLIFIED AND WORKING
		initTypingAnimations();
		
		// Animate shopping overview numbers
		function animateShoppingNumbers() {
			const cartCount = document.getElementById('cart-count');
			const ordersCount = document.getElementById('orders-count');
			const wishlistCount = document.getElementById('wishlist-count');
			const reviewsCount = document.getElementById('reviews-count');
			
			if (cartCount) animateNumber(cartCount, 3);
			if (ordersCount) animateNumber(ordersCount, 7);
			if (wishlistCount) animateNumber(wishlistCount, 12);
			if (reviewsCount) animateNumber(reviewsCount, 5);
		}
		
		function animateNumber(element, target) {
			let current = 0;
			const increment = target / 30;
			const timer = setInterval(() => {
				current += increment;
				if (current >= target) {
					element.textContent = target;
					clearInterval(timer);
				} else {
					element.textContent = Math.floor(current);
				}
			}, 50);
		}
		
		// Add a test button for manual animation trigger
		const testButton = document.createElement('button');
		testButton.textContent = '🎬 Test Animations';
		testButton.style.position = 'fixed';
		testButton.style.top = '10px';
		testButton.style.right = '10px';
		testButton.style.zIndex = '9999';
		testButton.style.padding = '10px';
		testButton.style.backgroundColor = '#4f46e5';
		testButton.style.color = 'white';
		testButton.style.border = 'none';
		testButton.style.borderRadius = '5px';
		testButton.style.cursor = 'pointer';
		testButton.onclick = function() {
			console.log('🎬 Manual animation trigger!');
			animateGuestPage();
		};
		document.body.appendChild(testButton);
	});

	// ADVANCED FEATURES FUNCTIONS
	function initAdvancedFeatures() {
		// Add advanced hover effects to all interactive elements
		document.querySelectorAll('.interactive-element').forEach(element => {
			element.addEventListener('mouseenter', function() {
				this.style.transform = 'scale(1.05) rotate(2deg)';
			});
			
			element.addEventListener('mouseleave', function() {
				this.style.transform = 'scale(1) rotate(0deg)';
			});
		});

		// Add ripple effect to buttons
		document.querySelectorAll('.premium-btn').forEach(button => {
			button.addEventListener('click', function(e) {
				createRippleEffect(e, this);
			});
		});

		// Add typing animation to hero text
		initTypingAnimation();
	}

	function initParticleSystem() {
		// Create floating particles
		setInterval(() => {
			createParticle();
		}, 2000);

		// Create initial particles
		for (let i = 0; i < 5; i++) {
			setTimeout(() => createParticle(), i * 1000);
		}
	}

	function createParticle() {
		const particle = document.createElement('div');
		particle.className = 'particle';
		particle.style.left = Math.random() * window.innerWidth + 'px';
		particle.style.animationDelay = Math.random() * 2 + 's';
		particle.style.animationDuration = (Math.random() * 3 + 5) + 's';
		
		document.body.appendChild(particle);
		
		setTimeout(() => {
			if (particle.parentNode) {
				particle.remove();
			}
		}, 8000);
	}

	function initParallaxEffects() {
		window.addEventListener('scroll', () => {
			const scrolled = window.pageYOffset;
			const parallaxElements = document.querySelectorAll('.parallax-element');
			
			parallaxElements.forEach((element, index) => {
				const speed = 0.5 + (index * 0.1);
				element.style.transform = `translateY(${scrolled * speed}px)`;
			});
		});
	}

	function initAdvancedAnimations() {
		// Intersection Observer for advanced animations
		const observer = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					entry.target.classList.add('animate-in');
					
					// Add staggered animation to child elements
					const children = entry.target.querySelectorAll('.feature-card, .stat-item');
					children.forEach((child, index) => {
						setTimeout(() => {
							child.style.opacity = '1';
							child.style.transform = 'translateY(0)';
						}, index * 100);
					});
				}
			});
		}, { threshold: 0.1 });

		// Observe elements for animation
		document.querySelectorAll('.feature-grid, .analytics-card').forEach(el => {
			observer.observe(el);
		});
	}

	function createRippleEffect(event, element) {
		const ripple = document.createElement('span');
		const rect = element.getBoundingClientRect();
		const size = Math.max(rect.width, rect.height);
		const x = event.clientX - rect.left - size / 2;
		const y = event.clientY - rect.top - size / 2;
		
		ripple.style.width = ripple.style.height = size + 'px';
		ripple.style.left = x + 'px';
		ripple.style.top = y + 'px';
		ripple.style.position = 'absolute';
		ripple.style.borderRadius = '50%';
		ripple.style.background = 'rgba(255, 255, 255, 0.6)';
		ripple.style.transform = 'scale(0)';
		ripple.style.animation = 'ripple 0.6s linear';
		ripple.style.pointerEvents = 'none';
		
		element.appendChild(ripple);
		
		setTimeout(() => {
			ripple.remove();
		}, 600);
	}

	function initTypingAnimation() {
		const heroText = document.querySelector('.hero-section h1');
		if (heroText) {
			const text = heroText.textContent;
			heroText.textContent = '';
			heroText.style.borderRight = '3px solid #4f46e5';
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
	}

	// PERFORMANCE OPTIMIZATIONS
	function initPerformanceOptimizations() {
		// Throttle scroll events for better performance
		let scrollTimeout;
		window.addEventListener('scroll', function() {
			if (!scrollTimeout) {
				scrollTimeout = setTimeout(function() {
					handleScrollOptimized();
					scrollTimeout = null;
				}, 16); // ~60fps
			}
		});

		// Use requestAnimationFrame for smooth animations
		function animateWithRAF(callback) {
			requestAnimationFrame(callback);
		}

		// Lazy load images and elements
		const imageObserver = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const img = entry.target;
					img.src = img.dataset.src;
					img.classList.remove('lazy');
					imageObserver.unobserve(img);
				}
			});
		});

		document.querySelectorAll('img[data-src]').forEach(img => {
			imageObserver.observe(img);
		});
	}

	function handleScrollOptimized() {
		const scrolled = window.pageYOffset;
		const parallaxElements = document.querySelectorAll('.parallax-element');
		
		parallaxElements.forEach((element, index) => {
			const speed = 0.5 + (index * 0.1);
			element.style.transform = `translateY(${scrolled * speed}px)`;
		});
	}

	// ADVANCED INTERACTIONS
	function initAdvancedInteractions() {
		// Advanced mouse tracking for interactive elements
		document.addEventListener('mousemove', function(e) {
			const cursor = document.querySelector('.custom-cursor');
			if (cursor) {
				cursor.style.left = e.clientX + 'px';
				cursor.style.top = e.clientY + 'px';
			}
		});

		// Create custom cursor
		createCustomCursor();

		// Advanced keyboard navigation
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Tab') {
				document.body.classList.add('keyboard-navigation');
			}
		});

		document.addEventListener('mousedown', function() {
			document.body.classList.remove('keyboard-navigation');
		});

		// Advanced touch gestures for mobile
		let touchStartX = 0;
		let touchStartY = 0;

		document.addEventListener('touchstart', function(e) {
			touchStartX = e.touches[0].clientX;
			touchStartY = e.touches[0].clientY;
		});

		document.addEventListener('touchend', function(e) {
			if (!touchStartX || !touchStartY) return;

			const touchEndX = e.changedTouches[0].clientX;
			const touchEndY = e.changedTouches[0].clientY;

			const diffX = touchStartX - touchEndX;
			const diffY = touchStartY - touchEndY;

			// Swipe detection
			if (Math.abs(diffX) > Math.abs(diffY)) {
				if (diffX > 50) {
					// Swipe left
					handleSwipe('left');
				} else if (diffX < -50) {
					// Swipe right
					handleSwipe('right');
				}
			}

			touchStartX = 0;
			touchStartY = 0;
		});
	}

	function createCustomCursor() {
		const cursor = document.createElement('div');
		cursor.className = 'custom-cursor';
		cursor.style.cssText = `
			position: fixed;
			width: 20px;
			height: 20px;
			background: radial-gradient(circle, rgba(79, 70, 229, 0.8) 0%, transparent 70%);
			border-radius: 50%;
			pointer-events: none;
			z-index: 9999;
			transition: transform 0.1s ease;
		`;
		document.body.appendChild(cursor);
	}

	function handleSwipe(direction) {
		console.log(`Swipe ${direction} detected`);
		// Add swipe functionality here
	}

	// DATA VISUALIZATION
	function initDataVisualization() {
		// Animate counters with easing
		animateCountersAdvanced();
		
		// Create progress bars
		createProgressBars();
		
		// Initialize charts if needed
		initCharts();
	}

	function animateCountersAdvanced() {
		const counters = document.querySelectorAll('.stat-number');
		const observer = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					animateCounter(entry.target);
					observer.unobserve(entry.target);
				}
			});
		});

		counters.forEach(counter => observer.observe(counter));
	}

	function animateCounter(element) {
		const target = parseInt(element.textContent.replace(/[^\d]/g, ''));
		const suffix = element.textContent.replace(/[\d]/g, '');
		let current = 0;
		const increment = target / 100;
		const duration = 2000; // 2 seconds
		const stepTime = duration / 100;

		const timer = setInterval(() => {
			current += increment;
			if (current >= target) {
				element.textContent = target + suffix;
				clearInterval(timer);
			} else {
				element.textContent = Math.floor(current) + suffix;
			}
		}, stepTime);
	}

	function createProgressBars() {
		const progressBars = document.querySelectorAll('.progress-bar');
		progressBars.forEach(bar => {
			const percentage = bar.dataset.percentage || 0;
			bar.style.width = '0%';
			
			setTimeout(() => {
				bar.style.transition = 'width 1s ease-in-out';
				bar.style.width = percentage + '%';
			}, 500);
		});
	}

	function initCharts() {
		// Add chart functionality here if needed
		console.log('Charts initialized');
	}

	// ADVANCED SEARCH FUNCTIONALITY
	function initAdvancedSearch() {
		const searchInput = document.getElementById('smart-search');
		if (!searchInput) return;

		let searchTimeout;
		const searchSuggestions = document.createElement('div');
		searchSuggestions.className = 'search-suggestions';
		searchSuggestions.style.cssText = `
			position: absolute;
			top: 100%;
			left: 0;
			right: 0;
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 15px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
			z-index: 1000;
			display: none;
		`;

		searchInput.parentNode.style.position = 'relative';
		searchInput.parentNode.appendChild(searchSuggestions);

		searchInput.addEventListener('input', function() {
			clearTimeout(searchTimeout);
			const query = this.value.trim();

			if (query.length < 2) {
				searchSuggestions.style.display = 'none';
				return;
			}

			searchTimeout = setTimeout(() => {
				performAdvancedSearch(query, searchSuggestions);
			}, 300);
		});

		// Hide suggestions when clicking outside
		document.addEventListener('click', function(e) {
			if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
				searchSuggestions.style.display = 'none';
			}
		});
	}

	function performAdvancedSearch(query, container) {
		// Simulate API call
		const suggestions = [
			'Electronics',
			'Clothing',
			'Home & Garden',
			'Books',
			'Sports & Outdoors'
		].filter(item => 
			item.toLowerCase().includes(query.toLowerCase())
		);

		container.innerHTML = suggestions.map(item => 
			`<div class="suggestion-item" style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid rgba(0,0,0,0.1);">${item}</div>`
		).join('');

		container.style.display = suggestions.length ? 'block' : 'none';

		// Add click handlers to suggestions
		container.querySelectorAll('.suggestion-item').forEach(item => {
			item.addEventListener('click', function() {
				document.getElementById('smart-search').value = this.textContent;
				container.style.display = 'none';
				performSearch();
			});
		});
	}

		// TYPING ANIMATIONS - SIMPLIFIED AND WORKING
		function initTypingAnimations() {
			console.log('🚀 Starting typing animations...');
			alert('🎬 Typing animations are starting! Check console for details.');
			
			// Wait for page to fully load
			setTimeout(() => {
				console.log('⚡ Executing animations...');
				
				// Check if user is logged in or guest
				const heroTitle = document.querySelector('.hero-section h1');
				const isLoggedIn = heroTitle && heroTitle.textContent.includes('Welcome Back');
				
				console.log('Hero title found:', heroTitle);
				console.log('Is logged in:', isLoggedIn);
				
				if (isLoggedIn) {
					// Customer dashboard animations
					console.log('👤 Running customer dashboard animations...');
					animateCustomerDashboard();
				} else {
					// Guest landing page animations
					console.log('👥 Running guest page animations...');
					animateGuestPage();
				}
			}, 1000);
		}
		
		// GUEST PAGE ANIMATIONS
		function animateGuestPage() {
			console.log('🎬 Starting guest page animations...');
			
			// Add a visual indicator that animations are starting
			const heroTitle = document.querySelector('.hero-section h1');
			if (heroTitle) {
				heroTitle.style.border = '2px solid #4f46e5';
				heroTitle.style.padding = '10px';
				heroTitle.style.borderRadius = '10px';
			}
			
			// 1. Type hero title
			typeGuestHeroTitle();
			
			// 2. Type subtitle after title
			setTimeout(() => {
				typeGuestSubtitle();
			}, 2000);
			
			// 3. Type search placeholder
			setTimeout(() => {
				animateSearchPlaceholder();
			}, 3500);
			
			// 4. Type welcome card title
			setTimeout(() => {
				typeWelcomeTitle();
			}, 4500);
			
			// 5. Type welcome subtitle
			setTimeout(() => {
				typeWelcomeSubtitle();
			}, 5500);
			
			// 6. Animate analytics numbers
			setTimeout(() => {
				animateAnalyticsNumbers();
			}, 6500);
			
			// 7. Animate feature cards
			setTimeout(() => {
				animateFeatureCards();
			}, 7500);
		}
		
		function typeGuestHeroTitle() {
			const heroTitle = document.querySelector('.hero-section h1');
			console.log('Guest hero title element:', heroTitle);
			
			if (heroTitle) {
				const text = heroTitle.textContent;
				console.log('Guest hero title text:', text);
				
				// Clear the text and add typing cursor
				heroTitle.textContent = '';
				heroTitle.style.borderRight = '3px solid #4f46e5';
				heroTitle.style.animation = 'blink 1s infinite';
				heroTitle.style.backgroundColor = 'rgba(79, 70, 229, 0.1)';
				
				let i = 0;
				const timer = setInterval(() => {
					if (i < text.length) {
						heroTitle.textContent += text.charAt(i);
						i++;
					} else {
						clearInterval(timer);
						heroTitle.style.borderRight = 'none';
						heroTitle.style.animation = 'none';
						heroTitle.style.backgroundColor = 'transparent';
						console.log('✅ Hero title typing complete!');
					}
				}, 150);
			} else {
				console.log('❌ Hero title not found!');
			}
		}
		
		function typeGuestSubtitle() {
			const subtitle = document.querySelector('.hero-section .lead');
			if (subtitle) {
				const text = subtitle.textContent;
				subtitle.textContent = '';
				
				let i = 0;
				const timer = setInterval(() => {
					if (i < text.length) {
						subtitle.textContent += text.charAt(i);
						i++;
					} else {
						clearInterval(timer);
					}
				}, 50);
			}
		}
		
		function animateSearchPlaceholder() {
			const searchInput = document.querySelector('#smart-search');
			if (searchInput) {
				const placeholder = searchInput.placeholder;
				searchInput.placeholder = '';
				searchInput.style.borderRight = '2px solid #4f46e5';
				
				let i = 0;
				const timer = setInterval(() => {
					if (i < placeholder.length) {
						searchInput.placeholder += placeholder.charAt(i);
						i++;
					} else {
						clearInterval(timer);
						searchInput.style.borderRight = 'none';
					}
				}, 30);
			}
		}
		
		function typeWelcomeTitle() {
			const welcomeTitle = document.querySelector('.welcome-card h3');
			if (welcomeTitle) {
				const text = welcomeTitle.textContent;
				welcomeTitle.textContent = '';
				
				let i = 0;
				const timer = setInterval(() => {
					if (i < text.length) {
						welcomeTitle.textContent += text.charAt(i);
						i++;
					} else {
						clearInterval(timer);
					}
				}, 80);
			}
		}
		
		function typeWelcomeSubtitle() {
			const welcomeSubtitle = document.querySelector('.welcome-card p');
			if (welcomeSubtitle) {
				const text = welcomeSubtitle.textContent;
				welcomeSubtitle.textContent = '';
				
				let i = 0;
				const timer = setInterval(() => {
					if (i < text.length) {
						welcomeSubtitle.textContent += text.charAt(i);
						i++;
					} else {
						clearInterval(timer);
					}
				}, 40);
			}
		}
		
		function animateAnalyticsNumbers() {
			const numbers = document.querySelectorAll('.stat-number');
			console.log('Found analytics numbers:', numbers.length);
			
			numbers.forEach((number, index) => {
				const target = parseInt(number.textContent.replace(/[^\d]/g, ''));
				const suffix = number.textContent.replace(/[\d]/g, '');
				number.textContent = '0' + suffix;
				
				setTimeout(() => {
					let current = 0;
					const increment = target / 30;
					const timer = setInterval(() => {
						current += increment;
						if (current >= target) {
							number.textContent = target + suffix;
							clearInterval(timer);
						} else {
							number.textContent = Math.floor(current) + suffix;
						}
					}, 50);
				}, index * 200);
			});
		}
		
		// CUSTOMER DASHBOARD ANIMATIONS
		function animateCustomerDashboard() {
			// Animate stat cards
			const statCards = document.querySelectorAll('.stat-card');
			statCards.forEach((card, index) => {
				card.style.opacity = '0';
				card.style.transform = 'translateY(20px)';
				
				setTimeout(() => {
					card.style.transition = 'all 0.6s ease';
					card.style.opacity = '1';
					card.style.transform = 'translateY(0)';
				}, 2000 + (index * 200));
			});
			
			// Animate product preview cards
			const productCards = document.querySelectorAll('.product-preview-card');
			productCards.forEach((card, index) => {
				card.style.opacity = '0';
				card.style.transform = 'scale(0.8)';
				
				setTimeout(() => {
					card.style.transition = 'all 0.5s ease';
					card.style.opacity = '1';
					card.style.transform = 'scale(1)';
				}, 3000 + (index * 100));
			});
			
			// Add hover effects to action buttons
			const actionButtons = document.querySelectorAll('.btn');
			actionButtons.forEach(button => {
				button.addEventListener('mouseenter', function() {
					this.style.transform = 'translateY(-2px)';
					this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
				});
				
				button.addEventListener('mouseleave', function() {
					this.style.transform = 'translateY(0)';
					this.style.boxShadow = 'none';
				});
			});
		}

	function typeHeroTitle() {
		const heroTitle = document.querySelector('.hero-section h1');
		console.log('Hero title element:', heroTitle);
		
		if (heroTitle) {
			const text = heroTitle.textContent;
			console.log('Hero title text:', text);
			
			heroTitle.textContent = '';
			heroTitle.style.borderRight = '3px solid #4f46e5';
			heroTitle.style.animation = 'blink 1s infinite';
			
			let i = 0;
			const timer = setInterval(() => {
				if (i < text.length) {
					heroTitle.textContent += text.charAt(i);
					i++;
				} else {
					clearInterval(timer);
					heroTitle.style.borderRight = 'none';
					heroTitle.style.animation = 'none';
					
					// Type subtitle after title
					setTimeout(() => {
						typeSubtitle();
					}, 500);
				}
			}, 80);
		} else {
			console.log('Hero title not found!');
		}
	}
	
	function typeSubtitle() {
		const subtitle = document.querySelector('.hero-section .lead');
		if (subtitle) {
			const text = subtitle.textContent;
			subtitle.textContent = '';
			
			let i = 0;
			const timer = setInterval(() => {
				if (i < text.length) {
					subtitle.textContent += text.charAt(i);
					i++;
				} else {
					clearInterval(timer);
				}
			}, 50);
		}
	}

	function animateFeatureCards() {
		const cards = document.querySelectorAll('.feature-card');
		console.log('Found feature cards:', cards.length);
		
		cards.forEach((card, index) => {
			card.style.opacity = '0';
			card.style.transform = 'translateY(30px)';
			
			setTimeout(() => {
				card.style.transition = 'all 0.6s ease';
				card.style.opacity = '1';
				card.style.transform = 'translateY(0)';
				
				// Type the title
				const title = card.querySelector('h5');
				if (title) {
					const text = title.textContent;
					title.textContent = '';
					
					setTimeout(() => {
						let i = 0;
						const timer = setInterval(() => {
							if (i < text.length) {
								title.textContent += text.charAt(i);
								i++;
							} else {
								clearInterval(timer);
							}
						}, 60);
					}, 300);
				}
			}, 2000 + (index * 400));
		});
	}

	function animateNumbers() {
		const numbers = document.querySelectorAll('.stat-number');
		console.log('Found numbers:', numbers.length);
		
		numbers.forEach((number, index) => {
			const target = parseInt(number.textContent.replace(/[^\d]/g, ''));
			const suffix = number.textContent.replace(/[\d]/g, '');
			number.textContent = '0' + suffix;
			
			setTimeout(() => {
				let current = 0;
				const increment = target / 50;
				const timer = setInterval(() => {
					current += increment;
					if (current >= target) {
						number.textContent = target + suffix;
						clearInterval(timer);
					} else {
						number.textContent = Math.floor(current) + suffix;
					}
				}, 30);
			}, 1500 + (index * 300));
		});
	}

	// Simple search bar animation
	function animateSearchBar() {
		const searchBar = document.querySelector('.smart-search');
		if (searchBar) {
			searchBar.style.opacity = '0';
			searchBar.style.transform = 'translateY(20px)';
			
			setTimeout(() => {
				searchBar.style.transition = 'all 0.8s ease';
				searchBar.style.opacity = '1';
				searchBar.style.transform = 'translateY(0)';
			}, 2000);
		}
	}

	// ADVANCED CSS ANIMATIONS
	const advancedStyles = document.createElement('style');
	advancedStyles.textContent = `
		.animate-in {
			animation: slideInUp 0.8s ease forwards;
		}
		
		@keyframes slideInUp {
			from {
				opacity: 0;
				transform: translateY(50px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		.feature-card, .stat-item {
			opacity: 0;
			transform: translateY(30px);
			transition: all 0.6s ease;
		}
		
		@keyframes ripple {
			to {
				transform: scale(4);
				opacity: 0;
			}
		}
		
		@keyframes blink {
			0%, 50% { border-color: transparent; }
			51%, 100% { border-color: #4f46e5; }
		}
		
		/* TYPING ANIMATION STYLES */
		.typing-cursor {
			border-right: 3px solid #4f46e5;
			animation: blink 1s infinite;
		}
		
		.typing-text {
			overflow: hidden;
			white-space: nowrap;
		}
		
		@keyframes typewriter {
			from { width: 0; }
			to { width: 100%; }
		}
		
		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		@keyframes slideInLeft {
			from {
				opacity: 0;
				transform: translateX(-50px);
			}
			to {
				opacity: 1;
				transform: translateX(0);
			}
		}
		
		@keyframes slideInRight {
			from {
				opacity: 0;
				transform: translateX(50px);
			}
			to {
				opacity: 1;
				transform: translateX(0);
			}
		}
		
		@keyframes scaleIn {
			from {
				opacity: 0;
				transform: scale(0.8);
			}
			to {
				opacity: 1;
				transform: scale(1);
			}
		}
		
		/* LOADING STATES FOR TYPING */
		.loading-text {
			background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
			background-size: 200% 100%;
			animation: shimmer 1.5s infinite;
			border-radius: 4px;
			height: 1.2em;
			display: inline-block;
		}
		
		@keyframes shimmer {
			0% { background-position: -200% 0; }
			100% { background-position: 200% 0; }
		}
	`;
	document.head.appendChild(advancedStyles);
		
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

