<?php
include '../config/db.php';

// Fetch customer count
$customer_sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'";
$customer_result = $conn->query($customer_sql);
$customer_count = $customer_result->fetch_assoc()['total'];

// Fetch vendor count
$vendor_sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'vendor'";
$vendor_result = $conn->query($vendor_sql);
$vendor_count = $vendor_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        /* Header */
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .hamburger {
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: white;
        }

        /* Dropdown Menu */
        .dropdown {
            position: absolute;
            top: 60px;
            left: 20px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        .dropdown a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        .dropdown a:hover {
            background-color: #f0f0f0;
        }

        h1 {
            text-align: center;
            margin: 20px;
        }

        .dashboard-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            padding: 20px;
        }

        .card {
            background: white;
            border: 2px solid #ccc;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 300px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .count {
            font-size: 40px;
            color: #007bff;
            margin-bottom: 20px;
        }

        .view-btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .view-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="hamburger" onclick="toggleDropdown()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <h2>Admin Panel</h2>
</div>

<div class="dropdown" id="dropdownMenu">
    <a href="../logout.php">🔓 Logout</a>
</div>

<h1>👨‍💼 Admin Dashboard</h1>
<div class="dashboard-container">
    <!-- Customer Box -->
    <div class="card">
        <h2>👥 Customers</h2>
        <div class="count"><?= $customer_count ?></div>
        <a href="managecustomer.php" class="view-btn">View</a>
    </div>

    <!-- Vendor Box -->
    <div class="card">
        <h2>🏪 Vendors</h2>
        <div class="count"><?= $vendor_count ?></div>
        <a href="managevendor.php" class="view-btn">View</a>
    </div>
</div>

<script>
    function toggleDropdown() {
        const menu = document.getElementById('dropdownMenu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    window.addEventListener('click', function(e) {
        const menu = document.getElementById('dropdownMenu');
        const hamburger = document.querySelector('.hamburger');
        if (!menu.contains(e.target) && !hamburger.contains(e.target)) {
            menu.style.display = 'none';
        }
    });
</script>

</body>
</html>
