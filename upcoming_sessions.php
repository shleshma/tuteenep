<?php
session_start();

if (!isset($_SESSION['tutor_id'])) {
    header("Location: tutor_login.html");
    exit;
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

// Fetch booking requests for this tutor including student and session details
$sql = "
    SELECT 
        b.booking_id, b.session_id, b.student_id, b.booking_date, b.status,
        s.session_date, s.session_time, s.session_duration, s.session_grade, s.session_subject, s.session_location, s.session_note, s.session_type,
        st.student_name, st.student_email, st.student_phone, st.student_grade, st.student_difficulty
    FROM 
        bookings b
    JOIN 
        tblsession s ON b.session_id = s.session_id
    JOIN 
        tblstudent st ON b.student_id = st.student_id
    WHERE 
        s.tutor_id = ? AND b.status IN ('confirmed', 'canceled')
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tutor_id);
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
    <title>Booking Requests</title>
    <link rel="stylesheet" href="css/booking_request.css">
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
        <h2>Booking Requests</h2>
        <?php if ($bookings): ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking">
                    <div class="columns">
                        <div class="column">
                            <h3>Student Details</h3>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['student_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['student_email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['student_phone']); ?></p>
                            <p><strong>Grade:</strong> <?php echo htmlspecialchars($booking['student_grade']); ?></p>
                            <p><strong>Difficulty Level:</strong> <?php echo htmlspecialchars($booking['student_difficulty']); ?></p>
                        </div>
                        <div class="column">
                            <h3>Session Details</h3>
                            <p><strong>Session Date:</strong> <?php echo htmlspecialchars($booking['session_date']); ?></p>
                            <p><strong>Session Time:</strong> <?php echo htmlspecialchars(date('h:i a', strtotime($booking['session_time']))); ?></p>
                            <p><strong>Duration:</strong> <?php echo htmlspecialchars($booking['session_duration']); ?> mins</p>
                            <p><strong>Grade:</strong> <?php echo htmlspecialchars($booking['session_grade']); ?></p>
                            <p><strong>Subject:</strong> <?php echo htmlspecialchars($booking['session_subject']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['session_location']); ?></p>
                            <p><strong>Session Type:</strong> <?php echo htmlspecialchars($booking['session_type']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No booking requests found.</p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="copyright">
            <p>&copy; TuteeNep 2024</p>
        </div>
        <div class="query">
            <p>Have a question? <a href="query.html">Contact Us</a></p>
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
