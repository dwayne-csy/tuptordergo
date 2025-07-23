<?php
session_start();
include '../config/db.php';

// Ensure vendor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch vendor data
$query = $conn->prepare("SELECT fullname, email, photo FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$vendor = $result->fetch_assoc();
$query->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);

    // Handle photo upload
    $photo_path = $vendor['photo']; // Keep current photo by default
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = 'vendor_' . $user_id . '_' . time() . '.' . $ext;
        $destination = '../images/' . $filename;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
            $photo_path = $filename; // Save filename only in DB
        } else {
            $error = "Failed to move uploaded file.";
        }
    }

    // Update vendor info
    $stmt = $conn->prepare("UPDATE users SET fullname = ?, photo = ? WHERE id = ?");
    $stmt->bind_param("ssi", $fullname, $photo_path, $user_id);
    if ($stmt->execute()) {
        $_SESSION['fullname'] = $fullname;
        header("Location: editstall.php?success=1");
        exit;
    } else {
        $error = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Stall Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        .profile-photo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .profile-photo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ccc;
        }

        form label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php">
            <button class="back-btn">⬅️</button>
        </a>

        <?php if (!empty($vendor['photo'])): ?>
            <div class="profile-photo">
                <img src="../images/<?= htmlspecialchars($vendor['photo']) ?>" alt="Profile Photo">
            </div>
        <?php endif; ?>

        <h2 style="text-align:center;">Edit Stall Profile</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="message success">✅ Profile updated successfully!</div>
        <?php elseif (isset($error)): ?>
            <div class="message error">❌ <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Stall Name:</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($vendor['fullname']) ?>" required>

            <label>Email:</label>
            <input type="email" value="<?= htmlspecialchars($vendor['email']) ?>" readonly disabled>


            <label>Upload New Photo:</label>
            <input type="file" name="photo" accept="image/*">

            <button type="submit">💾 Save Changes</button>
        </form>
    </div>
</body>
</html>
