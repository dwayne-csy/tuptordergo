<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stall = $_POST['stall'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, stall, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $stall, $rating, $comment);

    if ($stmt->execute()) {
        $success = "✅ Review submitted successfully!";
    } else {
        $error = "❌ Failed to submit review.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Review Stall - TUPT OrderGo</title>
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

    .review-box {
      background: #fff;
      padding: 30px;
      border-radius: 20px;
      width: 400px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #d35400;
    }

    label {
      font-weight: 600;
      display: block;
      margin-top: 15px;
    }

    select, textarea {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: 2px solid #f39c12;
      margin-top: 5px;
    }

    textarea {
      resize: vertical;
      height: 80px;
    }

    button[type="submit"] {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background: #e67e22;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
    }

    .message {
      text-align: center;
      margin-top: 10px;
      font-weight: bold;
    }

    .success {
      color: green;
    }

    .error {
      color: red;
    }

    .star-rating {
      display: flex;
      gap: 5px;
      font-size: 24px;
      cursor: pointer;
      margin-top: 5px;
    }

    .star {
      color: #ccc;
      transition: color 0.3s;
    }

    .star.selected {
      color: gold;
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

  <div class="review-box">
    <h2>Review a Stall</h2>

    <?php if ($success) echo "<p class='message success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='message error'>$error</p>"; ?>

    <form method="POST" action="">
      <label for="stall">Canteen Stall</label>
      <select name="stall" required>
        <option value="" disabled selected>Select a stall</option>
        <?php for ($i = 1; $i <= 10; $i++): ?>
          <option value="Stall <?= $i ?>">Stall <?= $i ?></option>
        <?php endfor; ?>
      </select>

      <label for="rating">Rating</label>
      <div class="star-rating">
        <input type="hidden" name="rating" id="rating" required>
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <span class="star" data-value="<?= $i ?>">&#9733;</span>
        <?php endfor; ?>
      </div>

      <label for="comment">Comment</label>
      <textarea name="comment" placeholder="Write your feedback..." required></textarea>

      <button type="submit">Submit Review</button>
    </form>
  </div>

  <script>
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    stars.forEach((star, index) => {
      star.addEventListener('click', () => {
        const value = star.getAttribute('data-value');
        ratingInput.value = value;

        stars.forEach((s, i) => {
          s.classList.toggle('selected', i < value);
        });
      });
    });
  </script>
</body>
</html>
