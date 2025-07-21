<?php
session_start();
include '../config/db.php';

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = $password_success = $password_error = "";

// Fetch current profile data
$sql = "SELECT fullname, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $email);
$stmt->fetch();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_fullname = $_POST['fullname'];
    $new_email = $_POST['email'];

    $sql = "UPDATE users SET fullname = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $new_fullname, $new_email, $user_id);

    if ($stmt->execute()) {
        $_SESSION['fullname'] = $new_fullname;
        $success = "✅ Profile updated successfully!";
    } else {
        $error = "❌ Failed to update profile.";
    }

    $stmt->close();
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $password_error = "❌ New passwords do not match.";
    } else {
        // Get current hashed password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current, $hashed)) {
            $password_error = "❌ Current password is incorrect.";
        } else {
            $new_hashed = password_hash($new, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_hashed, $user_id);

            if ($stmt->execute()) {
                $password_success = "✅ Password changed successfully!";
            } else {
                $password_error = "❌ Failed to update password.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Update Profile - TUPT OrderGo</title>
  <style>
    body {
      background: #f4f4f4;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
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

    .profile-box {
      background: #fff;
      padding: 30px 40px;
      border-radius: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 360px;
      overflow-y: auto;
      max-height: 90vh;
    }

    h2 {
      text-align: center;
      color: #d35400;
      margin-bottom: 25px;
    }

    label {
      font-weight: 600;
      margin-top: 10px;
      display: block;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: 2px solid #f39c12;
      margin-bottom: 15px;
      font-size: 16px;
    }

    button[type="submit"] {
      width: 100%;
      padding: 12px;
      background: #e67e22;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
    }

    button[type="submit"]:hover {
      background: #d35400;
    }

    .message {
      text-align: center;
      margin-bottom: 15px;
      font-weight: bold;
    }

    .success {
      color: green;
    }

    .error {
      color: red;
    }

    hr {
      margin: 20px 0;
      border: 0;
      border-top: 1px solid #ccc;
    }
  </style>
</head>
<body>

  <!-- Back Button -->
  <div class="back-button">
    <form action="../customer/dashboard.php" method="get">
      <button type="submit">← Back</button>
    </form>
  </div>

  <div class="profile-box">
    <h2>Update Profile</h2>

    <?php if ($success) echo "<p class='message success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='message error'>$error</p>"; ?>

    <form method="POST" action="">
      <input type="hidden" name="update_profile" value="1">
      <label for="fullname">Full Name:</label>
      <input type="text" name="fullname" id="fullname" value="<?= htmlspecialchars($fullname) ?>" required>

      <label for="email">Email:</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>

      <button type="submit">Update Profile</button>
    </form>

    <hr>

    <h2>Change Password</h2>

    <?php if ($password_success) echo "<p class='message success'>$password_success</p>"; ?>
    <?php if ($password_error) echo "<p class='message error'>$password_error</p>"; ?>

    <form method="POST" action="">
      <input type="hidden" name="change_password" value="1">
      <label for="current_password">Current Password:</label>
      <input type="password" name="current_password" id="current_password" required>

      <label for="new_password">New Password:</label>
      <input type="password" name="new_password" id="new_password" required>

      <label for="confirm_password">Confirm New Password:</label>
      <input type="password" name="confirm_password" id="confirm_password" required>

      <button type="submit">Change Password</button>
    </form>
  </div>
</body>
</html>
