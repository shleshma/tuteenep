<?php
session_start();

if (!isset($_SESSION['tutor_id'])) {
    header("Location: tutor_login.html");
    exit;
}

$tutor_id = $_SESSION['tutor_id'];
$booking_id = $_POST['booking_id'];
$session_id = $_POST['session_id'];

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

// Confirm the booking
$conn->begin_transaction();

try {
    // Update the booking status to 'confirmed'
    $sql = "UPDATE tblbooking SET status = 'confirmed' WHERE booking_id = ? AND session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $session_id);
    $stmt->execute();
    $stmt->close();

    // Update the session to mark it as booked
    $sql = "UPDATE tblsession SET is_booked = 1 WHERE session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    header("Location: booking_request.php");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    echo "Failed to confirm booking: " . $e->getMessage();
}

$conn->close();
?>
