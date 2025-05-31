<?php
// Start session for potential error messages
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration Form</title>
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Link to optional CSS -->
</head>
<body>

    <h2>👤 User Registration</h2>

    <?php
    // Display error messages if any
    if (isset($_SESSION['error'])) {
        echo "<p class='error'> {$_SESSION['error']}</p>";
        unset($_SESSION['error']); // Remove error after displaying
    }
    ?>

    <form action="process_form.php" method="POST">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" required placeholder="Enter your name">

        <label for="email">Email Address:</label>
        <input type="email" name="email" id="email" required placeholder="Enter your email">

        <label for="age">Age:</label>
        <input type="number" name="age" id="age" required min="1" max="120" placeholder="Enter your age">

        <button type="submit">Submit</button>
    </form>

</body>
</html>