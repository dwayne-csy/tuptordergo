<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header('Location: ../login.php');
    exit;
}
$fullname = $_SESSION['fullname'] ?? 'Vendor';

// Updated product list for Stall 1 - Rice Meals
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
  <meta charset="UTF-8">
  <title>Manage Reviews - Stall 1</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      margin: 0;
    }
    header {
      background: #004080;
      padding: 15px;
      color: #fff;
      text-align: center;
    }
    .container {
      margin: 20px auto;
      max-width: 900px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 12px;
      text-align: left;
    }
    th {
      background: #e6f2ff;
    }
    select {
      padding: 5px;
    }
    .back-btn {
      margin-top: 15px;
      display: inline-block;
      background: #004080;
      color: #fff;
      padding: 10px 16px;
      border-radius: 6px;
      text-decoration: none;
    }
    .back-btn:hover {
      background: #003366;
    }
  </style>
</head>
<body>

<header>
  <h1>Manage Reviews</h1>
</header>



<div class="container">
  <h2>Customer Feedback</h2>
  <table>
    <thead>
      <tr>
        <th>Customer Name</th>
        <th>Product</th>
        <th>Review</th>
        <th>Rating</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>John Reyes</td>
        <td>Chicken Adobo</td>
        <td>"Masarap yung Chicken Adobo, flavorful and tender!"</td>
        <td>⭐⭐⭐⭐⭐</td>
        <td>
          <select>
            <option>Visible</option>
            <option>Hidden</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Andrea Gomez</td>
        <td>Beef Tapa</td>
        <td>"Medyo dry yung Beef Tapa pero okay pa rin lasa."</td>
        <td>⭐⭐⭐</td>
        <td>
          <select>
            <option>Visible</option>
            <option>Hidden</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Michael Tan</td>
        <td>Tocino Meal</td>
        <td>"Sweet and juicy Tocino — favorite ko!"</td>
        <td>⭐⭐⭐⭐</td>
        <td>
          <select>
            <option>Visible</option>
            <option>Hidden</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Grace Lim</td>
        <td>Fried Bangus</td>
        <td>"Crispy and not too oily. Sarap ng Fried Bangus!"</td>
        <td>⭐⭐⭐⭐⭐</td>
        <td>
          <select>
            <option>Visible</option>
            <option>Hidden</option>
          </select>
        </td>
      </tr>
    </tbody>
  </table>

  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

</body>
</html>
