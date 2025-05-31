<?php
require_once 'Database.php';
require_once 'Book.php';

$conn = Database::connect();

$stmt = $conn->prepare("INSERT INTO Books (title, author, price, genre, year) VALUES (?, ?, ?, ?, ?)");
$title = "Brave New World";
$author = "Aldous Huxley";
$price = 14.99;
$genre = "Dystopian";
$year = 1932;

$title = "Brave New World";
$author = "Aldous Huxley";
$price = 14.99;
$genre = "Dystopian";
$year = 1932;

$stmt->bind_param("ssdsi", $title, $author, $price, $genre, $year);
$stmt->execute();

$title = "Brave New World";
$author = "Aldous Huxley";
$price = 14.99;
$genre = "Dystopian";
$year = 1932;

$stmt->bind_param("ssdsi", $title, $author, $price, $genre, $year);
$stmt->execute();


echo "Book inserted with ID: " . $stmt->insert_id;

$stmt->close();
Database::disconnect();
?>
