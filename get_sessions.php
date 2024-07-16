<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['tutor_id'])) {
    $tutor_id = $_GET['tutor_id'];

    // Updated query to fetch sessions with available seats
    $sql = "SELECT * FROM tblsession WHERE tutor_id = ? AND session_date >= CURDATE() AND is_booked = 0 ";
    $sql .= "AND (group_size IS NULL OR group_size > (SELECT COUNT(*) FROM tblbooking WHERE tblbooking.session_id = tblsession.session_id AND tblbooking.status = 'accepted'))";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tutor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sessions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    echo json_encode($sessions);
} else {
    echo json_encode([]);
}

$conn->close();
?>
