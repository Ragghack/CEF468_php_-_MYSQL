<?php
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password is empty in XAMPP
$dbname = "EmployeeDB"; // Ensure this matches your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

if (!$conn) {
    die(" Database connection failed.");
} else {
    echo " Database connected successfully!";
}
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Employees</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

    <h2> Employee List</h2>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Salary</th>
            <th>Department</th>
        </tr>

        <?php
        $result = $conn->query("
            SELECT e.emp_id, e.emp_name, e.emp_salary, d.dept_name 
            FROM Employee e 
            INNER JOIN Department d ON e.emp_dept_id = d.dept_id
        ");

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['emp_id']}</td>
                        <td>{$row['emp_name']}</td>
                        <td>\${$row['emp_salary']}</td>
                        <td>{$row['dept_name']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No employees found</td></tr>";
        }

        $conn->close();
        ?>
    </table>

</body>
</html>