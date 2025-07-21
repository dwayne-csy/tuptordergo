<?php
session_start();

// Simulate a logged-in customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

$fullname = $_SESSION['fullname'] ?? 'John Doe';

// Define stalls
$stalls = [
    ['Rice Meals', 'food1'],
    ['Noodles & Pasta', 'food2'],
    ['Snacks & Sweets', 'food3'],
    ['Beverages', 'food4'],
    ['Street Foods', 'food5'],
    ['Sandwiches', 'food6'],
    ['Desserts', 'food7'],
    ['Healthy Options', 'food8'],
    ['Breakfast Meals', 'food9'],
    ['Combo Deals', 'food10']
];

// Sample fake notifications based on stalls
$notifications = [
    "🍛 New item in <strong>Rice Meals</strong>: *Chicken Adobo with Rice*!",
    "🍝 <strong>Noodles & Pasta</strong> now serves *Shrimp Alfredo Pasta*!",
    "🍬 Sweet treat alert in <strong>Snacks & Sweets</strong>: *Honey Glazed Donuts*!",
    "🥤 Try the refreshing *Mango Graham Smoothie* at <strong>Beverages</strong> stall!",
    "🍢 <strong>Street Foods</strong> added *Fishball & Isaw Combo*!",
    "🥪 New sandwich in <strong>Sandwiches</strong>: *Clubhouse Triple Stack*!",
    "🍰 Craving dessert? <strong>Desserts</strong> now has *Ube Cheesecake*!",
    "🥗 Eat clean! <strong>Healthy Options</strong> just added *Kale & Avocado Bowl*!",
    "🍳 <strong>Breakfast Meals</strong> now offers *Longganisa with Fried Rice*!",
    "🍽️ <strong>Combo Deals</strong>: *Spaghetti + Fried Chicken + Drink* for ₱99 only!"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notifications</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
    }

    header {
      background-color: #ff7f50;
      color: white;
      padding: 15px 20px;
      text-align: center;
      font-size: 22px;
      font-weight: bold;
    }

    .container {
      padding: 20px;
      max-width: 900px;
      margin: 0 auto;
    }

    .notification {
      background-color: white;
      border-left: 5px solid #ff7f50;
      margin-bottom: 15px;
      padding: 15px 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      border-radius: 6px;
      font-size: 16px;
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

  <header>📢 Notifications</header>
  <div class="container">
    <?php foreach ($notifications as $note): ?>
      <div class="notification">
        <?= $note ?>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
