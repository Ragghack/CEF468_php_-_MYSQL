<?php
include "config.php"; // Include database connection
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Employee</title>
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Link CSS -->
</head>
<body>

    <h2> Add a New Employee</h2>

    <form action="process_employee.php" method="POST">
        <label for="emp_name">Employee Name:</label>
        <input type="text" name="emp_name" id="emp_name" required placeholder="Enter employee name">

        <label for="emp_salary">Salary:</label>
        <input type="number" name="emp_salary" id="emp_salary" required min="0" step="0.01" placeholder="Enter salary">

        <label for="emp_dept_id">Department:</label>
        <select name="emp_dept_id" id="emp_dept_id" required>
            <option value="">-- Select Department --</option>
            <?php
            // Fetch department names from Department table
            $result = $conn->query("SELECT dept_id, dept_name FROM Department ORDER BY dept_name");

            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['dept_id']}'>{$row['dept_name']}</option>";
            }
            ?>
        </select>

        <button type="submit">Submit</button>
    </form>

</body>
</html>