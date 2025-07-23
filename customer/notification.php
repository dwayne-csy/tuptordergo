<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['user_id'];

// Delete single notification
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $notification_id = intval($_GET['delete']);
    $deleteStmt = $conn->prepare("DELETE FROM notifications WHERE id = ? AND customer_id = ?");
    $deleteStmt->bind_param("ii", $notification_id, $customer_id);
    $deleteStmt->execute();
    header("Location: notification.php");
    exit;
}

// Delete all notifications
if (isset($_GET['delete_all'])) {
    $deleteAllStmt = $conn->prepare("DELETE FROM notifications WHERE customer_id = ?");
    $deleteAllStmt->bind_param("i", $customer_id);
    $deleteAllStmt->execute();
    header("Location: notification.php");
    exit;
}

// Fetch notifications
$sql = "SELECT id, message, created_at FROM notifications WHERE customer_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Notifications</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f7fb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2b6777;
        }

        .notification {
            background-color: #dff6ff;
            border-left: 6px solid #2b6777;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            color: #333;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            position: relative;
        }

        .notification small {
            color: #666;
            display: block;
            margin-top: 6px;
            font-size: 13px;
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            color: red;
            cursor: pointer;
        }

        .no-data {
            text-align: center;
            color: #999;
            font-style: italic;
            margin-top: 30px;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #2b6777;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #1c4654;
        }

        .delete-all-btn {
            display: block;
            width: 150px;
            margin: 30px auto 0;
            background-color: #ff4d4d;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
        }

        .delete-all-btn:hover {
            background-color: #cc0000;
        }

        @media screen and (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            .notification {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <form action="dashboard.php" method="get">
        <button class="back-btn">← Back</button>
    </form>

    <div class="container">
        <h2>My Notifications</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="notification">
                    <?= htmlspecialchars($row['message']) ?>
                    <small><?= date('M d, Y H:i', strtotime($row['created_at'])) ?></small>
                    <form method="get" style="position:absolute; top: 10px; right: 15px;">
                        <button class="delete-btn" name="delete" value="<?= $row['id'] ?>" title="Delete Notification">🗑️</button>
                    </form>
                </div>
            <?php endwhile; ?>
            <form method="get">
                <button class="delete-all-btn" name="delete_all" onclick="return confirm('Delete all notifications?')">Delete All 🗑️</button>
            </form>
        <?php else: ?>
            <p class="no-data">You have no notifications yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
