<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

$book_id = intval($_POST['book_id'] ?? 0);
if ($book_id <= 0) {
    header("Location: customer_dashboard.php?error=invalid");
    exit;
}

// Initialize cart if needed
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Increase quantity or set to 1
if (isset($_SESSION['cart'][$book_id])) {
    $_SESSION['cart'][$book_id]++;
} else {
    $_SESSION['cart'][$book_id] = 1;
}

header("Location: cart.php");
exit;
