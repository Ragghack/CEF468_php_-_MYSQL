<?php
require_once 'Loanable.php';

class Book implements Loanable {
    protected $book_id;
    protected $title;
    protected $author;
    protected $price;
    protected $genre;
    protected $isLoaned = false;

    public function __construct($book_id, $title, $author, $price, $genre) {
        $this->book_id = $book_id;
        $this->title = $title;
        $this->author = $author;
        $this->price = $price;
        $this->genre = $genre;
    }

    public function borrowBook() {
        if ($this->isLoaned) {
            return "This book is already borrowed.";
        }
        $this->isLoaned = true;
        return "You have successfully borrowed '{$this->title}'.";
    }

    public function returnBook() {
        if (!$this->isLoaned) {
            return "This book wasn't borrowed.";
        }
        $this->isLoaned = false;
        return "You have successfully returned '{$this->title}'.";
    }

    public function getDetails() {
        return "{$this->title} by {$this->author} - {$this->genre} - \${$this->price}";
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getBookId() {
        return $this->book_id;
    }
}
?>
