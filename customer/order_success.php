<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    echo "No order ID provided.";
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch the order details
$stmt = $conn->prepare("SELECT o.id AS order_id, p.name AS product_name, u.fullname AS vendor_name, 
                               o.quantity, o.payment_method, p.price, o.created_at
                        FROM orders o
                        JOIN products p ON o.product_id = p.id
                        JOIN users u ON o.vendor_id = u.id
                        WHERE o.id = ? AND o.customer_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "Order not found.";
    exit;
}

$total = $order['price'] * $order['quantity'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 40px auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .receipt-row strong {
            display: inline-block;
            width: 120px;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
            border-top: 1px dashed #aaa;
            padding-top: 10px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            color: #007BFF;
        }
    </style>
</head>
<body>
    <h2>🧾 Order Receipt</h2>

    <div class="receipt-row"><strong>Order ID:</strong> #<?= $order['order_id'] ?></div>
    <div class="receipt-row"><strong>Date:</strong> <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></div>
    <div class="receipt-row"><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></div>
    <div class="receipt-row"><strong>Vendor:</strong> <?= htmlspecialchars($order['vendor_name']) ?></div>
    <div class="receipt-row"><strong>Price:</strong> ₱<?= number_format($order['price'], 2) ?></div>
    <div class="receipt-row"><strong>Quantity:</strong> <?= $order['quantity'] ?></div>
    <div class="receipt-row"><strong>Payment:</strong> <?= htmlspecialchars($order['payment_method']) ?></div>
    <div class="receipt-row total"><strong>Total:</strong> ₱<?= number_format($total, 2) ?></div>

    <a href="stall_view.php" class="back-link">← Back to Stalls</a>
</body>
</html>
