<?php
session_start(); // Start session for feedback messages
include "config.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input values
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $age = intval($_POST["age"]);

    // Validation checks
    if (empty($name) || empty($email) || empty($age)) {
        $_SESSION['error'] = " All fields are required!";
        header("Location: user_form.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = " Invalid email format!";
        header("Location: user_form.php");
        exit();
    }

    // Prepare SQL query to insert data securely
    $stmt = $conn->prepare("INSERT INTO Users (name, email, age) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $email, $age);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User registered successfully!";
    } else {
        $_SESSION['error'] = " Registration failed. Try again!";
    }

    // Close connections
    $stmt->close();
    $conn->close();

    // Redirect back to form
    header("Location: user_form.php");
    exit();
}
?>