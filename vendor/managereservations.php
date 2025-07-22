<?php
session_start();
include '../config/db.php';

// Only vendors can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];

// Handle reservation acceptance
if (isset($_GET['pending'])) {
    $reservation_id = intval($_GET['pending']);
    $update_sql = "UPDATE reservations SET status = 'accepted' WHERE id = ? AND vendor_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $reservation_id, $vendor_id);
    $stmt->execute();
    $stmt->close();
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
    <title>Manage Reservations</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            margin-top: 20px;
        }
        .back-button {
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .back-button a {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Customer Reservations</h2>

<div class="back-button">
    <a href="dashboard.php">← Back to Dashboard</a>
</div>

<table>
    <tr>
        <th>Customer</th>
        <th>Product</th>
        <th>Table #</th>
        <th>Date</th>
        <th>Time</th>
        <th>Message</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $reservations->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= htmlspecialchars($row['table_number']) ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['time']) ?></td>
            <td><?= htmlspecialchars($row['message']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if ($row['status'] === 'pending'): ?>
                    <a href="?pending=<?= $row['id'] ?>" onclick="return confirm('Accept this reservation?')">Accept</a>
                <?php else: ?>
                    Accepted
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
