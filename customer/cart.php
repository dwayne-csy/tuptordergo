<?php
session_start();
include '../config/db.php';

// Redirect if user not logged in or not a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ 1. Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity']));

    $check_stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result && $check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update_stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $row['id']);
        $update_stmt->execute();
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
    }

    header("Location: cart.php");
    exit;
}

// ✅ 2. Handle cart update actions: increment, decrement, remove
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);
    $action = $_POST['action'];

    if ($action === 'increment') {
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
    } elseif ($action === 'decrement') {
        $check = $conn->prepare("SELECT quantity FROM cart_items WHERE id = ? AND user_id = ?");
        $check->bind_param("ii", $cart_id, $user_id);
        $check->execute();
        $result = $check->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            if ($row['quantity'] > 1) {
                $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity - 1 WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $cart_id, $user_id);
                $stmt->execute();
            }
        }
    } elseif ($action === 'remove') {
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
    }

    header("Location: cart.php");
    exit;
}

// ✅ 3. Fetch cart items for this user
$cart_sql = $conn->prepare("
    SELECT ci.id AS cart_id, ci.quantity, p.id AS product_id, p.name, p.price, p.image_url
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ?
");
$cart_sql->bind_param("i", $user_id);
$cart_sql->execute();
$cart_items = $cart_sql->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .cart-container {
            max-width: 800px;
            margin: auto;
        }
        .cart-item {
            background: #fff;
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .cart-item img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }
        .cart-item-details {
            flex: 1;
        }
        .cart-item-details h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .cart-item-details p {
            color: #777;
            margin-top: 5px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .quantity-controls form {
            display: inline;
            margin: 0 5px;
        }
        .quantity-controls button {
            background: #3498db;
            color: white;
            border: none;
            padding: 6px 10px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .quantity-controls button:hover {
            background: #2980b9;
        }
        .remove-btn form {
            margin-top: 10px;
        }
        .remove-btn button {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 5px 12px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }
        .remove-btn button:hover {
            background: #c0392b;
        }
        .back-btn {
            background: #2ecc71;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background: #27ae60;
        }
        .total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            margin-top: 30px;
            color: #2c3e50;
        }
        .order-btn {
            text-align: right;
            margin-top: 20px;
        }
        .order-btn button {
            background: #f39c12;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        .order-btn button:hover {
            background: #e67e22;
        }
    </style>
</head>
<body>

<div class="cart-container">
    <a href="dashboard.php" class="back-btn">← Back</a>
    <h1>Your Cart</h1>

    <?php
    $grand_total = 0;

    if ($cart_items && $cart_items->num_rows > 0) {
        while ($item = $cart_items->fetch_assoc()) {
            $cart_id = $item['cart_id'];
            $name = htmlspecialchars($item['name']);
            $price = number_format($item['price'], 2);
            $qty = $item['quantity'];
            $image = $item['image_url'] ? $item['image_url'] : 'default-product.jpg';
            $subtotal = $item['price'] * $qty;
            $grand_total += $subtotal;
            $subtotalFormatted = number_format($subtotal, 2);

            echo "
            <div class='cart-item'>
                <img src='../images/$image' alt='$name'>
                <div class='cart-item-details'>
                    <h3>$name</h3>
                    <p>₱$price × $qty = ₱$subtotalFormatted</p>
                    <div class='quantity-controls'>
                        <form method='POST'>
                            <input type='hidden' name='cart_id' value='$cart_id'>
                            <input type='hidden' name='action' value='decrement'>
                            <button>-</button>
                        </form>
                        <span style='font-size: 16px;'>$qty</span>
                        <form method='POST'>
                            <input type='hidden' name='cart_id' value='$cart_id'>
                            <input type='hidden' name='action' value='increment'>
                            <button>+</button>
                        </form>
                    </div>
                    <div class='remove-btn'>
                        <form method='POST'>
                            <input type='hidden' name='cart_id' value='$cart_id'>
                            <input type='hidden' name='action' value='remove'>
                            <button>Remove</button>
                        </form>
                    </div>
                </div>
            </div>";
        }

        // Order now button
        echo "
        <div class='order-btn'>
        <form method='GET' action='cart_checkout.php'>
            <button type='submit' name='cart_checkout'>Order Now</button>
        </form>

        </div>
        ";

        echo "<div class='total'>Total: ₱" . number_format($grand_total, 2) . "</div>";
    } else {
        echo "<p>Your cart is empty.</p>";
    }
    ?>
</div>

</body>
</html>
