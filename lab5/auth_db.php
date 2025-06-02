<?php
$host = 'localhost';
$dbname = 'LibraryDB';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        google_id VARCHAR(255) DEFAULT NULL
    )";
    $pdo->exec($sql);
    
    echo "Database setup successfully!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>