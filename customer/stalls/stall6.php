<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../../login.php');
    exit;
}
$fullname = $_SESSION['fullname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Stall 6 - Sandwiches</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      margin: 0;
      background: #f4f4f4;
    }

    .navbar {
      background-color: #e67e22;
      color: white;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .back-btn {
      background-color: #e67e22;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      font-weight: bold;
    }

    .back-btn:hover {
      background-color: #d35400;
    }

    .content {
      padding: 2rem;
    }

    h2 {
      text-align: center;
      color: #e67e22;
    }

    .products {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

    .product {
      background-color: white;
      padding: 1rem;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }

    .product img {
      width: 100%;
      height: 140px;
      object-fit: cover;
      border-radius: 10px;
    }

    .product h4 {
      margin: 10px 0 5px;
      color: #e67e22;
    }

    .product p {
      font-size: 14px;
      color: #555;
    }

    .product span {
      display: block;
      margin: 8px 0;
      font-weight: bold;
    }

    .product button {
      background-color: #e67e22;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .product button:hover {
      background-color: #d35400;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <a href="../../customer/dashboard.php" class="back-btn">&larr; Back</a>
    <div>Welcome, <?php echo htmlspecialchars($fullname); ?>!</div>
  </div>

  <div class="content">
    <h2>Stall 6 - Sandwiches</h2>

    <div class="products">
      <?php
      $products = [
        ['name' => 'Ham & Cheese Sandwich', 'desc' => 'Toasted sandwich with ham and melted cheese.', 'price' => '₱35', 'img' => 'https://cdn.pixabay.com/photo/2014/10/23/18/05/sandwich-500010_1280.jpg'],
        ['name' => 'Clubhouse Sandwich', 'desc' => 'Triple-layered sandwich with meat, veggies, and mayo.', 'price' => '₱50', 'img' => 'https://cdn.pixabay.com/photo/2016/04/27/19/54/sandwich-1353329_1280.jpg'],
        ['name' => 'Egg Sandwich', 'desc' => 'Creamy egg filling on soft bread.', 'price' => '₱30', 'img' => 'https://cdn.pixabay.com/photo/2023/02/04/11/18/sandwich-7767282_1280.jpg']
      ];

      foreach ($products as $product) {
        echo "
        <div class='product'>
          <img src='{$product['img']}' alt='{$product['name']}'>
          <h4>{$product['name']}</h4>
          <p>{$product['desc']}</p>
          <span>{$product['price']}</span>
          <button>Order</button>
        </div>";
      }
      ?>
    </div>
  </div>
</body>
</html>
