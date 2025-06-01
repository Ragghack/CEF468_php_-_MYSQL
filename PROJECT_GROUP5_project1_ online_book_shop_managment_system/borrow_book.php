<?php
session_start();
require_once 'db_connect.php';

// Check if the customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Get customer ID and book ID
$customer_id = $_SESSION['customer_id'];
$book_id = $_POST['book_id'] ?? null;

// Check if book_id is valid
if (!$book_id) {
    die("Error: No book selected for borrowing.");
}

// Check if customer exists
$check_customer = $conn->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
$check_customer->bind_param("i", $customer_id);
$check_customer->execute();
$customer_result = $check_customer->get_result();

if ($customer_result->num_rows === 0) {
    die("Error: Customer ID {$customer_id} does not exist in the customers table.");
}

// Fetch book details including return_delay and stock
$book_stmt = $conn->prepare("SELECT stock, return_delay FROM books WHERE book_id = ?");
$book_stmt->bind_param("i", $book_id);
$book_stmt->execute();
$book_result = $book_stmt->get_result();

if ($book_result->num_rows === 0) {
    die("Error: Book not found.");
}

$book = $book_result->fetch_assoc();
$stock = $book['stock'];
$return_delay = $book['return_delay'];

// Check if book is in stock
if ($stock <= 0) {
    die("Error: Book is out of stock.");
}

// Calculate due date
$borrow_date = date("Y-m-d");
$due_date = date("Y-m-d", strtotime("+$return_delay days"));

// Insert borrowing record
$borrow_stmt = $conn->prepare("
    INSERT INTO borrowings (customer_id, book_id, borrow_date, due_date, returned)
    VALUES (?, ?, ?, ?, 0)
");
$borrow_stmt->bind_param("iiss", $customer_id, $book_id, $borrow_date, $due_date);

if ($borrow_stmt->execute()) {
    // Reduce stock by 1
    $update_stock_stmt = $conn->prepare("UPDATE books SET stock = stock - 1 WHERE book_id = ?");
    $update_stock_stmt->bind_param("i", $book_id);
    $update_stock_stmt->execute();

    header("Location: customer_dashboard.php?msg=Book borrowed successfully");
    exit();
} else {
    die("Error borrowing book: " . $conn->error);
}
?>
