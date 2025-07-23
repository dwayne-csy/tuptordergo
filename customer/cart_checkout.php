<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];

    $cart_stmt = $conn->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = ?");
    $cart_stmt->bind_param("i", $customer_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();

    $order_ids = [];

    while ($row = $cart_result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];

        $vendor_stmt = $conn->prepare("SELECT vendor_id FROM products WHERE id = ?");
        $vendor_stmt->bind_param("i", $product_id);
        $vendor_stmt->execute();
        $vendor_result = $vendor_stmt->get_result();
        $vendor_id = $vendor_result->fetch_assoc()['vendor_id'];

        $order_stmt = $conn->prepare("INSERT INTO orders (customer_id, vendor_id, product_id, quantity, payment_method, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $order_stmt->bind_param("iiiis", $customer_id, $vendor_id, $product_id, $quantity, $payment_method);
        $order_stmt->execute();
        $order_ids[] = $order_stmt->insert_id;
    }

    $delete_cart = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $delete_cart->bind_param("i", $customer_id);
    $delete_cart->execute();

    $encoded = urlencode(implode(",", $order_ids));
    header("Location: order_success.php?order_ids=$encoded");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart - TUPTOrderGo</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f9fbfd;
        margin: 0;
        padding: 40px 20px;
        display: flex;
        justify-content: center;
    }
    .cart-box {
        background: #fff;
        max-width: 900px;
        width: 100%;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        position: relative;
    }
    header {
        text-align: center;
        font-size: 2rem;
        font-weight: 600;
        color: #00b894;
        margin-bottom: 20px;
        position: relative;
    }
    .back-btn {
        text-decoration: none;
        color: #0984e3;
        font-size: 14px;
        font-weight: 500;
        background: #eaf4fb;
        padding: 6px 12px;
        border-radius: 8px;
        position: absolute;
        top: 20px;
        left: 20px;
        transition: background 0.3s;
    }
    .back-btn:hover {
        background: #d6ecf8;
    }
    h2 {
        font-size: 1.5rem;
        color: #0984e3;
        margin-bottom: 20px;
        position: relative;
    }
    h2::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background: #ffeaa7;
        border-radius: 2px;
        margin-top: 6px;
    }
    .cart-item {
        display: flex;
        align-items: center;
        gap: 20px;
        background: #f1f6fa;
        padding: 16px;
        border-radius: 14px;
        margin-bottom: 16px;
        transition: all 0.2s ease;
    }
    .cart-item:hover {
        background: #e8f4fa;
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    .cart-item img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #ffeaa7;
    }
    .cart-details {
        flex: 1;
    }
    .cart-details span.name {
        font-size: 17px;
        font-weight: 600;
        color: #2d3436;
        display: block;
        margin-bottom: 4px;
    }
    .cart-details span {
        font-size: 14px;
        color: #636e72;
        display: block;
    }
    .empty {
        background: #ffeaa7;
        color: #2d3436;
        text-align: center;
        padding: 16px;
        border-radius: 12px;
        font-style: italic;
        margin-bottom: 20px;
    }
    .total {
        text-align: right;
        font-size: 18px;
        font-weight: 600;
        color: #00b894;
        margin-top: 10px;
        background: #f1f6fa;
        padding: 10px 16px;
        border-radius: 10px;
    }
    form {
        margin-top: 20px;
        text-align: right;
    }
    select {
        padding: 10px 14px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 10px;
        margin-right: 10px;
        background: #f9fcff;
        transition: border-color 0.3s;
    }
    select:focus {
        outline: none;
        border-color: #00b894;
    }
    button {
        padding: 12px 24px;
        background: #00b894;
        color: #fff;
        font-size: 15px;
        font-weight: 500;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    button:hover {
        background: #019875;
    }
    @media(max-width:680px){
        .cart-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
        }
        form {
            text-align: center;
        }
        select {
            margin-bottom: 12px;
        }
    }
</style>
</head>
<body>

<div class="cart-box">
    <header>
        <a href="dashboard.php" class="back-btn">← Back</a>
        TUPTOrderGo
    </header>
    <h2>Your Cart Items</h2>

    <?php
    $cart_display_stmt = $conn->prepare("
        SELECT p.name, p.image_url, p.price, ci.quantity 
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?
    ");
    $cart_display_stmt->bind_param("i", $customer_id);
    $cart_display_stmt->execute();
    $cart_display_result = $cart_display_stmt->get_result();

    $total = 0;

    if ($cart_display_result->num_rows === 0) {
        echo "<div class='empty'>Your cart is empty. Grab your favorite snacks!</div>";
    } else {
        while ($item = $cart_display_result->fetch_assoc()) {
            $imageFile = !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'no-image.png';
            $imagePath = "../images/" . $imageFile;

            $price = $item['price'];
            $quantity = $item['quantity'];
            $subtotal = $price * $quantity;
            $total += $subtotal;

            echo "<div class='cart-item'>";
            echo "<img src='$imagePath' alt='Product Image'>";
            echo "<div class='cart-details'>";
            echo "<span class='name'>" . htmlspecialchars($item['name']) . "</span>";
            echo "<span>Quantity: " . htmlspecialchars($quantity) . "</span>";
            echo "<span>Price: ₱" . number_format($price, 2) . "</span>";
            echo "<span>Subtotal: ₱" . number_format($subtotal, 2) . "</span>";
            echo "</div></div>";
        }
        echo "<div class='total'>Total: ₱" . number_format($total, 2) . "</div>";
    }
    ?>

    <form method="POST">
        <select name="payment_method" required>
            <option value="">-- Select Payment Method --</option>
            <option value="cash">Cash</option>
            <option value="gcash">GCash</option>
        </select>
        <button type="submit">Confirm All Orders</button>
    </form>
</div>

</body>
</html>
