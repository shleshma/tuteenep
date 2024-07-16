<?php
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $tutor_id = $_POST['tutor_id'];
  $action = $_POST['action']; // 'approve' or 'reject'

  if ($action == 'approve') {
    $status = 'approved';
  } elseif ($action == 'reject') {
    $status = 'rejected';
  }

  $stmt = $conn->prepare("UPDATE tbltutor SET verification_status = ? WHERE tutor_id = ?");
  $stmt->bind_param("si", $status, $tutor_id);
  
  if ($stmt->execute()) {
    echo '<script>';
        echo 'alert("Tutor status updated successfully.");';
        echo 'window.location.href = "admin_dashboard.php";';
        echo '</script>';
  } else {
    echo "Error updating tutor status: " . $stmt->error;
  }
  
  $stmt->close();
}

$conn->close();
?>
