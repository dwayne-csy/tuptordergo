<?php
session_start();
include '../config/db.php';

// Check if user is logged in and a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $table_no = $_POST['table_no'];
    $stall = $_POST['stall'];
    $food = $_POST['food'];
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];

    $sql = "INSERT INTO reservations (user_id, table_no, stall, food, reservation_date, reservation_time) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $user_id, $table_no, $stall, $food, $reservation_date, $reservation_time);

    if ($stmt->execute()) {
        $success = "✅ Reservation successful!";
    } else {
        $error = "❌ Failed to reserve. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reserve Table - TUPT OrderGo</title>
  <style>
    body {
      background: #f4f4f4;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
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

    .reservation-box {
      background: #fff;
      padding: 30px 40px;
      border-radius: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 360px;
    }

    h2 {
      text-align: center;
      color: #d35400;
      margin-bottom: 25px;
    }

    label {
      font-weight: 600;
      margin-top: 10px;
      display: block;
    }

    select, input[type="date"], input[type="time"] {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: 2px solid #f39c12;
      margin-bottom: 20px;
      font-size: 16px;
    }

    button[type="submit"] {
      width: 100%;
      padding: 12px;
      background: #e67e22;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
    }

    button[type="submit"]:hover {
      background: #d35400;
    }

    .message {
      text-align: center;
      margin-bottom: 15px;
      font-weight: bold;
    }

    .success {
      color: green;
    }

    .error {
      color: red;
    }
  </style>
</head>
<body>

  <!-- Back Button -->
  <div class="back-button">
    <form action="../customer/dashboard.php" method="get">
      <button type="submit">← Back</button>
    </form>
  </div>

  <div class="reservation-box">
    <h2>Reserve a Table</h2>

    <?php if (isset($success)) echo "<p class='message success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='message error'>$error</p>"; ?>

    <form method="POST" action="">
      <label for="table_no">Table Number:</label>
      <select name="table_no" id="table_no" required>
        <option value="" disabled selected>Select a table</option>
        <?php for ($i = 1; $i <= 30; $i++): ?>
          <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
      </select>

      <label for="stall">Stall:</label>
      <select name="stall" id="stall" required onchange="populateFoods(this.value)">
        <option value="" disabled selected>Select a stall</option>
        <option value="Stall 1">Rice Meals</option>
        <option value="Stall 2">Noodles & Pasta</option>
        <option value="Stall 3">Snacks & Sweets</option>
        <option value="Stall 4">Beverages</option>
        <option value="Stall 5">Street Foods</option>
        <option value="Stall 6">Sandwiches</option>
        <option value="Stall 7">Desserts</option>
        <option value="Stall 8">Healthy Options</option>
        <option value="Stall 9">Breakfast Meals</option>
        <option value="Stall 10">Combo Deals</option>
      </select>

      <label for="food">Food:</label>
      <select name="food" id="food" required>
        <option value="" disabled selected>Select food</option>
      </select>

      <label for="reservation_date">Date:</label>
      <input type="date" name="reservation_date" id="reservation_date" required min="<?= date('Y-m-d'); ?>">

      <label for="reservation_time">Time:</label>
      <input type="time" name="reservation_time" id="reservation_time" required>

      <button type="submit">Reserve</button>
    </form>
  </div>

  <script>
    const stallFoods = {
      "Stall 1": ["Chicken Adobo", "Beef Tapa", "Tocino Meal", "Longganisa Rice Meal", "Fried Bangus"],
      "Stall 2": ["Spaghetti", "Carbonara", "Pancit Canton"],
      "Stall 3": ["Banana Cue", "Turon", "Chocolate Cake Slice"],
      "Stall 4": ["Iced Tea", "Fruit Shake","Bottled Water"],
      "Stall 5": ["Kwek-Kwek", "Fishball","Isaw"],
      "Stall 6": ["Ham Sandwich", "Clubhouse Sandwich", "Egg Sandwich"],
      "Stall 7": ["Leche Flan", "Buko Pandan","Chocolate Cake Slice"],
      "Stall 8": ["Grilled Chicken Salad", "Veggie Wrap", "Fruit Cup"],
      "Stall 9": ["Pancake with Egg", "Tapsilog", "Longsilog" ],
      "Stall 10": ["Chicken + Rice + Drink", "Burger + Fries + Soda", "Pasta + Garlic Bread + Juice"]
    };

    function populateFoods(stall) {
      const foodSelect = document.getElementById("food");
      foodSelect.innerHTML = '<option value="" disabled selected>Select food</option>';

      if (stallFoods[stall]) {
        stallFoods[stall].forEach(food => {
          const option = document.createElement("option");
          option.value = food;
          option.textContent = food;
          foodSelect.appendChild(option);
        });
      }
    }
  </script>
</body>
</html>
