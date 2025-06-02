<?php
require 'auth_check.php';
auth_check();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library</title>
</head>
<body>
    <h1>Library Catalog</h1>
    <p>Welcome to the library, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
    
    <!-- CRUD functionality would go here -->
    
    <a href="home.php">Back to Home</a>
</body>
</html>