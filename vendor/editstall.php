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
</head>
<body>
    <h2>Edit Stall Profile</h2>

    <?php if (isset($_GET['success'])): ?>
        <p style="color:green;">✅ Profile updated successfully!</p>
    <?php elseif (isset($error)): ?>
        <p style="color:red;">❌ <?= $error ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Full Name:</label><br>
        <input type="text" name="fullname" value="<?= htmlspecialchars($vendor['fullname']) ?>" required><br><br>

        <label>Email (not editable):</label><br>
        <input type="email" value="<?= htmlspecialchars($vendor['email']) ?>" readonly><br><br>

        <label>Current Photo:</label><br>
        <?php if (!empty($vendor['photo'])): ?>
            <img src="../images/<?= htmlspecialchars($vendor['photo']) ?>" width="100"><br>
        <?php else: ?>
            <em>No photo uploaded</em><br>
        <?php endif; ?><br>

        <label>Upload New Photo:</label><br>
        <input type="file" name="photo" accept="image/*"><br><br>

        <button type="submit">💾 Save Changes</button>
    </form>

    <br>
    <a href="dashboard.php">
        <button>⬅️ Back</button>
    </a>
</body>
</html>
