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

    $stmt = $conn->prepare("SELECT * FROM tblstudent WHERE student_email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && password_verify($password, $student['student_password'])) {
      $_SESSION['student_id'] = $student['student_id'];
      $_SESSION['student_name'] = $student['student_name'];
      echo "<script>alert('Login successful!'); window.location.href='student_dashboard.php';</script>";
    } else {
      echo "<script>alert('Invalid email or password.'); window.location.href='student_login.html';</script>";
    }
  }
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

$conn = null;
?>
