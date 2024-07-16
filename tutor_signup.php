<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tutor Sign Up</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/tutor_signup.css">
</head>
<body>

<?php
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "tutor_db";

    try {
        // Create a new PDO connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $error = "";

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT tutor_email FROM tbltutor WHERE tutor_email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Email already exists. Please use a different email.";
        }

        // Validate phone number length
        if (!preg_match('/^\d{10}$/', $phone)) {
            $error = "Invalid phone number. Please enter a valid phone number.";
        }

        if (empty($error)) {
            // Prepare SQL statement to insert data into tbltutor
            $stmt = $conn->prepare("INSERT INTO tbltutor (tutor_name, tutor_email, tutor_phone, tutor_password) 
                                    VALUES (:name, :email, :phone, :password)");
            
            // Bind parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);

            // Hash password before storing
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password);

            // Execute the statement
            $stmt->execute();

            // Store the email in session
            $_SESSION['tutor_email'] = $email;

            echo '<script>alert("Sign-up successful. You can now log in."); window.location.href = "tutor_login.html";</script>';
            exit;
        } else {
            echo "<script>alert('$error');</script>";
        }

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close connection
    $conn = null;
}
?>

<header>
    <div class="logo">
      <a href="home.html"><img src="img/tnlogo.svg" alt="TuteeNep Logo"></a> 
    </div>
    <!-- Menu Icon -->
    <div class="menu-container">
      <div class="menu-icon" onclick="toggleMenu()">
        <img src="img/menu.webp" alt="Menu">
      </div>
      <nav>
        <ul>
          <li><a href="find_tutor.html">Find Tutors</a></li>
          <li><a href="becomeTutor.html">Become a Tutor</a></li>
          <li><a href="about.html">About Us</a></li>
          <li><a href="login.html">Log In</a></li>
        </ul>
      </nav>
    </div>
</header>

<div class="container">
  <form class="signup-container" action="tutor_signup.php" method="POST">
    <h2>Sign Up as a Tutor</h2>
    <div class="form-group">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="phone">Phone Number:</label>
      <input type="text" id="phone" name="phone" required>
    </div>
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="signup">
      <input type="submit" value="Sign Up" name="signup" id="signup">
    </div>
  </form>
</div>

<footer class="footer">
    <div class="copyright">
      <p>Copyright © TuteeNep 2024</p>
    </div>
    <div class="query">
     <p>Learn more <a href="about.html">About Us</a>.</p>
    </div>
  </footer>

<script>
  function toggleMenu() {
    var menuIcon = document.querySelector('.menu-icon');
    menuIcon.classList.toggle('clicked');
  }
</script>
</body>
</html>
