<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header('Location: ../login.php');
    exit;
}

$fullname = $_SESSION['fullname'] ?? 'Vendor';

// Sample fake orders
$orders = [
    ['order_id' => 101, 'customer' => 'Juan Dela Cruz', 'product' => 'Chicken Adobo', 'quantity' => 2, 'total' => 150, 'status' => 'Preparing'],
    ['order_id' => 102, 'customer' => 'Maria Santos', 'product' => 'Pasta Carbonara', 'quantity' => 1, 'total' => 90, 'status' => 'Completed'],
    ['order_id' => 103, 'customer' => 'Jose Rizal', 'product' => 'Fruit Salad', 'quantity' => 3, 'total' => 180, 'status' => 'Cancelled'],
];

// For demo only – simulate form submission (no actual change will happen)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedStatus = $_POST['status'] ?? '';
    $orderId = $_POST['order_id'] ?? '';
    echo "<script>alert('Order #{$orderId} status updated to {$updatedStatus} (demo only)');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Orders - TUPT OrderGo</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fdfdfd;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
    }

    h1 {
      color: #e67e22;
      text-align: center;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }

    th, td {
      padding: 15px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #e67e22;
      color: #fff;
    }

    tr:hover {
      background-color: #f9f9f9;
    }

    select {
      padding: 6px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .update-btn {
      background-color: #3498db;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .update-btn:hover {
      background-color: #2980b9;
    }

    .back-button {
      position: fixed;
      top: 20px;
      left: 20px;
    }

    .back-button button {
      padding: 10px 16px;
      background-color: #e67e22;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
    }

    .back-button button:hover {
      background-color: #d35400;
    }
  </style>
</head>
<body>
  <!-- Back Button -->
  <div class="back-button">
    <form action="dashboard.php" method="get">
      <button type="submit">← Back</button>
    </form>
  </div>

  <div class="container">
    <h1>Manage Orders</h1>

    <table>
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total (₱)</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= $order['order_id'] ?></td>
          <td><?= htmlspecialchars($order['customer']) ?></td>
          <td><?= htmlspecialchars($order['product']) ?></td>
          <td><?= $order['quantity'] ?></td>
          <td><?= number_format($order['total'], 2) ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
              <select name="status">
                <option value="Preparing" <?= $order['status'] === 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                <option value="Completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
              </select>
          </td>
          <td>
              <button type="submit" class="update-btn">Update</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
