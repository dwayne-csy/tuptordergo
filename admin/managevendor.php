<?php
include '../config/db.php';

// Handle activate/inactivate
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'deactivate' ? 'inactive' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'vendor'");
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();

    header("Location: managevendor.php");
    exit;
}

// Fetch vendors
$sql = "SELECT id, fullname, email, photo, status FROM users WHERE role = 'vendor'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vendors</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f9fc;
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

        .vendor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .vendor-card {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }

        .vendor-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #28a745;
            margin-bottom: 15px;
        }

        .vendor-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .vendor-email {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .status {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .status.active {
            color: green;
        }

        .status.inactive {
            color: red;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .view-btn {
            background: #17a2b8;
        }

        .view-btn:hover {
            background: #138496;
        }

        .toggle-btn {
            background: #ffc107;
            color: #000;
        }

        .toggle-btn:hover {
            background: #e0a800;
        }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

<h1>🏪 Manage Stalls</h1>

<div class="vendor-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="vendor-card">
            <img src="../images/<?= htmlspecialchars($row['photo'] ?? 'default.jpg') ?>" alt="Vendor Photo" class="vendor-photo">

            <div class="vendor-name"><?= htmlspecialchars($row['fullname']) ?></div>
            <div class="vendor-email"><?= htmlspecialchars($row['email']) ?></div>
            <div class="status <?= $row['status'] === 'active' ? 'active' : 'inactive' ?>">
                <?= ucfirst($row['status']) ?>
            </div>
            <div class="btn-group">
                <a href="view_vendor_products.php?vendor_id=<?= $row['id'] ?>" class="btn view-btn">View Products</a>
                <?php if ($row['status'] === 'active'): ?>
                    <a href="?action=deactivate&id=<?= $row['id'] ?>" class="btn toggle-btn">Deactivate</a>
                <?php else: ?>
                    <a href="?action=activate&id=<?= $row['id'] ?>" class="btn toggle-btn">Activate</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
