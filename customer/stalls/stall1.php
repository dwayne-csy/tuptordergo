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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Stall 1 - Rice Meals</title>
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
    <div>Welcome, <?php echo htmlspecialchars($fullname); ?></div>
  </div>

  <div class="content">
    <h2>Stall 1 - Rice Meals</h2>

    <div class="products">
      <?php
      $products = [
        ['name' => 'Chicken Adobo', 'desc' => 'Classic Filipino chicken adobo with rice.', 'price' => '₱89', 'img' => 'https://cdn.pixabay.com/photo/2020/08/04/08/30/filipino-food-5462291_1280.jpg'],
        ['name' => 'Beef Tapa', 'desc' => 'Beef tapa with garlic rice and egg.', 'price' => '₱99', 'img' => 'https://cdn.pixabay.com/photo/2021/01/28/06/43/beef-tapa-5957426_1280.jpg'],
        ['name' => 'Tocino Meal', 'desc' => 'Sweet pork tocino served with fried rice.', 'price' => '₱85', 'img' => 'https://cdn.pixabay.com/photo/2020/07/15/18/05/tocino-5408471_1280.jpg'],
        ['name' => 'Longganisa Rice Meal', 'desc' => 'Garlicky longganisa with egg and rice.', 'price' => '₱90', 'img' => 'https://cdn.pixabay.com/photo/2021/03/08/07/48/filipino-food-6076495_1280.jpg'],
        ['name' => 'Fried Bangus', 'desc' => 'Crispy milkfish served with rice.', 'price' => '₱95', 'img' => 'https://cdn.pixabay.com/photo/2017/05/11/08/47/fish-2303290_1280.jpg']
      ];

      foreach ($products as $product) {
        echo "
        <div class='product'>
          <img src='{$product['img']}' alt='{$product['name']}'>
          <h4>{$product['name']}</h4>
          <p>{$product['desc']}</p>
          <span>{$product['price']}</span>
          <button>Order</button>
        </div>
        ";
      }
      ?>
    </div>
  </div>

</body>
</html>
