<?php
session_start();
include '../config/db.php';

// Only allow vendors
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];
$vendor_name = $_SESSION['fullname'] ?? 'Vendor';

// Handle order status updates (confirm or reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'] === 'confirm' ? 'confirmed' : 'rejected';

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("sii", $action, $order_id, $vendor_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all orders for this vendor
$stmt = $conn->prepare("
    SELECT o.id, o.quantity, o.payment_method, o.status, o.created_at,
           p.name AS product_name, p.price,
           u.fullname AS customer_name
    FROM orders o
    JOIN products p ON o.product_id = p.id
    JOIN users u ON o.customer_id = u.id
    WHERE o.vendor_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders - <?= htmlspecialchars($vendor_name) ?></title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 6px 12px;
            margin-right: 5px;
            border: none;
            cursor: pointer;
        }
        .btn-confirm { background-color: #4CAF50; color: white; }
        .btn-reject { background-color: #f44336; color: white; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .back-btn {
            background-color: #555;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border: none;
            margin-top: 10px;
            display: inline-block;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>📦 Manage Orders</h2>
    <p>Welcome, <strong><?= htmlspecialchars($vendor_name) ?></strong></p>

    <!-- Back Button -->
    <form action="dashboard.php" method="get">
        <button class="back-btn">← Back</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Payment</th>
                <th>Total</th>
                <th>Status</th>
                <th>Placed At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()):
            $total = $row['price'] * $row['quantity'];
        ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                <td>₱<?= number_format($total, 2) ?></td>
                <td class="status-<?= $row['status'] ?>">
                    <?= ucfirst($row['status']) ?>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <?php if ($row['status'] === 'pending'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="confirm" class="btn btn-confirm">Confirm</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                        </form>
                    <?php else: ?>
                        <em>Accepted</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
