<?php
session_start();
include '../config/db.php';

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get vendor_id from URL
$vendorId = isset($_GET['vendor_id']) ? intval($_GET['vendor_id']) : 1;

// Handle approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_product'])) {
    $product_id = intval($_POST['product_id']);
    $stmt = $conn->prepare("UPDATE products SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to prevent resubmission
    header("Location: view_vendor_products.php?vendor_id=$vendorId");
    exit;
}

// Fetch all products (approved and unapproved) for the specified vendor
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
            text-align: center;
        }

        .product {
            display: inline-block;
            width: 220px;
            border: 1px solid #ddd;
            margin: 10px;
            padding: 10px;
            text-align: center;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            vertical-align: top;
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

        .product h3 {
            margin: 10px 0 5px;
        }

        .product p {
            margin: 0;
            font-weight: bold;
            color: #27ae60;
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

        .approve-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 6px 12px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .approve-btn:hover {
            background-color: #27ae60;
        }

        .approved-label {
            display: block;
            color: #888;
            font-size: 14px;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .product {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <a href="managevendor.php" class="back-btn">← Back to Vendors</a>
    <h2>TUPT-OrderGo</h2>
    <div></div>
</div>

<h1>Products from Vendor #<?php echo $vendorId; ?></h1>

<?php
if ($product_query->num_rows > 0) {
    while ($product = $product_query->fetch_assoc()) {
        $name = htmlspecialchars($product['name']);
        $desc = isset($product['description']) ? htmlspecialchars($product['description']) : 'No description';
        $price = number_format($product['price'], 2);
        $image = !empty($product['image_url']) ? $product['image_url'] : 'default-product.jpg';
        $is_approved = $product['is_approved'];

        echo "
        <div class='product'>
            <img src='../images/$image' alt='$name'>
            <h3>$name</h3>
            <p>₱$price</p>";

        if ($is_approved) {
            echo "<span class='approved-label'>Approved</span>";
        } else {
            echo "
            <form method='post'>
                <input type='hidden' name='product_id' value='{$product['id']}'>
                <input type='submit' name='approve_product' value='Approve' class='approve-btn'>
            </form>";
        }

        echo "</div>";
    }
} else {
    echo "<p>No products found for this vendor.</p>";
}
?>

</body>
</html>
