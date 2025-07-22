<?php
session_start();
include '../config/db.php';

// Only allow logged-in customers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all orders for the customer
$sql = "
SELECT 
    o.id AS order_id,
    p.name AS product_name,
    u.fullname AS vendor_name,
    o.quantity,
    p.price,
    o.payment_method,
    o.created_at
FROM orders o
JOIN products p ON o.product_id = p.id
JOIN users u ON o.vendor_id = u.id
WHERE o.customer_id = ?
ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Order History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            background-color: #f9f9f9;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
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
    <h2>🧾 My Order History</h2>

    <?php if (count($orders) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Vendor</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td><?= htmlspecialchars($order['vendor_name']) ?></td>
                        <td>₱<?= number_format($order['price'], 2) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td>₱<?= number_format($order['price'] * $order['quantity'], 2) ?></td>
                        <td><?= htmlspecialchars($order['payment_method']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center;">You have no order history yet.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="back-link">← Back</a>
</body>
</html>
