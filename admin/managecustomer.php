<?php
include '../config/db.php';

// Handle activate/inactivate action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'deactivate' ? 'inactive' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();
    header("Location: managecustomer.php");
    exit;
}

// Fetch all customers
$sql = "SELECT id, fullname, email, photo, status FROM users WHERE role = 'customer'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 30px;
        }

        .back-btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background-color: #2980b9;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .customer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .customer-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .customer-card:hover {
            transform: translateY(-5px);
        }

        .customer-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 15px;
        }

        .customer-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .customer-email {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .status {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .status.active {
            color: green;
        }

        .status.inactive {
            color: red;
        }

        .toggle-btn {
            padding: 8px 16px;
            border-radius: 8px;
            background: #007bff;
            color: white;
            border: none;
            text-decoration: none;
            font-size: 14px;
        }

        .toggle-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>🛠️ Manage Customers</h1>

<div class="customer-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="customer-card">
            <img src="../images/<?= htmlspecialchars($row['photo'] ?? 'default.jpg') ?>" alt="Customer Photo" class="customer-photo">
            <div class="customer-name"><?= htmlspecialchars($row['fullname']) ?></div>
            <div class="customer-email"><?= htmlspecialchars($row['email']) ?></div>
            <div class="status <?= $row['status'] === 'active' ? 'active' : 'inactive' ?>">
                <?= ucfirst($row['status']) ?>
            </div>
            <?php if ($row['status'] === 'active'): ?>
                <a href="?action=deactivate&id=<?= $row['id'] ?>" class="toggle-btn">Deactivate</a>
            <?php else: ?>
                <a href="?action=activate&id=<?= $row['id'] ?>" class="toggle-btn">Activate</a>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
