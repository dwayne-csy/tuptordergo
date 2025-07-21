<?php
session_start();

// Simulate a logged-in vendor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header('Location: ../login.php');
    exit;
}

$fullname = $_SESSION['fullname'] ?? 'Vendor';

// Stall 1: Rice Meals only
$products = [
    ['id' => 1, 'name' => 'Chicken Adobo', 'price' => 75.00],
    ['id' => 2, 'name' => 'Beef Tapa', 'price' => 80.00],
    ['id' => 3, 'name' => 'Tocino Meal', 'price' => 90.00],
    ['id' => 4, 'name' => 'Fried Bangus', 'price' => 85.00],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Products - Stall 1 (Rice Meals)</title>
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

    .actions button {
      margin: 0 5px;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .edit-btn {
      background-color: #3498db;
      color: #fff;
    }

    .edit-btn:hover {
      background-color: #2980b9;
    }

    .delete-btn {
      background-color: #e74c3c;
      color: #fff;
    }

    .delete-btn:hover {
      background-color: #c0392b;
    }

    .add-form {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .add-form h2 {
      margin-top: 0;
      color: #e67e22;
    }

    .add-form input {
      padding: 10px;
      margin-bottom: 10px;
      width: 100%;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .add-form button {
      padding: 10px 16px;
      background-color: #27ae60;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
    }

    .add-form button:hover {
      background-color: #1e8449;
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
    <h1>Manage Products</h1>

    <!-- Add Product Form -->
    <div class="add-form">
      <h2>Add New Product</h2>
      <form method="post">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" name="price" placeholder="Price" step="10" required>
        <button type="submit">Add Product</button>
      </form>
    </div>

    <!-- Product Table -->
    <table>
      <tr>
        <th>ID</th>
        <th>Product Name</th>
        <th>Price (₱)</th>
        <th>Actions</th>
      </tr>
      <?php foreach ($products as $product): ?>
        <tr>
          <td><?= $product['id'] ?></td>
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td><?= number_format($product['price'], 2) ?></td>
          <td class="actions">
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
