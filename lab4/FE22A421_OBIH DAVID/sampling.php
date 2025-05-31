<?php
require_once 'Book.php';
require_once 'Member.php';

$book1 = new Book(1, "The Great Gatsby", "F. Scott Fitzgerald", 12.99, "Classic");
$member1 = new Member(101, "Jane Doe", "jane@example.com", "2024-06-01");

echo $member1->borrowBook($book1);
echo "\n" . $member1->viewBorrowedBooks();
echo "\n" . $member1->returnBook($book1);
echo "\n" . $member1->viewBorrowedBooks();
?>
