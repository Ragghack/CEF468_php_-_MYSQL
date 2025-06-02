<?php
require 'auth_check.php';
auth_check();
require 'auth_db.php';

$stmt = $pdo->query("SELECT * FROM books");
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Books</title>
</head>
<body>
    <h1>Book List</h1>
    <a href="add_book.php">Add New Book</a>
    
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Price</th>
            <th>Genre</th>
            <th>Year</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($books as $book): ?>
        <tr>
            <td><?= htmlspecialchars($book['title']) ?></td>
            <td><?= htmlspecialchars($book['author']) ?></td>
            <td>$<?= number_format($book['price'], 2) ?></td>
            <td><?= htmlspecialchars($book['genre']) ?></td>
            <td><?= htmlspecialchars($book['year']) ?></td>
            <td>
                <a href="edit_book.php?id=<?= $book['book_id'] ?>">Edit</a>
                <a href="delete_book.php?id=<?= $book['book_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <a href="home.php">Back to Home</a>
</body>
</html>