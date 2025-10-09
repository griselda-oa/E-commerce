<?php
require_once '../settings/core.php';

// Require admin access - redirect if not admin
if (!is_logged_in()) {
    header('Location: ../login/login.php?error=login_required');
    exit();
}

if (!is_admin()) {
    header('Location: ../index.php?error=admin_required');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin Dashboard - E-Commerce Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .admin-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 30px;
            min-height: calc(100vh - 40px);
        }
        
        .admin-header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .admin-header h1 {
            margin: 0;
            font-size: 3rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        .admin-header p {
            margin: 15px 0 0 0;
            opacity: 0.9;
            font-size: 1.2rem;
            position: relative;
            z-index: 1;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }
        
        .stat-icon.primary { background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); }
        .stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6b7280;
            font-weight: 500;
            font-size: 1rem;
        }
        
        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .action-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }
        
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            margin-bottom: 15px;
        }
        
        .action-icon.primary { background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); }
        .action-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .action-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .action-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .action-icon.info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
        
        .action-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .action-description {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .recent-activity {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            margin-right: 15px;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 2px;
        }
        
        .activity-time {
            color: #6b7280;
            font-size: 0.85rem;
        }
        
        .back-to-home {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .quick-stat {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        
        .quick-stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .quick-stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                margin: 10px;
                padding: 20px;
            }
            
            .admin-header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Back to Home Button -->
    <a href="../index.php" class="btn btn-light back-to-home">
        <i class="fa fa-arrow-left"></i> Back to Home
    </a>

    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1><i class="fa fa-crown"></i> Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars(get_user_first_name()); ?>! Manage your e-commerce platform</p>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="quick-stat">
                <div class="quick-stat-number" id="total-categories">0</div>
                <div class="quick-stat-label">Categories</div>
            </div>
            <div class="quick-stat">
                <div class="quick-stat-number" id="total-products">0</div>
                <div class="quick-stat-label">Products</div>
            </div>
            <div class="quick-stat">
                <div class="quick-stat-number" id="total-customers">0</div>
                <div class="quick-stat-label">Customers</div>
            </div>
            <div class="quick-stat">
                <div class="quick-stat-number" id="total-orders">0</div>
                <div class="quick-stat-label">Orders</div>
            </div>
        </div>

        <!-- Main Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fa fa-tags"></i>
                </div>
                <div class="stat-number" id="category-count">0</div>
                <div class="stat-label">Total Categories</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fa fa-shopping-bag"></i>
                </div>
                <div class="stat-number" id="product-count">0</div>
                <div class="stat-label">Total Products</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-number" id="customer-count">0</div>
                <div class="stat-label">Total Customers</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="stat-number" id="order-count">0</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="admin-actions">
            <a href="category_management.php" class="action-card">
                <div class="action-icon primary">
                    <i class="fa fa-tags"></i>
                </div>
                <div class="action-title">Manage Categories</div>
                <div class="action-description">Create, edit, and delete product categories. Organize your inventory effectively.</div>
            </a>
            
            <a href="product_management.php" class="action-card">
                <div class="action-icon success">
                    <i class="fa fa-shopping-bag"></i>
                </div>
                <div class="action-title">Manage Products</div>
                <div class="action-description">Add, edit, and delete products. Manage your inventory and pricing.</div>
            </a>
            
            <a href="#" class="action-card">
                <div class="action-icon success">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="action-title">Add Products</div>
                <div class="action-description">Add new products to your inventory with detailed descriptions and pricing.</div>
            </a>
            
            <a href="#" class="action-card">
                <div class="action-icon warning">
                    <i class="fa fa-edit"></i>
                </div>
                <div class="action-title">Edit Products</div>
                <div class="action-description">Update existing product information, prices, and availability.</div>
            </a>
            
            <a href="#" class="action-card">
                <div class="action-icon danger">
                    <i class="fa fa-trash"></i>
                </div>
                <div class="action-title">Delete Products</div>
                <div class="action-description">Remove products from your inventory permanently.</div>
            </a>
            
            <a href="#" class="action-card">
                <div class="action-icon info">
                    <i class="fa fa-chart-bar"></i>
                </div>
                <div class="action-title">Analytics</div>
                <div class="action-description">View detailed analytics and reports for your e-commerce platform.</div>
            </a>
            
            <a href="#" class="action-card">
                <div class="action-icon primary">
                    <i class="fa fa-users"></i>
                </div>
                <div class="action-title">Manage Users</div>
                <div class="action-description">View and manage customer accounts and admin privileges.</div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h3 class="mb-4"><i class="fa fa-clock"></i> Recent Activity</h3>
            
            <div class="activity-item">
                <div class="activity-icon primary">
                    <i class="fa fa-tags"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">Category Management</div>
                    <div class="activity-time">Manage your product categories</div>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon success">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">Product Management</div>
                    <div class="activity-time">Add, edit, or delete products</div>
                </div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon warning">
                    <i class="fa fa-chart-bar"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">Analytics Dashboard</div>
                    <div class="activity-time">View platform statistics and reports</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load dashboard data
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });

        function loadDashboardData() {
            // Load categories count
            fetch('../actions/fetch_category_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('total-categories').textContent = data.count;
                        document.getElementById('category-count').textContent = data.count;
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                });

            // Load other stats (placeholder for now)
            document.getElementById('total-products').textContent = '0';
            document.getElementById('total-customers').textContent = '0';
            document.getElementById('total-orders').textContent = '0';
            document.getElementById('product-count').textContent = '0';
            document.getElementById('customer-count').textContent = '0';
            document.getElementById('order-count').textContent = '0';
        }
    </script>
</body>
</html>
