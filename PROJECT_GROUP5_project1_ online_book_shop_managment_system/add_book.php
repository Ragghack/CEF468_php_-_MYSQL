<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require_once 'db_connect.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $return_delay = intval($_POST['return_delay']);

    // Upload image
    $image_path = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES['image']['name']);
        $image_path = "uploads/images/" . $image_name;
        if (!is_dir('uploads/images')) {
    mkdir('uploads/images', 0777, true);
}
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Upload ebook
    $ebook_path = "";
    if (!empty($_FILES['ebook']['name'])) {
        $ebook_name = basename($_FILES['ebook']['name']);
        $ebook_path = "uploads/ebooks/" . $ebook_name;
        if (!is_dir('uploads/ebooks')) {
    mkdir('uploads/ebooks', 0777, true);
}
        move_uploaded_file($_FILES['ebook']['tmp_name'], $ebook_path);
    }

    $stmt = $conn->prepare("INSERT INTO books (title, author, price, stock, return_delay, image_path, ebook_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiiss", $title, $author, $price, $stock, $return_delay, $image_path, $ebook_path);

    if ($stmt->execute()) {
        $msg = "Book added successfully!";
    } else {
        $msg = "Failed to add book!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add New Book</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Author:</label>
            <input type="text" name="author" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Price:</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stock:</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Return Delay (in days):</label>
            <input type="number" name="return_delay" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Book Cover Image (optional):</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label>Ebook File (optional):</label>
            <input type="file" name="ebook" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Book</button>
        <a href="manage_books.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
