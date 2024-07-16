<?php
session_start();
if (!isset($_SESSION['tutor_id'])) {
    header("Location: tutor_login.html");
    exit();
}

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

$tutor_id = $_SESSION['tutor_id'];
$sql = "SELECT photo_url, tutor_name, tutor_email, address, degree, university, graduation_year, subject, years_experience, description, verification_status FROM tbltutor WHERE tutor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$stmt->bind_result($photo_url, $tutor_name, $tutor_email, $address, $degree, $university, $graduation_year, $subject, $years_experience, $description, $verification_status);
$stmt->fetch();
$stmt->close();

// Check if tutor's verification status is approved
if ($verification_status !== 'approved') {
    // Redirect to an error page or show an error message
    echo '<script>';
        echo 'alert("Profile updates are only accessible to tutors whose registration process has been approved.");';
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
  <title>Update Tutor Profile</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/update_tutor.css">
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
      <h2>Update Profile</h2>
      <form action="process_update.php" method="POST" id="tutorForm" enctype="multipart/form-data">

<div class="form-group">
  <label for="photo">Photo:</label>
  <div class="photo-upload">
    <input type="file" id="photo" name="photo" style="display: none;">
    <label for="photo" class="photo-label">
      <?php if (!empty($photo_url)) {?>
        <img src="<?php echo htmlspecialchars($photo_url);?>" alt="Tutor Photo" width="100">
      <?php } else {?>
        <i class="fas fa-camera" aria-hidden="true"></i>
        <span> + Add Your Photo</span>
      <?php }?>
    </label>
  </div>
</div>
        <div class="personal-info">
          <h3>Personal Information</h3>
          <div class="form-group">
            <label for="tutor_name">Full Name:</label>
            <input type="text" id="tutor_name" name="tutor_name" value="<?php echo htmlspecialchars($tutor_name); ?>" required>
          </div>
          <div class="form-group">
            <label for="tutor_email">Email:</label>
            <input type="email" id="tutor_email" name="tutor_email" value="<?php echo htmlspecialchars($tutor_email); ?>" required readonly>
          </div>
          <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
          </div>
        </div>
        <div class="education">
          <h3>Education</h3>
          <div class="form-group">
            <label for="degree">Degree:</label>
            <input type="text" id="degree" name="degree" value="<?php echo htmlspecialchars($degree); ?>" required>
          </div>
          <div class="form-group">
            <label for="university">University/Institution:</label>
            <input type="text" id="university" name="university" value="<?php echo htmlspecialchars($university); ?>">
          </div>
          <div class="form-group">
            <label for="graduation_year">Graduation Year:</label>
            <input type="text" id="graduation_year" name="graduation_year" value="<?php echo htmlspecialchars($graduation_year); ?>">
          </div>
        </div>
        <div class="experience">
          <h3>Teaching Experience</h3>
          <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
          </div>
          <div class="form-group">
            <label for="years_experience">Years of Experience:</label>
            <input type="number" id="years_experience" name="years_experience" value="<?php echo htmlspecialchars($years_experience); ?>">
          </div>
          <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <input type="submit" id="submitBtn" name="submitBtn" value="Update">
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
