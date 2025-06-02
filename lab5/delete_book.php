<?php
require 'auth_check.php';
auth_check();
require 'auth_db.php';

if (!isset($_GET['id'])) {
    header("Location: view_books.php");
    exit();
}

$bookId = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->execute([$bookId]);
} catch (PDOException $e) {
    // Handle error if needed
}

header("Location: view_books.php");
exit();
?>