<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

$order_ids = [];

if (isset($_GET['order_id'])) {
    $order_ids[] = intval($_GET['order_id']);
} elseif (isset($_GET['order_ids'])) {
    $ids = explode(",", $_GET['order_ids']);
    foreach ($ids as $id) {
        $order_ids[] = intval($id);
    }
}

$orders = [];
$grand_total = 0;

foreach ($order_ids as $id) {
    $stmt = $conn->prepare("
        SELECT o.*, p.name AS product_name, p.price, p.image_url
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.id = ? AND o.customer_id = ?
    ");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $orders[] = $row;
        $grand_total += $row['price'] * $row['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Receipt</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f9fbfd;
        margin: 0;
        padding: 40px 20px;
        display: flex;
        justify-content: center;
    }
    .receipt-box {
        background: #fff;
        max-width: 800px;
        width: 100%;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    h2 {
        text-align: center;
        color: #00b894;
        margin-bottom: 24px;
    }
    .order-card {
        display: flex;
        gap: 20px;
        background: #f1f6fa;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        align-items: center;
        transition: all 0.2s;
    }
    .order-card:hover {
        background: #e8f4fa;
    }
    .order-card img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #ffeaa7;
    }
    .order-details {
        flex: 1;
    }
    .order-details p {
        margin: 4px 0;
        font-size: 14px;
    }
    .order-details p strong {
        color: #0984e3;
    }
    .grand-total {
        text-align: right;
        font-size: 18px;
        font-weight: 600;
        color: #00b894;
        margin-top: 10px;
        background: #f1f6fa;
        padding: 12px 16px;
        border-radius: 10px;
    }
    .back-btn {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        padding: 10px 18px;
        background: #00b894;
        color: white;
        border-radius: 8px;
        font-weight: 500;
        transition: background 0.3s ease;
    }
    .back-btn:hover {
        background: #019875;
    }
    .no-order {
        text-align: center;
        color: #636e72;
        font-style: italic;
    }
    @media(max-width:600px){
        .order-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .order-card img {
            width: 80px;
            height: 80px;
        }
    }
</style>
</head>
<body>

<div class="receipt-box">
    <h2>Order Receipt</h2>

    <?php if (empty($orders)): ?>
        <p class="no-order">No order found.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
                $imageFile = !empty($order['image_url']) ? htmlspecialchars($order['image_url']) : 'no-image.png';
                $imagePath = "../images/" . $imageFile;
            ?>
            <div class="order-card">
                <img src="<?= $imagePath ?>" alt="Product Image">
                <div class="order-details">
                    <p><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
                    <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
                    <p><strong>Total:</strong> ₱<?= number_format($order['price'] * $order['quantity'], 2) ?></p>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
                    <p><strong>Order Date:</strong> <?= $order['created_at'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="grand-total">
            Grand Total: ₱<?= number_format($grand_total, 2) ?>
        </div>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
