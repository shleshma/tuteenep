<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/student_signup.css">
</head>
<body>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $grade = $_POST['grade'];
    $difficulty = $_POST['difficulty'];
    $school = $_POST['school']; 
    $error = "";

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Invalid email address.";
    }

    // Validate password
    if (strlen($password) < 8) {
      $error = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[a-zA-Z]/', $password)) {
      $error = "Password must contain at least one letter.";
    } elseif (!preg_match('/[0-9]/', $password)) {
      $error = "Password must contain at least one number.";
    }

    // Validate phone number
    if (!preg_match('/^\d{10}$/', $phone)) {
      $error = "Invalid phone number. Please enter a valid phone number.";
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT student_email FROM tblstudent WHERE student_email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      $error = "Email already exists. Please use a different email.";
    }

    if (empty($error)) {
      $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security
      $stmt = $conn->prepare("INSERT INTO tblstudent (student_name, student_email, student_password, student_phone, student_grade, student_school, student_difficulty) VALUES (:name, :email, :password, :phone, :grade, :school, :difficulty)");
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':password', $password);
      $stmt->bindParam(':phone', $phone);
      $stmt->bindParam(':grade', $grade);
      $stmt->bindParam(':school', $school);
      $stmt->bindParam(':difficulty', $difficulty);

      $stmt->execute();

      echo "<script>alert('Sign up successful!'); window.location.href='student_login.html';</script>";
    } else {
      echo "<script>alert('$error');</script>";
    }
  }
}
catch(PDOException $e) {
  echo "Error: ". $e->getMessage();
}

$conn = null;
?>

<header>
  <div class="logo">
    <a href="home.html"><img src="img/tnlogo.svg" alt="TuteeNep Logo"></a> 
  </div>
  <div class="menu-container">
    <div class="menu-icon" onclick="toggleMenu()">
      <img src="img/menu.webp" alt="Menu">
    </div>
    <nav>
      <ul>
        <li><a href="find_tutor.html">Find Tutors</a></li>
        <li><a href="becomeTutor.html">Become a Tutor</a></li>
        <li><a href="login.html">Log In</a></li>
      </ul>
    </nav>
  </div>
</header>

<div class="container">
  <form class="signup-container" action="student_signup.php" method="POST">
    <h2>Sign Up as a Student</h2><br>
    <div class="form-group">
      <label for="name">Full Name:</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="form-group">
      <label for="phone">Phone Number:</label>
      <input type="text" id="phone" name="phone" required>
    </div>
     <div class="form-group">
                <label for="grade">Grade Level:</label>
                <select id="grade" name="grade">
                    <option value="">Select Grade</option>
                    <option value="9">Grade 9</option>
                    <option value="10">Grade 10</option>
                    <option value="11 Management">Grade 11 (Management)</option>
                    <option value="12 Management">Grade 12 (Management)</option>
                    <option value="11 Science">Grade 11 (Science)</option>
                    <option value="12 Science">Grade 12 (Science)</option>
                    <option value="A1 Non-Science">A1 (Non-Science)</option>
                    <option value="A2 Non-Science">A2 (Non-Science)</option>
                    <option value="A1 Science">A1 (Science)</option>
                    <option value="A2 Science">A2 (Science)</option>
                </select>
            </div>
             <div class="form-group">
      <label for="difficulty">Primary Subject of Concern:</label>
      <select id="difficulty" name="difficulty"></select>
    </div>
     <div class="form-group">
        <label for="school">School:</label>
        <input type="text" id="school" name="school" required>
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

  
<script type="text/javascript">
    const subjects = {
        '9': ['Science', 'Social Studies', 'Compulsory Mathematics', 'Environment Population and Health', 'Accountancy', 'Computer Science', 'English', 'Optional Mathematics', 'Nepali'],
        '10': ['Science', 'Social Studies', 'Compulsory Mathematics', 'Environment Population and Health', 'Accountancy', 'Computer Science', 'English', 'Optional Mathematics', 'Nepali'],
        '11 Management': ['English', 'Nepali', 'Social Studies', 'Mathematics', 'Accountancy', 'Economics', 'Computer Science', 'Business Mathematics', 'Hotel Management', 'Business Studies'],
        '12 Management': ['English', 'Nepali', 'Social Studies', 'Mathematics', 'Accountancy', 'Economics', 'Computer Science', 'Business Mathematics', 'Hotel Management', 'Business Studies'],
        '11 Science': ['Nepali', 'English', 'Social Studies', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Computer Science'],
        '12 Science': ['Nepali', 'English', 'Social Studies', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Computer Science'],
        'A1 Non-Science': ['English General Paper', 'Accountancy', 'Economics', 'Business Studies', 'Computer Science', ''],
       'A1 Science': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'General Paper (AS)'],
    'A2 Non-Science': ['English General Paper', 'Accountancy', 'Economics', 'Business Studies', 'Computer Science', 'Mathematics'],
    'A2 Science': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'General Paper (AS)']
  };

    const gradeSelect = document.getElementById('grade');
    const subjectSelect = document.getElementById('difficulty');

    gradeSelect.addEventListener('change', function() {
       
        // Clear options in subject select
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';

        // Get selected grade
        const selectedGrade = gradeSelect.value;

        // Populate subjects based on selected grade
        if (selectedGrade in subjects) {
            subjects[selectedGrade].forEach(subject => {
                const option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectSelect.appendChild(option);
            });
        }
    });

  document.getElementById('grade').addEventListener('change', populateSubjects);
  populateSubjects();

  function toggleMenu() {
    var menuIcon = document.querySelector('.menu-icon');
    menuIcon.classList.toggle('clicked');
  }
</script>
</body>
</html>
