<?php
session_start();
require_once 'db_connect.php';

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Get customer details
$customer_stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$customer_stmt->bind_param("i", $customer_id);
$customer_stmt->execute();
$customer = $customer_stmt->get_result()->fetch_assoc();

// Get customer addresses
$address_stmt = $conn->prepare("SELECT * FROM customer_addresses WHERE customer_id = ?");
$address_stmt->bind_param("i", $customer_id);
$address_stmt->execute();
$addresses = $address_stmt->get_result();

// Get order history
$orders_stmt = $conn->prepare("
    SELECT o.order_id, o.order_date, o.total_amount, o.status, 
           COUNT(oi.item_id) as item_count
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.customer_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
    LIMIT 5
");
$orders_stmt->bind_param("i", $customer_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();

// Get wishlist items
$wishlist_stmt = $conn->prepare("
    SELECT b.book_id, b.title, b.author, b.price, b.image_path
    FROM wishlist w
    JOIN books b ON w.book_id = b.book_id
    WHERE w.customer_id = ?
    LIMIT 4
");
$wishlist_stmt->bind_param("i", $customer_id);
$wishlist_stmt->execute();
$wishlist = $wishlist_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile - Online Bookshop</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3a3a3a;
            --secondary-color: #5c3c21;
            --accent-color: #8b5a2b;
            --light-color: #f5f1e8;
            --highlight-color: #d4a76a;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--light-color);
            color: var(--primary-color);
            background-image: url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
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
            background-color: rgba(245, 241, 232, 0.9);
            z-index: -1;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            color: var(--secondary-color);
        }
        
        .profile-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        
        .profile-header {
            position: relative;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .profile-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100px;
            height: 2px;
            background-color: var(--accent-color);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .profile-card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .profile-card .card-header {
            background-color: var(--secondary-color);
            color: white;
            font-family: 'Playfair Display', serif;
            border-bottom: none;
        }
        
        .address-card {
            border-left: 4px solid var(--highlight-color);
        }
        
        .order-card {
            border-left: 4px solid var(--accent-color);
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            transform: translateX(5px);
        }
        
        .wishlist-item {
            transition: all 0.3s ease;
        }
        
        .wishlist-item:hover {
            transform: scale(1.03);
        }
        
        .status-pending {
            color: #d4a76a;
            font-weight: 500;
        }
        
        .status-completed {
            color: #5a8b46;
            font-weight: 500;
        }
        
        .status-cancelled {
            color: #b33a3a;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link {
            color: var(--secondary-color);
            font-family: 'Playfair Display', serif;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--accent-color);
            font-weight: bold;
            border-bottom: 2px solid var(--accent-color);
        }
        
        .btn-edit {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #7a4b24;
            border-color: #7a4b24;
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="customer_dashboard.php">
            <i class="fas fa-book-open me-2"></i>Online Bookshop
        </a>
        <div class="d-flex align-items-center">
            <a href="customer_dashboard.php" class="btn btn-outline-light btn-sm me-2">
                <i class="fas fa-home me-1"></i> Dashboard
            </a>
            <a href="customer_logout.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container profile-container">
    <div class="profile-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-user-circle me-2"></i>My Profile</h1>
            <p class="lead mb-0">Manage your account information and preferences</p>
        </div>
        <div class="text-end">
            <img src="<?= !empty($customer['profile_image']) ? htmlspecialchars($customer['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($customer['first_name'].'+'.$customer['last_name']).'&background='.substr($customer['customer_id']*123456, 0, 6) ?>" 
                 alt="Profile Image" class="profile-avatar">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Personal Information Card -->
            <div class="card profile-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Full Name</h6>
                        <p><?= htmlspecialchars($customer['first_name'] . ' ' . htmlspecialchars($customer['last_name']))?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Email</h6>
                        <p><?= htmlspecialchars($customer['email']) ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Phone</h6>
                        <p><?= !empty($customer['phone']) ? htmlspecialchars($customer['phone']) : 'Not provided' ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Member Since</h6>
                        <p><?= date('F j, Y', strtotime($customer['created_at'])) ?></p>
                    </div>
                    <a href="edit_profile.php" class="btn btn-edit btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
                </div>
            </div>

            <!-- Addresses Card -->
            <div class="card profile-card address-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Saved Addresses</h5>
                </div>
                <div class="card-body">
                    <?php if ($addresses->num_rows > 0): ?>
                        <?php while ($address = $addresses->fetch_assoc()): ?>
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($address['address_name']) ?>
                                    <a href="edit_address.php?id=<?= $address['address_id'] ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </h6>
                                <p class="mb-1"><?= htmlspecialchars($address['street_address']) ?></p>
                                <p class="mb-1"><?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state']) ?> <?= htmlspecialchars($address['postal_code']) ?></p>
                                <p class="mb-0"><?= htmlspecialchars($address['country']) ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">No saved addresses found.</p>
                    <?php endif; ?>
                    <a href="add_address.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New Address
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                        <i class="fas fa-shopping-bag me-1"></i> Recent Orders
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="wishlist-tab" data-bs-toggle="tab" data-bs-target="#wishlist" type="button" role="tab">
                        <i class="fas fa-heart me-1"></i> Wishlist
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">
                        <i class="fas fa-cog me-1"></i> Account Settings
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="profileTabsContent">
                <div class="tab-pane fade show active" id="orders" role="tabpanel">
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <div class="card order-card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title mb-1">Order #<?= $order['order_id'] ?></h5>
                                            <p class="card-text mb-1">
                                                <small class="text-muted"><?= date('F j, Y', strtotime($order['order_date'])) ?></small>
                                            </p>
                                            <p class="card-text mb-1">
                                                <?= $order['item_count'] ?> item<?= $order['item_count'] > 1 ? 's' : '' ?> • 
                                                $<?= number_format($order['total_amount'], 2) ?>
                                            </p>
                                        </div>
                                        <div>
                                            <span class="badge 
                                                <?= $order['status'] == 'completed' ? 'bg-success' : 
                                                   ($order['status'] == 'cancelled' ? 'bg-danger' : 'bg-warning') ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-between">
                                        <a href="order_details.php?id=<?= $order['order_id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </a>
                                        <?php if ($order['status'] == 'pending'): ?>
                                            <a href="#" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-times me-1"></i> Cancel Order
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <div class="text-end mt-3">
                            <a href="order_history.php" class="btn btn-outline-secondary">
                                <i class="fas fa-history me-1"></i> View Full Order History
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> You haven't placed any orders yet.
                        </div>
                        <a href="customer_dashboard.php" class="btn btn-primary">
                            <i class="fas fa-book-open me-1"></i> Browse Books
                        </a>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="wishlist" role="tabpanel">
                    <?php if ($wishlist->num_rows > 0): ?>
                        <div class="row">
                            <?php while ($item = $wishlist->fetch_assoc()): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card wishlist-item h-100">
                                        <div class="row g-0">
                                            <div class="col-md-4">
                                                <?php if (!empty($item['image_path'])): ?>
                                                    <img src="<?= htmlspecialchars($item['image_path']) ?>" class="img-fluid rounded-start" alt="Book Cover" style="height: 150px; width: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 150px;">
                                                        <i class="fas fa-book-open fa-3x text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
                                                    <p class="card-text"><?= htmlspecialchars($item['author']) ?></p>
                                                    <p class="card-text"><strong>$<?= number_format($item['price'], 2) ?></strong></p>
                                                    <div class="d-flex justify-content-between">
                                                        <form method="POST" action="add_to_cart.php" class="d-inline">
                                                            <input type="hidden" name="book_id" value="<?= $item['book_id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="remove_from_wishlist.php" class="d-inline">
                                                            <input type="hidden" name="book_id" value="<?= $item['book_id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash me-1"></i> Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="text-end mt-3">
                            <a href="wishlist.php" class="btn btn-outline-secondary">
                                <i class="fas fa-heart me-1"></i> View Full Wishlist
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Your wishlist is empty.
                        </div>
                        <a href="customer_dashboard.php" class="btn btn-primary">
                            <i class="fas fa-book-open me-1"></i> Browse Books
                        </a>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="settings" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><i class="fas fa-lock me-2"></i>Security Settings</h5>
                            
                            <form action="change_password.php" method="POST" class="mb-4">
                                <div class="mb-3">
                                    <label for="currentPassword" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-1"></i> Change Password
                                </button>
                            </form>
                            
                            <hr>
                            
                            <h5 class="card-title mb-4"><i class="fas fa-bell me-2"></i>Notification Preferences</h5>
                            <form action="update_notifications.php" method="POST">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" checked>
                                    <label class="form-check-label" for="emailNotifications">Email Notifications</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="promotionalEmails" name="promotional_emails" checked>
                                    <label class="form-check-label" for="promotionalEmails">Promotional Emails</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="orderUpdates" name="order_updates" checked>
                                    <label class="form-check-label" for="orderUpdates">Order Updates</label>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Preferences
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="fas fa-book-open me-2"></i>Online Bookshop</h5>
                <p class="mb-0">Your literary journey begins here.</p>
            </div>
            <div class="col-md-6 text-end">
                <p class="mb-0">&copy; <?= date('Y') ?> Online Bookshop. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
</body>
</html>