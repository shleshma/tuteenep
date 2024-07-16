<?php
session_start();

if (!isset($_SESSION['tutor_id'])) {
    header("Location: tutor_login.html");
    exit;
}

$booking_id = $_POST['booking_id'];

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

// Get the session ID from the booking
$sql = "SELECT session_id FROM tblbooking WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->bind_result($session_id);
$stmt->fetch();
$stmt->close();

$conn->begin_transaction();

try {
    // Update the booking status to 'canceled'
    $sql = "UPDATE bookings SET status = 'canceled' WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    // Update the session's booking status to '0'
    $sql = "UPDATE tblsession SET is_booked = 0 WHERE session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    header("Location: booking_request.php");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
