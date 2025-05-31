<?php
session_start();
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $emp_name = trim($_POST["emp_name"]);
    $emp_salary = floatval($_POST["emp_salary"]);
    $emp_dept_id = intval($_POST["emp_dept_id"]);

    // Check if required fields are empty
    if (empty($emp_name) || empty($emp_salary) || empty($emp_dept_id)) {
        $_SESSION['error'] = "❌ All fields are required!";
        header("Location: add_employee.php");
        exit();
    }

    // Salary must be positive
    if ($emp_salary <= 0) {
        $_SESSION['error'] = "❌ Salary must be greater than zero!";
        header("Location: add_employee.php");
        exit();
    }

    // Validate department exists
    $checkDept = $conn->prepare("SELECT dept_id FROM Department WHERE dept_id = ?");
    $checkDept->bind_param("i", $emp_dept_id);
    $checkDept->execute();
    $checkDept->store_result();

    if ($checkDept->num_rows == 0) {
        $_SESSION['error'] = "❌ Invalid department selection!";
        $checkDept->close();
        header("Location: add_employee.php");
        exit();
    }

    $checkDept->close();

    // Insert employee data
    $stmt = $conn->prepare("INSERT INTO Employee (emp_name, emp_salary, emp_dept_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $emp_name, $emp_salary, $emp_dept_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Employee added successfully!";
    } else {
        $_SESSION['error'] = "❌ Failed to add employee. Try again!";
    }

    // Close connections
    $stmt->close();
    $conn->close();

    // Redirect back to the form
    header("Location: add_employee.php");
    exit();
}
?>