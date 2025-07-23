<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];
$vendor_name = $_SESSION['fullname'] ?? 'Vendor';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];
    $status = '';

    if ($action === 'accept') {
        $status = 'accepted';
    } elseif ($action === 'reject') {
        $status = 'rejected';
    }

    if ($status !== '') {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ? AND vendor_id = ?");
        $stmt->bind_param("sii", $status, $order_id, $vendor_id);
        $stmt->execute();

        $order_stmt = $conn->prepare("SELECT customer_id FROM orders WHERE id = ?");
        $order_stmt->bind_param("i", $order_id);
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        $order_row = $order_result->fetch_assoc();

        if ($order_row) {
            $customer_id = $order_row['customer_id'];
            $timestamp = date('M d, Y H:i');
            $emoji = ($status === 'accepted') ? '✅' : '❎';
            $message = "$emoji $vendor_name $status your order on $timestamp.";

            $notif_stmt = $conn->prepare("INSERT INTO notifications (customer_id, vendor_id, order_id, message) VALUES (?, ?, ?, ?)");
            $notif_stmt->bind_param("iiis", $customer_id, $vendor_id, $order_id, $message);
            $notif_stmt->execute();
        }
    }

    header("Location: manageorder.php");
    exit;
}

$sql = "SELECT o.*, u.fullname AS customer_name FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.vendor_id = ? ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders - <?= htmlspecialchars($vendor_name) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f7fb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2b6777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #dff6ff;
            color: #2b6777;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-accept {
            background-color: #38b000;
            color: white;
        }

        .btn-reject {
            background-color: #d90429;
            color: white;
        }

        .status-accepted {
            color: #2e7d32;
            font-weight: bold;
        }

        .status-rejected {
            color: #c62828;
            font-weight: bold;
        }

        .status-pending {
            color: #f57c00;
            font-weight: bold;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #2b6777;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #1c4654;
        }

        .action-btns form {
            display: inline-block;
            margin: 2px;
        }

        @media screen and (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            td {
                text-align: right;
                position: relative;
                padding-left: 50%;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                font-weight: bold;
                color: #333;
            }

            .action-btns {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <form action="dashboard.php" method="get">
        <button class="back-btn">← Back</button>
    </form>

    <div class="container">
        <h2>Orders</h2>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Product ID</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Placed At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $orders->fetch_assoc()): ?>
                <tr>
                    <td data-label="Order ID">#<?= $row['id'] ?></td>
                    <td data-label="Customer"><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td data-label="Product"><?= $row['product_id'] ?></td>
                    <td data-label="Qty"><?= $row['quantity'] ?></td>
                    <td data-label="Status" class="status-<?= $row['status'] ?>">
                        <?= ucfirst($row['status']) ?>
                    </td>
                    <td data-label="Placed At"><?= $row['created_at'] ?></td>
                    <td data-label="Action" class="action-btns">
                        <?php if ($row['status'] === 'pending'): ?>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="accept" class="btn btn-accept">Accept</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                            </form>
                        <?php else: ?>
                            <em><?= ucfirst($row['status']) ?></em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
