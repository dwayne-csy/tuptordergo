<?php
session_start();
include '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

$customer_id = $_SESSION['user_id'];

$sql = "SELECT r.*, 
               u.fullname AS vendor_name, 
               p.name AS product_name 
        FROM reservations r
        JOIN users u ON r.vendor_id = u.id
        JOIN products p ON r.product_id = p.id
        WHERE r.customer_id = ?
        ORDER BY r.date DESC, r.time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$reservations = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Reservations</title>
    <style>
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #aaa;
            text-align: center;
        }
        h2 {
            text-align: center;
        }
        .back-button {
            display: block;
            text-align: center;
            margin: 20px auto;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            width: 200px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h2>My Reservation Receipts</h2>

<table>
    <tr>
        <th>Vendor</th>
        <th>Product</th>
        <th>Date</th>
        <th>Time</th>
        <th>Message</th>
        <th>Status</th>
    </tr>
    <?php if ($reservations->num_rows > 0): ?>
        <?php while ($row = $reservations->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['vendor_name']) ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['time'] ?></td>
                <td><?= htmlspecialchars($row['message']) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No reservations found.</td>
        </tr>
    <?php endif; ?>
</table>

<a href="reservations.php" class="back-button">Back to Reservation Form</a>

</body>
</html>
