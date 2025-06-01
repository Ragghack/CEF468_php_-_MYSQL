<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$book_id = $_POST['book_id'];
$payment_method = $_POST['payment_method'];
$price = $_POST['price'];

$date = date("Y-m-d H:i:s");

// Simulated book purchase entry (you can also reduce stock, update sales, etc.)
$stmt = $conn->prepare("INSERT INTO book_purchases (customer_id, book_id, payment_method, amount, purchase_date) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisds", $customer_id, $book_id, $payment_method, $price, $date);

if ($stmt->execute()) {
    echo "<h3>Thank you! Your purchase was successful.</h3>";
} else {
    echo "<h3>Error: " . $stmt->error . "</h3>";
}
?>
