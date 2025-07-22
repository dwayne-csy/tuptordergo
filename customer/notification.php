<?php
session_start();
include '../config/db.php'; // Connect to your DB

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

$fullname = $_SESSION['fullname'] ?? 'John Doe';

// Fetch all vendor announcements
$notifications = [];

$sql = "SELECT a.*, u.fullname AS stall_name 
        FROM announcements a 
        JOIN users u ON a.vendor_id = u.id 
        WHERE u.role = 'vendor' 
        ORDER BY a.id DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stall = htmlspecialchars($row['stall_name']);
        $title = htmlspecialchars($row['title']);
         $content = htmlspecialchars($row['message'] ?? $row['content'] ?? '');
        $formatted = "📢 <strong>{$stall}</strong> posted: <em>{$title}</em> — {$content}";
        $notifications[] = $formatted;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notifications</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
    }

    header {
      background-color: #ff7f50;
      color: white;
      padding: 15px 20px;
      text-align: center;
      font-size: 22px;
      font-weight: bold;
    }

    .container {
      padding: 20px;
      max-width: 900px;
      margin: 0 auto;
    }

    .notification {
      background-color: white;
      border-left: 5px solid #ff7f50;
      margin-bottom: 15px;
      padding: 15px 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      border-radius: 6px;
      font-size: 16px;
    }

    .back-button {
      position: fixed;
      top: 20px;
      left: 20px;
    }

    .back-button button {
      padding: 10px 16px;
      background-color: #e67e22;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
    }

    .back-button button:hover {
      background-color: #d35400;
    }
  </style>
</head>
<body>
  <!-- Back Button -->
  <div class="back-button">
    <form action="dashboard.php" method="get">
      <button type="submit">← Back</button>
    </form>
  </div>

  <header>📢 Notifications</header>
  <div class="container">
    <?php if (empty($notifications)): ?>
      <div class="notification">No announcements yet.</div>
    <?php else: ?>
      <?php foreach ($notifications as $note): ?>
        <div class="notification">
          <?= $note ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
