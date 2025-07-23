<?php
session_start();
include '../config/db.php';

// Only vendors can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];
$vendor_name = $_SESSION['fullname'] ?? 'Vendor';

// Handle reservation acceptance
if (isset($_GET['pending'])) {
    $reservation_id = intval($_GET['pending']);

    // Accept the reservation
    $update_sql = "UPDATE reservations SET status = 'accepted' WHERE id = ? AND vendor_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $reservation_id, $vendor_id);
    $stmt->execute();
    $stmt->close();

    // Get customer ID for notification
    $res_sql = "SELECT customer_id FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($res_sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $res_result = $stmt->get_result();

    if ($res_row = $res_result->fetch_assoc()) {
        $customer_id = $res_row['customer_id'];
        $timestamp = date('M d, Y H:i');
        $message = "📅 $vendor_name accepted your reservation on $timestamp.";

        // Insert into notifications table
        $notif_sql = "INSERT INTO notifications (customer_id, vendor_id, reservation_id, message) VALUES (?, ?, ?, ?)";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("iiis", $customer_id, $vendor_id, $reservation_id, $message);
        $notif_stmt->execute();
        $notif_stmt->close();
    }

    $stmt->close();
    header("Location: managereservations.php");
    exit;
}

// Fetch vendor-specific reservations
$sql = "SELECT r.*, u.fullname AS customer_name, p.name AS product_name 
        FROM reservations r
        JOIN users u ON r.customer_id = u.id
        JOIN products p ON r.product_id = p.id
        WHERE r.vendor_id = ?
        ORDER BY r.date, r.time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$reservations = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reservations - <?= htmlspecialchars($vendor_name) ?></title>
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

        .btn-accept:hover {
            background-color: #2e8600;
        }

        .accepted-text {
            color: #2e7d32;
            font-weight: bold;
        }

        .pending-text {
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
        }
    </style>
</head>
<body>

<form action="dashboard.php" method="get">
    <button class="back-btn">← Back</button>
</form>

<div class="container">
    <h2>Reservations</h2>

    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Product</th>
                <th>Date</th>
                <th>Time</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $reservations->fetch_assoc()): ?>
            <tr>
                <td data-label="Customer"><?= htmlspecialchars($row['customer_name']) ?></td>
                <td data-label="Product"><?= htmlspecialchars($row['product_name']) ?></td>
                <td data-label="Date"><?= htmlspecialchars($row['date']) ?></td>
                <td data-label="Time"><?= htmlspecialchars($row['time']) ?></td>
                <td data-label="Message"><?= htmlspecialchars($row['message']) ?></td>
                <td data-label="Status" class="<?= $row['status'] === 'accepted' ? 'accepted-text' : 'pending-text' ?>">
                    <?= ucfirst($row['status']) ?>
                </td>
                <td data-label="Action">
                    <?php if ($row['status'] === 'pending'): ?>
                        <a href="?pending=<?= $row['id'] ?>" class="btn btn-accept" onclick="return confirm('Accept this reservation?')">Accept</a>
                    <?php else: ?>
                        <span class="accepted-text"></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
