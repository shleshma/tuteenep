<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";

session_start();

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM tbltutor WHERE tutor_email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tutor && password_verify($password, $tutor['tutor_password'])) {
      $_SESSION['tutor_id'] = $tutor['tutor_id'];
      $_SESSION['tutor_name'] = $tutor['tutor_name'];
      echo "<script>alert('Login successful!'); window.location.href='tutor_dashboard.php';</script>";
    } else {
      echo "<script>alert('Invalid email or password.'); window.location.href='tutor_login.html';</script>";
    }
  }
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

$conn = null;
?>
