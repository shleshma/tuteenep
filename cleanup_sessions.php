<?php
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

// Delete sessions where session_date is in the past
$stmt = $conn->prepare("DELETE FROM tblsession WHERE session_date < CURDATE()");
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Expired sessions have been deleted successfully.";
} else {
    echo "No expired sessions found to delete.";
}

$stmt->close();
$conn->close();
?>
