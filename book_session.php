<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.html");
    exit();
}

$student_id = $_SESSION['student_id'];
$session_id = isset($_POST['session_id']) ? $_POST['session_id'] : null;

if ($session_id === null) {
    echo "Invalid session ID.";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO tblbooking (student_id, session_id) VALUES (?, ?)");
$stmt->bind_param("ii", $student_id, $session_id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
     echo '<script>';
                    echo 'alert("Booking successful!");';
                    echo 'window.location.href = "findtutors.php";';
                    echo '</script>';
} else {
    $stmt->close();
    $conn->close();
    echo "Error: " . $conn->error;
}

// Check if the student has already booked this session
$checkStmt = $conn->prepare("SELECT * FROM tblbooking WHERE student_id = ? AND session_id = ?");
$checkStmt->bind_param("ii", $student_id, $session_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    $checkStmt->close();
    $conn->close();
    echo "You have already booked this session.";
    exit();
}

$checkStmt->close();

?>
