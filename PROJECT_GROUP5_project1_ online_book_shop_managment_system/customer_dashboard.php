<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_username = isset($_SESSION['customer_username']) ? $_SESSION['customer_username'] : 'Guest';

// Database queries remain the same as original
$books_stmt = $conn->query("SELECT * FROM books");
$ebooks_stmt = $conn->query("SELECT * FROM ebook_views");

$purchase_stmt = $conn->prepare("
    SELECT ev.title, ev.author, p.purchase_date
    FROM purchases p
    JOIN ebook_views ev ON p.ebook_id = ev.ebook_id
    WHERE p.customer_id = ?
    ORDER BY p.purchase_date DESC
");
$purchase_stmt->bind_param("i", $customer_id);
$purchase_stmt->execute();
$purchases_result = $purchase_stmt->get_result();

$borrow_stmt = $conn->prepare("
    SELECT b.title, br.borrow_date, br.due_date, br.returned
    FROM borrowings br
    JOIN books b ON br.book_id = b.book_id
    WHERE br.customer_id = ?
    ORDER BY br.borrow_date DESC
");
$borrow_stmt->bind_param("i", $customer_id);
$borrow_stmt->execute();
$borrows_result = $borrow_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Digital Bibliotheca - Customer Dashboard</title>
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
            background-color: rgba(245, 241, 232, 0.85);
            z-index: -1;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            color: var(--secondary-color);
        }
        
        .navbar {
            background-color: var(--secondary-color) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
        
        .container {
            background-color: rgba(255, 255, 255, 0.92);
            border-radius: 8px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--highlight-color);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100px;
            height: 2px;
            background-color: var(--accent-color);
        }
        
        .card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            background-color: white;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .card-img-top {
            object-fit: cover;
            height: 300px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.75rem;
        }
        
        .card-text {
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-success:hover {
            background-color: #7a4b24;
            border-color: #7a4b24;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #4a2f1a;
            border-color: #4a2f1a;
        }
        
        .table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: var(--secondary-color);
            color: white;
            font-family: 'Playfair Display', serif;
            border: none;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(139, 90, 43, 0.05);
        }
        
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
        
        .badge.bg-success {
            background-color: var(--accent-color) !important;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 0;
        }
        
        .status-pending {
            color: #d4a76a;
            font-weight: 500;
        }
        
        .status-returned {
            color: #5a8b46;
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        footer {
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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="fas fa-book-open me-2"></i>Digital Bibliotheca
        </a>
        <span class="navbar-text text-white">
            <i class="fas fa-user-circle me-2"></i>Welcome, <?= htmlspecialchars($customer_username) ?>
        </span>
        <a href="customer_logout.php" class="btn btn-outline-light ms-3">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </div>
</nav>

<div class="container">
    <div class="welcome-banner">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="welcome-text">
                <i class="fas fa-book-reader me-2"></i>Your Personal Library
            </h2>
            <div>
                <a href="#" class="btn btn-light me-2">
                    <i class="fas fa-shopping-cart me-1"></i> View Cart
                </a>
                <a href="#" class="btn btn-light">
                    <i class="fas fa-user-edit me-1"></i> Profile
                </a>
            </div>
        </div>
    </div>

    <h3 class="section-title">
        <i class="fas fa-book me-2"></i>Available Physical Books
    </h3>
    <div class="row g-4">
        <?php while ($book = $books_stmt->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <?php if (!empty($book['image_path'])): ?>
                        <img src="<?= htmlspecialchars($book['image_path']) ?>" class="card-img-top" alt="Book Cover">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 300px;">
                            <i class="fas fa-book-open fa-5x text-white"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                        <p class="card-text"><strong>Price:</strong> $<?= number_format($book['price'], 2) ?></p>
                        <p class="card-text">
                            <strong>Availability:</strong> 
                            <span class="badge <?= ($book['stock'] > 0) ? 'bg-success' : 'bg-secondary' ?>">
                                <?= ($book['stock'] > 0) ? 'In Stock' : 'Out of Stock' ?>
                            </span>
                        </p>
                        <div class="action-buttons mt-auto">
                            <form method="POST" action="purchase_book.php" class="d-inline">
                                <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                <input type="hidden" name="price" value="<?= $book['price'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-shopping-bag me-1"></i> Buy
                                </button>
                            </form>
                            <form method="POST" action="add_to_cart.php" class="d-inline">
                                <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                </button>
                            </form>
                            <form method="POST" action="borrow_book.php" class="d-inline">
                                <input type="hidden" name="book_id" value="<?= (int)$book['book_id'] ?>">
                                <button type="submit" class="btn btn-primary btn-sm" <?= ($book['stock'] < 1) ? 'disabled' : '' ?>>
                                    <i class="fas fa-hand-holding me-1"></i> Borrow
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <h3 class="section-title mt-5">
        <i class="fas fa-tablet-alt me-2"></i>Available eBooks
    </h3>
    <div class="row g-4">
        <?php while ($ebook = $ebooks_stmt->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <?php if (!empty($ebook['image_path'])): ?>
                        <img src="<?= htmlspecialchars($ebook['image_path']) ?>" class="card-img-top" alt="eBook Cover">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 300px;">
                            <i class="fas fa-tablet-alt fa-5x text-white"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($ebook['title']) ?></h5>
                        <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($ebook['author']) ?></p>
                        <p class="card-text"><strong>Price:</strong> $<?= number_format($ebook['price'], 2) ?></p>
                        <div class="action-buttons mt-auto">
                            <form method="POST" action="buy_ebook.php" class="d-inline">
                                <input type="hidden" name="ebook_id" value="<?= (int)$ebook['ebook_id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-cloud-download-alt me-1"></i> Buy eBook
                                </button>
                            </form>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#previewModal" data-id="<?= (int)$ebook['ebook_id'] ?>">
                                <i class="fas fa-eye me-1"></i> Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <h3 class="section-title mt-5">
        <i class="fas fa-receipt me-2"></i>Your Purchase History
    </h3>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>eBook Title</th>
                    <th>Author</th>
                    <th>Purchase Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($purchase = $purchases_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($purchase['title']) ?></td>
                        <td><?= htmlspecialchars($purchase['author']) ?></td>
                        <td><?= date('F j, Y', strtotime($purchase['purchase_date'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Download
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($purchases_result->num_rows === 0): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i>No purchase history found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h3 class="section-title mt-5">
        <i class="fas fa-exchange-alt me-2"></i>Your Borrowing History
    </h3>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($borrow = $borrows_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($borrow['title']) ?></td>
                        <td><?= date('F j, Y', strtotime($borrow['borrow_date'])) ?></td>
                        <td><?= date('F j, Y', strtotime($borrow['due_date'])) ?></td>
                        <td>
                            <span class="<?= $borrow['returned'] ? 'status-returned' : 'status-pending' ?>">
                                <i class="fas <?= $borrow['returned'] ? 'fa-check-circle' : 'fa-clock' ?> me-1"></i>
                                <?= ($borrow['returned'] ? 'Returned' : 'Pending') ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!$borrow['returned']): ?>
                                <button class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-undo me-1"></i> Return
                                </button>
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-calendar-plus me-1"></i> Extend
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-redo me-1"></i> Re-borrow
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($borrows_result->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i>No borrowing history found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">eBook Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-1x1">
                    <iframe src="" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Purchase Now</button>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        <p class="footer-text">
            <i class="fas fa-book-open me-2"></i>Digital Bibliotheca &copy; <?= date('Y') ?>
        </p>
        <div class="text-center text-white mt-2">
            <small>Your literary journey begins here</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview modal handler
    document.addEventListener('DOMContentLoaded', function() {
        var previewModal = document.getElementById('previewModal');
        if (previewModal) {
            previewModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var ebookId = button.getAttribute('data-id');
                var iframe = previewModal.querySelector('iframe');
                iframe.src = 'ebook_preview.php?id=' + ebookId;
            });
        }
    });
</script>
</body>
</html>