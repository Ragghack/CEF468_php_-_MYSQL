<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$book_id = $_POST['book_id'];
$price = $_POST['price'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Purchase Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4">
        <h3 class="mb-3">Purchase Book</h3>

        <form action="confirm_purchase.php" method="POST">
            <input type="hidden" name="book_id" value="<?= $book_id ?>">
            <input type="hidden" name="price" value="<?= $price ?>">

            <div class="mb-3">
                <label for="method">Payment Method</label>
                <select class="form-select" name="payment_method" id="method" required>
                    <option value="">Select a method</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="mtn_momo">MTN Mobile Money</option>
                </select>
            </div>

            <div id="credit-card-section" style="display:none">
                <div class="mb-3">
                    <label>Card Number</label>
                    <input type="text" name="card_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Expiry Date</label>
                    <input type="text" name="expiry_date" class="form-control" placeholder="MM/YY">
                </div>
                <div class="mb-3">
                    <label>CVV</label>
                    <input type="text" name="cvv" class="form-control">
                </div>
            </div>

            <div id="mtn-section" style="display:none">
                <div class="mb-3">
                    <label>MTN MoMo Number</label>
                    <input type="text" name="mtn_number" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Confirm Purchase</button>
        </form>
    </div>
</div>

<script>
    const methodSelect = document.getElementById("method");
    const ccSection = document.getElementById("credit-card-section");
    const mtnSection = document.getElementById("mtn-section");

    methodSelect.addEventListener("change", () => {
        const value = methodSelect.value;
        ccSection.style.display = value === "credit_card" ? "block" : "none";
        mtnSection.style.display = value === "mtn_momo" ? "block" : "none";
    });
</script>

</body>
</html>
