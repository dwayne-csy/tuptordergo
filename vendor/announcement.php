<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header('Location: ../login.php');
    exit;
}

$fullname = $_SESSION['fullname'] ?? 'Vendor';
$vendor_id = $_SESSION['user_id'];
$stall_name = $_SESSION['stall_name'] ?? 'Unknown Stall';

// Insert new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title']) && !empty($_POST['content'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']); // this will go to `message` column

    $stmt = $conn->prepare("INSERT INTO announcements (vendor_id, stall_name, title, message, date_posted) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $vendor_id, $stall_name, $title, $content);
    $stmt->execute();
    $stmt->close();
}

// Fetch announcements for this vendor
$stmt = $conn->prepare("SELECT * FROM announcements WHERE vendor_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$announcements = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Announcements - TUPT OrderGo</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fdfdfd;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
    }

    h1 {
      color: #e67e22;
      text-align: center;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }

    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #e67e22;
      color: #fff;
      text-align: center;
    }

    tr:hover {
      background-color: #f9f9f9;
    }

    .add-form {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .add-form h2 {
      margin-top: 0;
      color: #e67e22;
    }

    .add-form input,
    .add-form textarea {
      padding: 10px;
      margin-bottom: 10px;
      width: 100%;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .add-form button {
      padding: 10px 16px;
      background-color: #27ae60;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
    }

    .add-form button:hover {
      background-color: #1e8449;
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

  <div class="container">
    <h1>Manage Announcements</h1>

    <!-- Add Announcement Form -->
    <div class="add-form">
      <h2>Create New Announcement</h2>
      <form method="post">
        <input type="text" name="title" placeholder="Announcement Title" required>
        <textarea name="content" rows="4" placeholder="Write your announcement here..." required></textarea>
        <button type="submit">Post Announcement</button>
      </form>
    </div>

    <!-- Announcements Table -->
    <table>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Message</th>
        <th>Date</th>
      </tr>
      <?php foreach ($announcements as $announcement): ?>
        <tr>
          <td><?= $announcement['id'] ?></td>
          <td><?= htmlspecialchars($announcement['title']) ?></td>
          <td><?= htmlspecialchars($announcement['message']) ?></td>
          <td><?= $announcement['date_posted'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
