<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.html");
    exit;
}

$student_id = $_SESSION['student_id'];

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

// Get the bookings for the logged-in student
$sql = "
    SELECT 
        b.booking_id, 
        s.session_date, 
        s.session_time, 
        s.session_duration, 
        s.session_grade, 
        s.session_subject, 
        s.session_location, 
        s.session_type,
        s.group_size,
        t.tutor_id, 
        t.tutor_name, 
        t.photo_url, 
        t.tutor_email, 
        t.tutor_phone, 
        t.description, 
        t.subject, 
        t.address,
        b.status
    FROM tblbooking b
    JOIN tblsession s ON b.session_id = s.session_id
    JOIN tbltutor t ON s.tutor_id = t.tutor_id
    WHERE b.student_id = ?
    ORDER BY s.session_date, s.session_time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Bookings</title>
  <link rel="stylesheet" href="css/booking.css">
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
    <h2>Your Bookings</h2>
    <?php if ($bookings): ?>
      <ul class="booking-list">
        <?php foreach ($bookings as $booking): ?>
          <li class="booking-item">
            <img src="<?php echo htmlspecialchars($booking['photo_url']); ?>" alt="Tutor Photo" class="tutor-photo">
            <div class="booking-details">
              <h3><a href="#" class="tutor-name" data-tutor="<?php echo htmlspecialchars(json_encode($booking)); ?>">
                <?php echo htmlspecialchars($booking['tutor_name']); ?>
              </a></h3>
              <p><strong>Date:</strong> <?php echo htmlspecialchars($booking['session_date']); ?></p>
              <p><strong>Time:</strong> <?php echo htmlspecialchars(date('h:i a', strtotime($booking['session_time']))); ?></p>
              <p><strong>Duration:</strong> <?php echo htmlspecialchars($booking['session_duration']); ?></p>
              <p><strong>Grade:</strong> <?php echo htmlspecialchars($booking['session_grade']); ?></p>
              <p><strong>Subject:</strong> <?php echo htmlspecialchars($booking['session_subject']); ?></p>
              <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['session_location']); ?></p>
              <p><strong>Type:</strong> <?php echo htmlspecialchars($booking['session_type']); ?></p>
              <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
              <?php if ($booking['status'] == '<canceled'): ?>
                <p> This booking has been <strong style="color:green;">canceled.</strong> Please contact the tutor for further information.</p>
              <?php elseif ($booking['status'] == 'confirmed'): ?>
                <p> This booking has been <strong style="color:green;">confirmed.</strong> Please contact the tutor for further information.</p>
              <?php endif; ?>
              <form method="post" action="cancel_booking.php" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                </form>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="no-book">
      <p>You have no bookings yet.</p>
    </div>
    <?php endif; ?>
  </div>

  <div id="tutorModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 id="modalTutorName"></h2>
      <img id="modalTutorPhoto" src="" alt="Tutor Photo" class="tutor-photo">
      <p><strong>Email:</strong> <span id="modalTutorEmail"></span></p>
      <p><strong>Phone:</strong> <span id="modalTutorPhone"></span></p>
      <p><strong>Description:</strong> <span id="modalTutorDescription"></span></p>
      <p><strong>Subjects:</strong> <span id="modalTutorSubjects"></span></p>
      <p><strong>Address:</strong> <span id="modalTutorLocation"></span></p>
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
  // Add event listener to .tutor-name elements
  document.querySelectorAll('.tutor-name').forEach(item => {
    item.addEventListener('click', event => {
      event.preventDefault();
      const booking = JSON.parse(item.getAttribute('data-tutor'));
      document.getElementById('modalTutorName').innerText = booking.tutor_name;
      document.getElementById('modalTutorPhoto').src = booking.photo_url;
      document.getElementById('modalTutorEmail').innerText = booking.tutor_email;
      document.getElementById('modalTutorPhone').innerText = booking.tutor_phone;
      document.getElementById('modalTutorDescription').innerText = booking.description;
      document.getElementById('modalTutorSubjects').innerText = booking.subject;
      document.getElementById('modalTutorLocation').innerText = booking.address;
      document.getElementById('tutorModal').style.display = 'block';
    });
  });

  // Add event listener to .close element
  document.querySelector('.close').addEventListener('click', () => {
    document.getElementById('tutorModal').style.display = 'none';
  });

  // Add event listener to window to close modal when clicked outside
  window.addEventListener('click', event => {
    if (event.target == document.getElementById('tutorModal')) {
      document.getElementById('tutorModal').style.display = 'none';
    }
  });

  function toggleMenu() {
    var menuIcon = document.querySelector('.menu-icon');
    menuIcon.classList.toggle('clicked');
  }
</script>

</body>
</html>
