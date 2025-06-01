<?php
session_start();

$book_id = intval($_POST['book_id'] ?? 0);
if ($book_id && isset($_SESSION['cart'][$book_id])) {
    unset($_SESSION['cart'][$book_id]);
}
header("Location: cart.php");
exit;
