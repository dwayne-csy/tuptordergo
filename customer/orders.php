<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $payment_method = $_POST['payment_method'];

    // Get vendor_id from the product
    $stmt = $conn->prepare("SELECT vendor_id FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($vendor_id);
    $stmt->fetch();
    $stmt->close();

    if (!$vendor_id) {
        echo "Invalid product or vendor.";
        exit;
    }

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, vendor_id, product_id, quantity, payment_method, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iiiis", $user_id, $vendor_id, $product_id, $quantity, $payment_method);
    
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        $stmt->close();

        // ✅ Redirect with the order_id
        header("Location: order_success.php?order_id=" . $order_id);
        exit;
    } else {
        echo "Failed to place order.";
        $stmt->close();
        exit;
    }
}

// If no product_id in URL, show error
if (!isset($_GET['product_id'])) {
    echo "No product selected.";
    exit;
}

$product_id = intval($_GET['product_id']);

// Get product details
$stmt = $conn->prepare("
    SELECT p.id as product_id, p.name as product_name, p.price, u.fullname as vendor_name 
    FROM products p 
    JOIN users u ON p.vendor_id = u.id 
    WHERE p.id = ?
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "Product not found.";
    exit;
}
?>
<!-- HTML form stays unchanged -->


<!DOCTYPE html>
<html>
<head>
    <title>Confirm Order</title>
</head>
<body>
    <h2>Confirm Your Order</h2>

    <form method="post" action="orders.php">
        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

        <p><strong>Product:</strong> <?= htmlspecialchars($product['product_name']) ?></p>
        <p><strong>Vendor:</strong> <?= htmlspecialchars($product['vendor_name']) ?></p>
        <p><strong>Price:</strong> ₱<?= $product['price'] ?></p>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" value="1" min="1" required>
        <br><br>

        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" required>
            <option value="">-- Select Payment --</option>
            <option value="Gcash">Gcash</option>
            <option value="Cash">Cash</option>
        </select>
        <br><br>

        <button type="submit">Confirm Order</button>
    </form>

    <br><a href="../customer/stall_view.php">Cancel and Go Back</a>
</body>
</html>
