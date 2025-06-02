<?php
require 'auth_check.php';
auth_check();
require 'auth_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = trim($_POST['price']);
    $genre = trim($_POST['genre']);
    $year = trim($_POST['year']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, price, genre, year) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $price, $genre, $year]);
        
        header("Location: view_books.php");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to add book: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
</head>
<body>
    <h1>Add New Book</h1>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="title" placeholder="Title" required><br>
        <input type="text" name="author" placeholder="Author" required><br>
        <input type="number" step="0.01" name="price" placeholder="Price" required><br>
        <input type="text" name="genre" placeholder="Genre" required><br>
        <input type="number" name="year" placeholder="Year" required><br>
        <button type="submit">Add Book</button>
    </form>
    <a href="view_books.php">Back to Books</a>
</body>
</html>