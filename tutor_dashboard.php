<?php
session_start();
if (!isset($_SESSION['tutor_id'])) {
  header("Location: tutor_login.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tutor Dashboard</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/tutor_dashboard.css">
</head>
<body>
  <header>
    <div class="logo">
      <a href="tutor_dashboard.php"><img src="img/tnlogo.svg" alt="TuteeNep Logo"></a>
    </div>
    <div class="menu-container">
      <div class="menu-icon" onclick="toggleMenu()">
        <img src="img/menu.webp" alt="Menu">
      </div>
      <nav>
        <ul>
          <li><a href="registration.php">Register</a></li>
          <li><a href="sessions.php">Sessions</a></li>         
          <li><a href="booking_request.php">Booking Requests</a></li>
          <li><a href="update_tutor.php">Your Profile</a></li>
          <li><a href="logout_tutor.php">Log Out</a></li>
        </ul>
      </nav>
    </div>
  </header>

 <div class="container">
    <h1>Tutor Profile</h1>
    <h2>Welcome, <?php echo $_SESSION['tutor_name']; ?>!</h2>
    <div class="dashboard-options">
      <div class="option">
        <a href="registration.php">
          <img src="img/register.jpg" alt="Registration">
          <h3>Registration Form</h3>
        </a>
      </div>
       <div class="option">
        <a href="sessions.php">
          <img src="img/session.avif" alt="Session">
          <h3>Sessions</h3>
        </a>
      </div>
       <div class="option">
        <a href="booking_request.php">
          <img src="img/booking.png" alt="Booking">
          <h3>Booking Requests</h3>
        </a>
      </div>
      <div class="option">
        <a href="update_tutor.php">
          <img src="img/updatetutor.webp" alt="Update Profile">
          <h3>Your Profile</h3>
        </a>
      </div>
      <div class="option">
        <a href="logout_tutor.php">
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
