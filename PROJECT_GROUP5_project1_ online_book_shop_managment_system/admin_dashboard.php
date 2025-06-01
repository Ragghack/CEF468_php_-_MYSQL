<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Get admin username from session
$admin_username = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Portal - Digital Bibliotheca</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #1a252f;
            --accent-color: #3498db;
            --light-color: #f8f9fa;
            --highlight-color: #f1c40f;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: var(--primary-color);
            background-image: url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(248, 249, 250, 0.92);
            z-index: -1;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            color: var(--secondary-color);
        }
        
        .navbar {
            background-color: var(--secondary-color) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 1px;
        }
        
        .nav-text {
            font-size: 1.1rem;
        }
        
        .admin-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        
        .welcome-header {
            position: relative;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
        }
        
        .welcome-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-color), var(--highlight-color));
        }
        
        .admin-card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            border-left: 4px solid var(--accent-color);
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .card-icon {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--secondary-color);
        }
        
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            border-top: 4px solid var(--highlight-color);
        }
        
        .stats-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--secondary-color);
            line-height: 1;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: start;
            padding: 1.25rem;
            border-radius: 8px;
            background: white;
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--accent-color);
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            color: var(--accent-color);
        }
        
        .action-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: var(--accent-color);
        }
        
        .recent-activity {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
        }
        
        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .admin-footer {
            background-color: var(--secondary-color);
            color: white;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }
        
        .footer-text {
            font-family: 'Playfair Display', serif;
            text-align: center;
            margin-bottom: 0;
        }
        
        .btn-admin {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }
        
        .btn-admin:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            color: white;
        }
        
        .badge-admin {
            background-color: var(--accent-color);
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-book-reader me-2"></i>Digital Bibliotheca
        </a>
        <div class="d-flex align-items-center">
            <span class="navbar-text text-white me-4">
                <i class="fas fa-user-shield me-2"></i><?= htmlspecialchars($admin_username) ?>
            </span>
            <a href="admin_logout.php" class="btn btn-outline-light">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container admin-container">
    <div class="welcome-header">
        <h1><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h1>
        <p class="lead">Welcome back, <?= htmlspecialchars($admin_username) ?>! Here's what's happening today.</p>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-value">1,248</div>
                <div class="stats-label">Total Books</div>
                <div class="mt-2">
                    <span class="badge badge-admin p-2">
                        <i class="fas fa-arrow-up me-1"></i> 12% from last month
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-value">568</div>
                <div class="stats-label">Active Customers</div>
                <div class="mt-2">
                    <span class="badge badge-admin p-2">
                        <i class="fas fa-arrow-up me-1"></i> 5% from last week
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-value">42</div>
                <div class="stats-label">New Orders</div>
                <div class="mt-2">
                    <span class="badge bg-success p-2">
                        <i class="fas fa-bell me-1"></i> 3 require attention
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-value">$8,742</div>
                <div class="stats-label">Monthly Revenue</div>
                <div class="mt-2">
                    <span class="badge badge-admin p-2">
                        <i class="fas fa-arrow-up me-1"></i> 18% from last month
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="quick-actions">
        <a href="manage_books.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-book"></i>
            </div>
            <div>
                <h5 class="card-title mb-1">Manage Books</h5>
                <p class="mb-0 text-muted">Add, edit or remove books</p>
            </div>
        </a>
        
        <a href="manage_customers.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h5 class="card-title mb-1">Manage Customers</h5>
                <p class="mb-0 text-muted">View and manage users</p>
            </div>
        </a>
        
        <a href="view_orders.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
                <h5 class="card-title mb-1">View Orders</h5>
                <p class="mb-0 text-muted">Process and track orders</p>
            </div>
        </a>
        
        <a href="inventory.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <h5 class="card-title mb-1">Inventory</h5>
                <p class="mb-0 text-muted">Manage stock levels</p>
            </div>
        </a>
        
        <a href="reports.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>
                <h5 class="card-title mb-1">Reports</h5>
                <p class="mb-0 text-muted">Generate sales reports</p>
            </div>
        </a>
        
        <a href="promotions.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-tag"></i>
            </div>
            <div>
                <h5 class="card-title mb-1">Promotions</h5>
                <p class="mb-0 text-muted">Create special offers</p>
            </div>
        </a>
    </div>

    <div class="recent-activity">
        <h4><i class="fas fa-clock me-2"></i>Recent Activity</h4>
        <div class="activity-item">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>New order #1042</strong> - The Great Gatsby
                </div>
                <div class="activity-time">
                    15 minutes ago
                </div>
            </div>
        </div>
        <div class="activity-item">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>Customer registered</strong> - johndoe@example.com
                </div>
                <div class="activity-time">
                    1 hour ago
                </div>
            </div>
        </div>
        <div class="activity-item">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>Book added</strong> - To Kill a Mockingbird
                </div>
                <div class="activity-time">
                    3 hours ago
                </div>
            </div>
        </div>
        <div class="activity-item">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>Inventory updated</strong> - 1984 stock increased
                </div>
                <div class="activity-time">
                    5 hours ago
                </div>
            </div>
        </div>
        <div class="activity-item">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>Promotion created</strong> - Summer Sale 20% off
                </div>
                <div class="activity-time">
                    1 day ago
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="admin-footer">
    <div class="container">
        <p class="footer-text">
            <i class="fas fa-book-open me-2"></i>Digital Bibliotheca Admin Portal &copy; <?= date('Y') ?>
        </p>
        <div class="text-center text-white mt-2">
            <small>Powered by the finest digital literature</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Any admin-specific JavaScript can go here
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Admin dashboard loaded');
    });
</script>
</body>
</html>