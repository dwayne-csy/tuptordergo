<?php
session_start();
include '../../config/db.php';

// Check if customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../../login.php');
    exit;
}

// Fetch available stalls (vendors with products)
$stall_sql = "SELECT DISTINCT u.id, u.fullname 
              FROM users u 
              JOIN products p ON u.id = p.vendor_id 
              WHERE u.role = 'vendor'";
$stalls_result = $conn->query($stall_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reserve Table</title>
    <style>
        form {
            max-width: 400px;
            margin: auto;
            padding: 1rem;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        select, input[type="submit"], input[type="text"], input[type="date"], input[type="time"], button {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
        }
        .view-btn {
            margin-top: 20px;
            display: block;
            text-align: center;
        }
        .view-btn a {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .view-btn a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Reserve a Table</h2>

<form method="POST" action="process_reservation.php">
    <label for="stall">Select Stall:</label>
    <select name="stall" id="stall" required>
        <option value="">-- Select Stall --</option>
        <?php while ($row = $stalls_result->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['fullname']) ?></option>
        <?php endwhile; ?>
    </select>

    <label for="product">Select Product:</label>
    <select name="product" id="product" required>
        <option value="">-- Select Product --</option>
    </select>

    <label for="message">Message (optional):</label>
    <input type="text" name="message" id="message">

    <label for="table_number">Table Number:</label>
    <input type="text" name="table_number" id="table_number" required>

    <label for="date">Reservation Date:</label>
    <input type="date" name="date" id="date" required>

    <label for="time">Reservation Time:</label>
    <input type="time" name="time" id="time" required>

    <input type="submit" value="Reserve">
</form>

<div class="view-btn">
    <a href="my_reservations.php">📄 View My Reservations</a>
</div>

<div class="view-btn">
    <a href="../dashboard.php">🔙 Back to Dashboard</a>
</div>

<script>
document.getElementById('stall').addEventListener('change', function () {
    const vendorId = this.value;
    const productSelect = document.getElementById('product');
    productSelect.innerHTML = '<option value="">Loading...</option>';

    fetch('fetch_products.php?vendor_id=' + vendorId)
        .then(response => response.json())
        .then(data => {
            productSelect.innerHTML = '<option value="">-- Select Product --</option>';
            if (data.length > 0) {
                data.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = product.name + ' (₱' + parseFloat(product.price).toFixed(2) + ')';
                    productSelect.appendChild(option);
                });
            } else {
                productSelect.innerHTML = '<option value="">No products available</option>';
            }
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            productSelect.innerHTML = '<option value="">Error loading products</option>';
        });
});
</script>

</body>
</html>
