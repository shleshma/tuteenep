<?php
session_start();
if (!isset($_SESSION['tutor_id'])) {
  header("Location: tutor_login.html");
  exit();
}

$tutor_id = $_SESSION['tutor_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if tutor just signed up (has pending status) and has not yet submitted registration form
$stmt = $conn->prepare("SELECT verification_status FROM tbltutor WHERE tutor_id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$stmt->bind_result($verification_status);
$stmt->fetch();
$stmt->close();

// Allow access to the registration form if verification_status is 'pending'
if  ($verification_status === 'approved') {
  echo '<script>';
  echo 'alert("Your registration has been approved. Now your tutor profile will be visible to the students. Please update your profile accordingly.");';
  echo 'window.location.href = "update_tutor.php";';
  echo '</script>';
  exit();
} elseif ($verification_status === 'rejected') {
  echo '<script>';
  echo 'alert("Your registration has been rejected. Please contact support for further information.");';
  echo 'window.location.href = "tutor_dashboard.php";';
  echo '</script>';
  exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tutor Registration Form</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/register.css">
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
    <div class="form-container">
      <h2>Tutor Registration Form</h2>
      <form action="register.php" method="POST" id="tutorForm" enctype="multipart/form-data">
        <div class="personal-info">
          <h3>Personal Information</h3>
          <div class="form-group">
            <label for="tutor_name">Full Name:</label>
            <input type="text" id="tutor_name" name="tutor_name" required>
          </div>
          <div class="form-group">
            <label for="tutor_email">Email:</label>
            <input type="email" id="tutor_email" name="tutor_email" required>
          </div>
          <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
          </div>
        </div>
        <div class="education">
          <h3>Education</h3>
          <div class="form-group">
            <label for="degree">Degree:</label>
            <input type="text" id="degree" name="degree" required>
          </div>
          <div class="form-group">
            <label for="university">University/Institution:</label>
            <input type="text" id="university" name="university">
          </div>
          <div class="form-group">
            <label for="graduation_year">Graduation Year:</label>
            <input type="text" id="graduation_year" name="graduation_year">
          </div>
        </div>
        <div class="experience">
          <h3>Teaching Experience</h3>
          <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject">
          </div>
          <div class="form-group">
            <label for="years_experience">Years of Experience:</label>
            <input type="number" id="years_experience" name="years_experience">
          </div>
          <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>
          </div>
        </div>
        <div class="file-upload">
          <h3>Upload Documents</h3>
          <div class="form-group">
            <label for="cv">Upload CV:</label>
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx,.rtf">
          </div>
          <div class="form-group">
            <label for="certificates">Upload Certificates:</label>
            <input type="file" id="certificates" name="certificates" multiple accept=".pdf,.jpg,.png,.jpeg">
          </div>
        </div>
        <div class="form-group submit">
          <input type="submit" value="Submit" id="submitBtn">
        </div>
      </form>
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
