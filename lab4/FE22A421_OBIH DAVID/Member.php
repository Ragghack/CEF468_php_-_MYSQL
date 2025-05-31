<?php
class Member {
    private $member_id;
    private $name;
    private $email;
    private $membership_date;
    private $borrowedBooks = []; // Array of Book objects

    public function __construct($member_id, $name, $email, $membership_date) {
        $this->member_id = $member_id;
        $this->name = $name;
        $this->email = $email;
        $this->membership_date = $membership_date;
    }

    public function borrowBook(Book $book) {
        $result = $book->borrowBook();
        if (strpos($result, "successfully")) {
            $this->borrowedBooks[] = $book;
        }
        return $result;
    }

    public function returnBook(Book $book) {
        $result = $book->returnBook();
        // Remove from borrowedBooks
        foreach ($this->borrowedBooks as $index => $b) {
            if ($b->getBookId() === $book->getBookId()) {
                unset($this->borrowedBooks[$index]);
                break;
            }
        }
        return $result;
    }

    public function viewBorrowedBooks() {
        if (empty($this->borrowedBooks)) {
            return "No books currently borrowed.";
        }
        $list = "Borrowed books:\n";
        foreach ($this->borrowedBooks as $book) {
            $list .= "- " . $book->getTitle() . "\n";
        }
        return $list;
    }

    public function getName() {
        return $this->name;
    }

    public function getMemberId() {
        return $this->member_id;
    }
}
?>
