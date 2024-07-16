<?php
session_start();
if (!isset($_SESSION['student_id'])) {
  header("Location: student_login.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/student_dashboard.css">
</head>
<body>
  <header>
    <div class="logo">
      <a href="student_dashboard.php"><img src="img/tnlogo.svg" alt="TuteeNep Logo"></a>
      </div>
      <div class="menu-container">
    <div class="menu-icon" onclick="toggleMenu()">
      <img src="img/menu.webp" alt="Menu">
    </div>
      <nav>
      <ul>
        <li><a href="findtutors.php">Find Tutors</a></li>
        <li><a href="booking.php">Your Bookings</a></li>
        <li><a href="update_student.php">Your Profile</a></li>
        <li><a href="logout.php">Log Out</a></li>
      </ul>
    </nav>
  </div>
  </header>

  <div class="container">
    <h1>Student Profile</h1>
    <h2>Welcome, <?php echo $_SESSION['student_name']; ?>!</h2>
    <div class="dashboard-options">
      <div class="option">
        <a href="findtutors.php">
          <img src="img/find.png" alt="Find Tutors">
          <h3>Find Tutors</h3>
        </a>
      </div>
       <div class="option">
        <a href="booking.php">
          <img src="img/session.avif" alt="Bookings">
          <h3>Your Bookings</h3>
        </a>
      </div>
      <div class="option">
        <a href="update_student.php">
          <img src="img/update.png" alt="Update Profile">
          <h3>Your Profile</h3>
        </a>
      </div>
      <div class="option">
        <a href="logout.php">
          <img src="img/logout.png" alt="Log Out">
          <h3>Log Out</h3>
        </a>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="copyright">
        <p style="color: #338">&copy; TuteeNep 2024</p>
    </div>
    <div class="query">
            <p style="font-size: 14px;">"Empowering Education, Connecting Futures"</p>
    </div>
</footer>

  <script type="text/javascript">
    function toggleMenu() {
      var menuIcon = document.querySelector('.menu-icon');
      menuIcon.classList.toggle('clicked');
    }
  </script>
</body>
</html>
