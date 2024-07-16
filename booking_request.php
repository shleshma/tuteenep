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

// Fetch booking requests for this tutor
$sql_pending = "
    SELECT 
        b.booking_id, b.session_id, b.student_id, b.booking_date, 
        s.session_date, s.session_time, s.session_duration, s.session_grade, s.session_subject, s.session_location, s.session_note,
        st.student_name, st.student_email, st.student_school as student_school, st.student_grade as student_grade, st.student_phone as student_phone
    FROM 
        tblbooking b
    JOIN 
        tblsession s ON b.session_id = s.session_id
    JOIN 
        tblstudent st ON b.student_id = st.student_id
    WHERE 
        s.tutor_id = ? AND b.status = 'pending'
";
$stmt_pending = $conn->prepare($sql_pending);

if ($stmt_pending === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt_pending->bind_param("i", $tutor_id);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();
$pending_bookings = $result_pending->fetch_all(MYSQLI_ASSOC);
$stmt_pending->close();

// Fetch booked sessions for this tutor
$sql_confirmed = "
    SELECT 
        b.booking_id, b.session_id, b.student_id, b.booking_date, b.status,
        s.session_date, s.session_time, s.session_duration, s.session_grade, s.session_subject, s.session_location, s.session_note,
        st.student_name, st.student_email, st.student_school as student_school, st.student_grade as student_grade, st.student_phone as student_phone
    FROM 
        tblbooking b
    JOIN 
        tblsession s ON b.session_id = s.session_id
    JOIN 
        tblstudent st ON b.student_id = st.student_id
    WHERE 
        s.tutor_id = ? AND b.status IN ('confirmed', 'canceled')
";
$stmt_confirmed = $conn->prepare($sql_confirmed);

if ($stmt_confirmed === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt_confirmed->bind_param("i", $tutor_id);
$stmt_confirmed->execute();
$result_confirmed = $stmt_confirmed->get_result();
$confirmed_bookings = $result_confirmed->fetch_all(MYSQLI_ASSOC);
$stmt_confirmed->close();
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
        <div class="booking-requests-container">
            <?php if ($pending_bookings): ?>
                <?php foreach ($pending_bookings as $booking): ?>
                    <div class="booking">
                        <div class="columns">
                            <div class="column">
                                <h3>Student Details</h3>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['student_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['student_email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['student_phone']); ?></p>
                                <p><strong>Grade:</strong> <?php echo htmlspecialchars($booking['student_grade']); ?></p>
                                <p><strong>School:</strong> <?php echo htmlspecialchars($booking['student_school']); ?></p>
                            </div>
                            <div class="column">
                                <h3>Session Details</h3>
                                <p><strong>Session Date:</strong> <?php echo htmlspecialchars($booking['session_date']); ?></p>
                                <p><strong>Session Time:</strong> <?php echo htmlspecialchars(date('h:i a', strtotime($booking['session_time']))); ?></p>
                                <p><strong>Duration:</strong> <?php echo htmlspecialchars($booking['session_duration']); ?></p>
                                <p><strong>Grade:</strong> <?php echo htmlspecialchars($booking['session_grade']); ?></p>
                                <p><strong>Subject:</strong> <?php echo htmlspecialchars($booking['session_subject']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['session_location']); ?></p>
                            </div>
                        </div>
                        <div class="actions">
                            <form action="confirm_booking.php" method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                                <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($booking['session_id']); ?>">
                                <button type="submit" class="confirm">Confirm Booking</button>
                            </form>
                            <form action="cancel_booking.php" method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                                <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($booking['session_id']); ?>">
                                <button type="submit" class="cancel">Cancel Booking</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No booking requests found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <h2>Booked Sessions</h2>
        <div class="booked-sessions-container">
            <?php if ($confirmed_bookings): ?>
                <?php foreach ($confirmed_bookings as $booking): ?>
                    <div class="booking">
                        <div class="columns">
                            <div class="column">
                                <h3>Student Details</h3>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['student_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['student_email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['student_phone']); ?></p>
                                <p><strong>Grade:</strong> <?php echo htmlspecialchars($booking['student_grade']); ?></p>
                                <p><strong>School:</strong> <?php echo htmlspecialchars($booking['student_school']); ?></p>
                            </div>
                            <div class="column">
                                <h3>Session Details</h3>
                                <p><strong>Session Date:</strong> <?php echo htmlspecialchars($booking['session_date']); ?></p>
                                <p><strong>Session Time:</strong> <?php echo htmlspecialchars(date('h:i a', strtotime($booking['session_time']))); ?></p>
                                <p><strong>Duration:</strong> <?php echo htmlspecialchars($booking['session_duration']); ?></p>
                                <p><strong>Grade:</strong> <?php echo htmlspecialchars($booking['session_grade']); ?></p>
                                <p><strong>Subject:</strong> <?php echo htmlspecialchars($booking['session_subject']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['session_location']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No booked sessions found.</p>
            <?php endif; ?>
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
