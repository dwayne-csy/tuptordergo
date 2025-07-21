<?php
session_start();

// Simulate a logged-in customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

$fullname = $_SESSION['fullname'] ?? 'John Doe';

$stalls = [
    ['Stall 1', 'food1'],
    ['Stall 2', 'food2'],
    ['Stall 3', 'food3'],
    ['Stall 4', 'food4'],
    ['Stall 5', 'food5'],
    ['Stall 6', 'food6'],
    ['Stall 7', 'food7'],
    ['Stall 8', 'food8'],
    ['Stall 9', 'food9'],
    ['Stall 10', 'food10']
];

// Fake orders with only the statuses: Preparing, Completed, Cancelled
$fakeOrders = [
    ['stall' => 1, 'item' => 'Chicken Adobo', 'quantity' => 1, 'total' => 75, 'status' => 'Preparing'],
    ['stall' => 2, 'item' => 'Carbonara', 'quantity' => 1, 'total' => 90, 'status' => 'Completed'],
    ['stall' => 4, 'item' => 'Lemon Iced Tea', 'quantity' => 2, 'total' => 40, 'status' => 'Preparing'],
    ['stall' => 6, 'item' => 'Ham & Cheese Sandwich', 'quantity' => 1, 'total' => 55, 'status' => 'Completed'],
    ['stall' => 7, 'item' => 'Chocolate Cake', 'quantity' => 1, 'total' => 65, 'status' => 'Cancelled'],
    ['stall' => 9, 'item' => 'Tapsilog', 'quantity' => 1, 'total' => 80, 'status' => 'Completed'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Orders - TUPT OrderGo</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fdfdfd;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 900px;
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

    .status-Preparing {
      color: orange;
      font-weight: bold;
    }

    .status-Completed {
      color: green;
      font-weight: bold;
    }

    .status-Cancelled {
      color: red;
      font-weight: bold;
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
    <h1>My Orders</h1>

    <table>
      <tr>
        <th>Stall</th>
        <th>Food Item</th>
        <th>Quantity</th>
        <th>Total Price (₱)</th>
        <th>Status</th>
      </tr>
      <?php foreach ($fakeOrders as $order): ?>
        <tr>
          <td><?= $stalls[$order['stall'] - 1][0] ?></td>
          <td><?= htmlspecialchars($order['item']) ?></td>
          <td><?= $order['quantity'] ?></td>
          <td><?= number_format($order['total'], 2) ?></td>
          <td class="status-<?= $order['status'] ?>"><?= $order['status'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
