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
  <title>Stall 2 - Noodles & Pasta</title>
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
    <h2>Stall 2 - Noodles & Pasta</h2>

    <div class="products">
      <?php
      $products = [
        ['name' => 'Spaghetti', 'desc' => 'Classic sweet-style Filipino spaghetti.', 'price' => '₱70', 'img' => 'https://cdn.pixabay.com/photo/2017/08/06/00/18/spaghetti-2589424_1280.jpg'],
        ['name' => 'Pancit Canton', 'desc' => 'Stir-fried noodles with vegetables.', 'price' => '₱60', 'img' => 'https://cdn.pixabay.com/photo/2020/08/04/08/29/pancit-canton-5462286_1280.jpg'],
        ['name' => 'Carbonara', 'desc' => 'Creamy carbonara with ham.', 'price' => '₱80', 'img' => 'https://cdn.pixabay.com/photo/2017/01/22/19/20/food-2009590_1280.jpg']
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
