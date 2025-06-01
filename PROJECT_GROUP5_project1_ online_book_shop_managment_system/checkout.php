<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$cart = $_SESSION['cart'] ?? [];

// Fetch cart items details
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
        $line = $row['price'] * $qty;
        $row['qty'] = $qty;
        $row['line_total'] = $line;
        $total += $line;
        $books[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect payment info
    $method = $_POST['payment_method'];
    $card = trim($_POST['card_number'] ?? '');
    $expiry = trim($_POST['expiry_date'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');
    $mtn = trim($_POST['mtn_number'] ?? '');

    // Here you would integrate real payment gateway...
    // For now, assume payment is always successful.

    // Insert orders and update stock
    $order_stmt = $conn->prepare("
        INSERT INTO orders (customer_id, book_id, quantity, total_price, payment_method, payment_status, created_at)
        VALUES (?, ?, ?, ?, ?, 'Paid', NOW())
    ");
    foreach ($books as $b) {
        $order_stmt->bind_param(
            "iiids",
            $customer_id,
            $b['book_id'],
            $b['qty'],
            $b['line_total'],
            $method
        );
        $order_stmt->execute();

        // decrement stock
        $upd = $conn->prepare("UPDATE books SET stock = stock - ? WHERE book_id = ?");
        $upd->bind_param("ii", $b['qty'], $b['book_id']);
        $upd->execute();
    }

    // Clear cart
    unset($_SESSION['cart']);

    header("Location: thank_you.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Checkout</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <h2>Checkout</h2>

  <?php if (!$books): ?>
    <p>Your cart is empty. <a href="customer_dashboard.php">Shop Now</a>.</p>
    <?php exit; ?>
  <?php endif; ?>

  <h4>Order Summary</h4>
  <table class="table table-striped">
    <thead>
      <tr><th>Title</th><th>Price</th><th>Qty</th><th>Line Total</th></tr>
    </thead>
    <tbody>
      <?php foreach($books as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['title']) ?></td>
          <td>$<?= number_format($b['price'],2) ?></td>
          <td><?= $b['qty'] ?></td>
          <td>$<?= number_format($b['line_total'],2) ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="3" class="text-end"><strong>Total:</strong></td>
        <td><strong>$<?= number_format($total,2) ?></strong></td>
      </tr>
    </tbody>
  </table>

  <h4 class="mt-4">Payment Details</h4>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Payment Method</label>
      <select name="payment_method" id="method" class="form-select" required>
        <option value="">Choose…</option>
        <option value="Credit Card">Credit Card</option>
        <option value="MTN Mobile Money">MTN Mobile Money</option>
      </select>
    </div>

    <div id="cc" style="display:none">
      <div class="mb-3">
        <label class="form-label">Card Number</label>
        <input type="text" name="card_number" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Expiry (MM/YY)</label>
        <input type="text" name="expiry_date" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">CVV</label>
        <input type="text" name="cvv" class="form-control">
      </div>
    </div>

    <div id="mtn" style="display:none">
      <div class="mb-3">
        <label class="form-label">MTN MoMo Number</label>
        <input type="text" name="mtn_number" class="form-control">
      </div>
    </div>

    <button class="btn btn-primary">Pay $<?= number_format($total,2) ?></button>
  </form>
</div>

<script>
  const sel = document.getElementById('method');
  sel.addEventListener('change', ()=> {
    document.getElementById('cc').style.display = sel.value==='Credit Card' ? 'block':'none';
    document.getElementById('mtn').style.display = sel.value==='MTN Mobile Money' ? 'block':'none';
  });
</script>
</body>
</html>
