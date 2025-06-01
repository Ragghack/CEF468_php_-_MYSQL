<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

// Fetch book details for items in cart
$cart = $_SESSION['cart'] ?? [];
$books = [];
$total = 0.00;

if ($cart) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT book_id, title, price, stock FROM books WHERE book_id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $qty = $cart[$row['book_id']];
        $row['qty'] = $qty;
        $row['line_total'] = $row['price'] * $qty;
        $total += $row['line_total'];
        $books[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <h2>Your Shopping Cart</h2>
  <?php if (!$books): ?>
    <p>Your cart is empty. <a href="customer_dashboard.php">Continue shopping</a>.</p>
  <?php else: ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Title</th>
          <th>Unit Price</th>
          <th>Quantity</th>
          <th>Line Total</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($books as $b): ?>
          <tr>
            <td><?= htmlspecialchars($b['title']) ?></td>
            <td>$<?= number_format($b['price'],2) ?></td>
            <td><?= $b['qty'] ?></td>
            <td>$<?= number_format($b['line_total'],2) ?></td>
            <td>
              <form method="POST" action="remove_from_cart.php" style="display:inline;">
                <input type="hidden" name="book_id" value="<?= $b['book_id'] ?>">
                <button class="btn btn-sm btn-danger">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="3" class="text-end"><strong>Total:</strong></td>
          <td colspan="2"><strong>$<?= number_format($total,2) ?></strong></td>
        </tr>
      </tbody>
    </table>
    <div class="d-flex justify-content-between">
      <a href="customer_dashboard.php" class="btn btn-secondary">Continue Shopping</a>
      <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
