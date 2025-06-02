<?php
require 'auth_check.php';
auth_check();
require 'auth_db.php';

if (!isset($_GET['id'])) {
    header("Location: view_books.php");
    exit();
}

$bookId = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = trim($_POST['price']);
    $genre = trim($_POST['genre']);
    $year = trim($_POST['year']);
    
    try {
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, price=?, genre=?, year=? WHERE book_id=?");
        $stmt->execute([$title, $author, $price, $genre, $year, $bookId]);
        
        header("Location: view_books.php");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to update book: " . $e->getMessage();
    }
}

$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    header("Location: view_books.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
</head>
<body>
    <h1>Edit Book</h1>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required><br>
        <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required><br>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($book['price']) ?>" required><br>
        <input type="text" name="genre" value="<?= htmlspecialchars($book['genre']) ?>" required><br>
        <input type="number" name="year" value="<?= htmlspecialchars($book['year']) ?>" required><br>
        <button type="submit">Update Book</button>
    </form>
    <a href="view_books.php">Back to Books</a>
</body>
</html>