<?php

require_once 'Database.php';
require_once 'Book.php';
require_once 'Ebook.php';

$tempBooks = [];
session_start();

if (!isset($_SESSION['temp_ebooks'])) {
    $_SESSION['temp_ebooks'] = [];
}

if (isset($_POST['add_temp_book'])) {
    $id = count($_SESSION['temp_ebooks']) + 1000; // Arbitrary ID starting from 1000
    $ebook = new Ebook(
        $id,
        $_POST['temp_title'],
        $_POST['temp_author'],
        $_POST['temp_price'],
        $_POST['temp_genre'],
        $_POST['temp_filesize'],
        $_POST['temp_download']
    );
    $_SESSION['temp_ebooks'][] = $ebook;

    echo '<div class="alert alert-info">Temporary eBook added: <strong>' . htmlspecialchars($ebook->getTitle()) . '</strong> - Discounted Price: $' . $ebook->getDiscount() . '</div>';
}

$conn = Database::connect();

$message = '';

// Handle borrow
if (isset($_POST['borrow'])) {
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];

    $check = $conn->prepare("SELECT * FROM BookLoans WHERE book_id = ? AND return_date IS NULL");
    $check->bind_param("i", $book_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $message = '<div class="alert alert-danger">Book is already borrowed.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO BookLoans (book_id, member_id, loan_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $book_id, $member_id);
        $stmt->execute();
        $message = '<div class="alert alert-success">Book borrowed successfully.</div>';
    }
}

// Handle return
if (isset($_POST['return'])) {
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];

    $stmt = $conn->prepare("UPDATE BookLoans SET return_date = NOW() WHERE book_id = ? AND member_id = ? AND return_date IS NULL");
    $stmt->bind_param("ii", $book_id, $member_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = '<div class="alert alert-success">Book returned successfully.</div>';
    } else {
        $message = '<div class="alert alert-warning">No active loan found for this member and book.</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Test Interface</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">📚 Library Borrow/Return System</h2>
        </div>
        <hr>
        <h4>📚 Add New Book to Library (Stored in Database)</h4>
<form method="post" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" name="book_title" class="form-control" placeholder="Title" required>
    </div>
    <div class="col-md-2">
        <input type="text" name="book_author" class="form-control" placeholder="Author" required>
    </div>
    <div class="col-md-2">
        <input type="number" name="book_price" class="form-control" placeholder="Price" step="0.01" required>
    </div>
    <div class="col-md-2">
        <input type="text" name="book_genre" class="form-control" placeholder="Genre" required>
    </div>
    <div class="col-md-2">
        <input type="number" name="book_year" class="form-control" placeholder="Year" required>
    </div>
    <div class="col-md-1">
        <button type="submit" name="add_book" class="btn btn-primary w-100">Add</button>
    </div>
</form>

<h4>➕ Add Temporary eBook (No Database)</h4>
<form method="post" class="row g-3 mb-4">
    <div class="col-md-2">
        <input type="text" name="temp_title" class="form-control" placeholder="Title" required>
    </div>
    <div class="col-md-2">
        <input type="text" name="temp_author" class="form-control" placeholder="Author" required>
    </div>
    <div class="col-md-1">
        <input type="number" name="temp_price" class="form-control" placeholder="Price" step="0.01" required>
    </div>
    <div class="col-md-2">
        <input type="text" name="temp_genre" class="form-control" placeholder="Genre" required>
    </div>
    <div class="col-md-2">
        <input type="number" name="temp_filesize" class="form-control" placeholder="File Size (MB)" required>
    </div>
    <div class="col-md-2">
        <input type="url" name="temp_download" class="form-control" placeholder="Download URL" required>
    </div>
    <div class="col-md-1">
        <button type="submit" name="add_temp_book" class="btn btn-success w-100">Add</button>
    </div>
</form>


        <div class="card-body">
            <?= $message ?>

            <form method="post" class="mb-4">
                <div class="mb-3">
                    <label for="member_id" class="form-label">Member ID</label>
                    <input type="number" class="form-control" name="member_id" required>
                </div>

                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th><th>Author</th><th>Genre</th><th>Year</th><th>Status</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "
                            SELECT b.book_id, b.title, b.author, b.genre, b.year,
                            (SELECT COUNT(*) FROM BookLoans WHERE book_id = b.book_id AND return_date IS NULL) AS is_loaned
                            FROM Books b
                        ";
                        $result = $conn->query($query);

                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><?= htmlspecialchars($row['genre']) ?></td>
                            <td><?= $row['year'] ?></td>
                            <td>
                                <?= $row['is_loaned'] ? "<span class='badge bg-danger'>Borrowed</span>" : "<span class='badge bg-success'>Available</span>" ?>
                            </td>
                            <td>
                                <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                                <?php if (!$row['is_loaned']): ?>
                                    <button type="submit" name="borrow" class="btn btn-sm btn-primary">Borrow</button>
                                <?php else: ?>
                                    <button type="submit" name="return" class="btn btn-sm btn-warning">Return</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </form>

            <hr>
             <h4>📥 Temporary eBooks</h4>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php foreach ($_SESSION['temp_ebooks'] as $ebook): ?>
        <div class="col">
            <div class="card border-info shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($ebook->getTitle()) ?></h5>
                    <p class="card-text"><?= $ebook->getDetails() ?></p>
                    <p class="card-text">
                        <strong>Original Price:</strong> $<?= $ebook->getPrice() ?><br>
                        <strong>Discounted:</strong> <span class="text-success">$<?= $ebook->getDiscount() ?></span>
                    </p>
                    <a href="<?= htmlspecialchars($ebook->getDownloadUrl()) ?>" class="btn btn-outline-primary" target="_blank">
                        📥 Download
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
            <h4 class="mt-4">📖 Currently Borrowed Books</h4>
            <ul class="list-group">
            <?php
            $loanedBooks = $conn->query("
                SELECT b.title, m.name, bl.loan_date
                FROM BookLoans bl
                JOIN Books b ON bl.book_id = b.book_id
                JOIN Members m ON bl.member_id = m.member_id
                WHERE bl.return_date IS NULL
            ");

            if ($loanedBooks->num_rows > 0) {
                while ($loan = $loanedBooks->fetch_assoc()) {
                    echo "<li class='list-group-item'>" . htmlspecialchars($loan['title']) . 
                         " borrowed by <strong>" . htmlspecialchars($loan['name']) . "</strong> on " . $loan['loan_date'] . "</li>";
                }
            } else {
                echo "<li class='list-group-item text-muted'>No books are currently borrowed.</li>";
            }
            ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>

<?php Database::disconnect(); ?>
