<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
  header("Location: student_login.html");
  exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";
$student_id = $_SESSION['student_id'];

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $phone = htmlspecialchars(trim($_POST['phone']));
    $grade = htmlspecialchars(trim($_POST['grade']));
    $difficulty = htmlspecialchars(trim($_POST['difficulty']));
    $school = htmlspecialchars(trim($_POST['school'])); 

    // Prepare the update statement
    $query = "UPDATE tblstudent SET student_name = :name, student_email = :email, student_phone = :phone, student_grade = :grade, student_school = :school, student_difficulty = :difficulty";
    if ($password) {
      $query .= ", student_password = :password";
    }
    $query .= " WHERE student_id = :student_id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':grade', $grade);
    $stmt->bindParam(':difficulty', $difficulty);
    $stmt->bindParam(':school', $school);
    if ($password) {
      $stmt->bindParam(':password', $password);
    }
    $stmt->bindParam(':student_id', $student_id);

    if ($stmt->execute()) {
      // Update session variables if email or name is changed
      $_SESSION['student_email'] = $email;
      $_SESSION['student_name'] = $name;
      echo "<script>alert('Profile updated successfully.'); window.location.href='student_dashboard.php';</script>";
    } else {
      echo "<script>alert('Failed to update profile.');</script>";
    }
  } else {
    // Fetch current student details
    $stmt = $conn->prepare("SELECT * FROM tblstudent WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
      throw new Exception("Student not found.");
    }
  }
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
} catch(Exception $e) {
  echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Student Profile</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/update_student.css">
</head>
<body>
  <header>
    <div class="logo">
      <a href="student_dashboard.php"><img src="img/tnlogo.svg" alt="TuteeNep Logo"></a>
      </div>
      <div class="menu-container">
    <div class="menu-icon" onclick="toggleMenu()">
      <img src="img/menu.webp" alt="Menu">
    </div>
      <nav>
      <ul>
        <li><a href="findtutors.php">Find Tutors</a></li>
        <li><a href="booking.php">Your Bookings</a></li>
        <li><a href="update_student.php">Your Profile</a></li>
        <li><a href="logout.php">Log Out</a></li>
      </ul>
    </nav>
  </div>
  </header>

  <div class="container">
    <form class="update-form" action="update_student.php" method="POST">
      <h2>Update Profile</h2>
      <div class="form-group">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['student_email']); ?>" required>
      </div>
      <div class="form-group">
        <label for="password">Password (leave blank if unchanged):</label>
        <input type="password" id="password" name="password">
      </div>
      <div class="form-group">
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($student['student_phone']); ?>" required>
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
        <input type="text" id="school" name="school" value="<?php echo htmlspecialchars($student['student_school']); ?>" required>
      </div>
          <!-- Subjects options will be dynamically populated based on the selected grade level -->
      <div class="form-group">
        <input type="submit" name="update" value="Update">
      </div>
    </form>
  </div>

 <footer class="footer">
    <div class="copyright">
        <p style="color: #338">&copy; TuteeNep 2024</p>
    </div>
    <div class="query">
            <p style="font-size: 14px;">"Empowering Education, Connecting Futures"</p>
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
