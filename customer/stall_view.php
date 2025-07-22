<?php
include '../config/db.php';

// Get vendor_id from URL
$vendorId = isset($_GET['vendor_id']) ? intval($_GET['vendor_id']) : 1;

// Fetch products for the specified vendor
$sql = "SELECT * FROM products WHERE vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendorId);
$stmt->execute();
$product_query = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor <?php echo $vendorId; ?> - Products</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: #f9fafb;
        }
        h1 {
            color: #2c3e50;
            margin-top: 60px;
        }
        .product {
            display: inline-block;
            width: 200px;
            border: 1px solid #ddd;
            margin: 10px;
            padding: 10px;
            text-align: center;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .product:hover {
            transform: scale(1.03);
        }
        .product img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .cart-btn,
        .order-btn {
            flex: 1;
            padding: 6px 8px;
            border: none;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            margin: 0 2px;
        }
        .cart-btn {
            background-color: #f39c12;
        }
        .cart-btn:hover {
            background-color: #d68910;
        }
        .order-btn {
            background-color: #27ae60;
        }
        .order-btn:hover {
            background-color: #219150;
        }
        .back-btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }

        /* Top navigation bar */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .top-bar h2 {
            margin: 0;
            font-size: 20px;
            color: #2c3e50;
        }

        .cart-link {
            text-decoration: none;
            background-color: #f39c12;
            color: white;
            padding: 8px 14px;
            border-radius: 4px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .cart-link:hover {
            background-color: #d68910;
        }

        .cart-link i {
            margin-right: 6px;
        }

        @media (max-width: 600px) {
            .product {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Top Bar with Title and Cart -->
<div class="top-bar">
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    <h2>Stall #<?php echo $vendorId; ?> - School Canteen</h2>
    <a href="cart.php" class="cart-link"><i>🛒</i> Cart</a>
</div>

<h1>Products for Vendor <?php echo $vendorId; ?></h1>

<?php
if ($product_query->num_rows > 0) {
    while ($product = $product_query->fetch_assoc()) {
        $id = $product['id'];
        $name = htmlspecialchars($product['name']);
        $desc = isset($product['description']) ? htmlspecialchars($product['description']) : 'No description';
        $price = number_format($product['price'], 2);
        $image = isset($product['image_url']) && $product['image_url'] ? $product['image_url'] : 'default-product.jpg';

        echo "
        <div class='product'>
            <img src='../images/$image' alt='$name'>
            <h3>$name</h3>
            <p>₱$price</p>
            <div class='button-group'>
                <a href='add_to_cart.php?product_id=$id' class='cart-btn'>🛒 Cart</a>
                <a href='orders.php?product_id=$id' class='order-btn'>🧾 Order</a>
            </div>
        </div>";
    }
} else {
    echo "<p>No products found for this stall.</p>";
}
?>

</body>
</html>
