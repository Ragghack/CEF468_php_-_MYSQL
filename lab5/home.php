<?php
require 'auth_check.php';
auth_check();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <p>Email: <?= htmlspecialchars($_SESSION['email']) ?></p>
    
    <?php if (isset($_SESSION['google_user'])): ?>
        <p>You logged in with Google</p>
    <?php endif; ?>
    
    <nav>
        <a href="library.php">Library</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</body>
</html>