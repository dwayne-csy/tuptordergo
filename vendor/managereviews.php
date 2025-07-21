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
  <meta charset="UTF-8" />
  <title>Manage Reviews - Stall 1</title>
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

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 25px;
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
  <h1>Manage Reviews</h1>
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
</div>

</body>
</html>
