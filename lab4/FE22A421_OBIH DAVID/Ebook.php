<?php
require_once 'Book.php';
require_once 'Discountable.php';

class Ebook extends Book implements Discountable {
    private $fileSizeMB;
    private $downloadUrl;

    public function __construct($book_id, $title, $author, $price, $genre, $fileSizeMB, $downloadUrl) {
        parent::__construct($book_id, $title, $author, $price, $genre);
        $this->fileSizeMB = $fileSizeMB;
        $this->downloadUrl = $downloadUrl;
    }

    public function download() {
        return "Downloading eBook '{$this->title}' from {$this->downloadUrl}";
    }

    public function getDiscount() {
        return round($this->price * 0.8, 2); // 20% off
    }

    public function getDetails() {
        return parent::getDetails() . " (eBook, {$this->fileSizeMB}MB)";
    }

    // Optionally, you can expose download URL and file size
    public function getDownloadUrl() {
        return $this->downloadUrl;
    }

    public function getFileSizeMB() {
        return $this->fileSizeMB;
    }
}
?>
