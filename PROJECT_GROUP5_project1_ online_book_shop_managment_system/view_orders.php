<?php
require_once 'db_connect.php';

// Build the query
$sql = "
    SELECT 
        o.order_id,
        c.name AS customer_name,
        b.title AS book_title,
        o.quantity,
        o.total_price,
        o.payment_method,
        o.payment_status,
        o.created_at AS order_date
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    JOIN books b ON o.book_id = b.book_id
    ORDER BY o.created_at DESC
";

// Execute the query
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Orders List</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Book Title</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['book_title']) ?></td>
                    <td><?= htmlspecialchars($order['quantity']) ?></td>
                    <td>$<?= number_format($order['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                    <td><?= htmlspecialchars($order['payment_status']) ?></td>
                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
