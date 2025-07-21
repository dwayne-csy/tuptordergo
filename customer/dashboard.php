<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

$fullname = $_SESSION['fullname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard - TUPT OrderGo</title>
   <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      margin: 0;
      background: #f4f4f4;
    }

    .navbar {
      background-color: #e67e22;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      position: relative;
      z-index: 2;
    }

    .hamburger {
      font-size: 26px;
      cursor: pointer;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: -250px;
      width: 250px;
      height: 100%;
      background-color: #fff;
      box-shadow: 2px 0 5px rgba(0,0,0,0.3);
      padding-top: 60px;
      transition: left 0.3s ease;
      z-index: 1;
    }

    .sidebar a {
      display: block;
      padding: 15px;
      color: #333;
      text-decoration: none;
      border-bottom: 1px solid #eee;
    }

    .sidebar a:hover {
      background-color: #f2f2f2;
    }

    .content {
      padding: 2rem;
    }

    .search-container {
      text-align: center;
      margin-bottom: 30px;
    }

    .search-container input[type="text"] {
      width: 50%;
      padding: 12px 20px;
      font-size: 16px;
      border: 2px solid #e67e22;
      border-radius: 25px;
      outline: none;
      transition: 0.3s ease;
    }

    .search-container input[type="text"]:focus {
      border-color: #d35400;
    }

    .stalls {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .stall {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      padding: 1rem;
      text-align: center;
    }

    .stall img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 10px;
    }

    .stall h3 {
      margin: 10px 0;
      color: #e67e22;
    }

    .stall button {
      background-color: #e67e22;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }

    .stall button:hover {
      background-color: #d35400;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="hamburger" onclick="toggleSidebar()">&#9776;</div>
    <div>Welcome, <?php echo htmlspecialchars($fullname); ?>!</div>
  </div>

  <div class="sidebar" id="sidebar">
    <a href="updateprofile.php">Update Profile</a>
    <a href="reservation.php">Reservation</a>
    <a href="reviews.php">Reviews</a>
    <a href="orders.php">Orders</a>
    <a href="#">Notifications</a>
    <a href="../logout.php">Logout</a>
  </div>

  <div class="content">
    <h2 style="text-align:center;">Available Canteen Stalls</h2>

    <div class="search-container">
      <input type="text" id="searchInput" placeholder="Search stalls...">
    </div>

    <div class="stalls" id="stallList">
        <?php
        $stalls = [
          ['Rice Meals', 'food1'],
          ['Noodles & Pasta', 'food2'],
          ['Snacks & Sweets', 'food3'],
          ['Beverages', 'food4'],
          ['Street Foods', 'food5'],
          ['Sandwiches', 'food6'],
          ['Desserts', 'food7'],
          ['Healthy Options', 'food8'],
          ['Breakfast Meals', 'food9'],
          ['Combo Deals', 'food10']
        ];

        $imageUrl = 'https://cdn.pixabay.com/photo/2024/09/23/ai-generated-spices-food-stall-9061583_1280.png';

        foreach ($stalls as $index => $stall) {
            $stallNumber = $index + 1;
            echo "
            <div class='stall'>
              <img src='{$imageUrl}' alt='Stall {$stallNumber}'>
              <h3>Stall {$stallNumber} - {$stall[0]}</h3>
              <button onclick=\"window.location.href='stalls/stall{$stallNumber}.php'\">View</button>
            </div>";
        }
        ?>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.style.left = sidebar.style.left === '0px' ? '-250px' : '0px';
    }

    document.addEventListener('click', function (e) {
      const sidebar = document.getElementById('sidebar');
      const hamburger = document.querySelector('.hamburger');
      if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
        sidebar.style.left = '-250px';
      }
    });

    // Simple search filter for stalls
    document.getElementById("searchInput").addEventListener("keyup", function () {
      const filter = this.value.toLowerCase();
      const stalls = document.querySelectorAll(".stall");

      stalls.forEach(stall => {
        const text = stall.innerText.toLowerCase();
        stall.style.display = text.includes(filter) ? "initial" : "none";
      });
    });
  </script>

</body>
</html>
