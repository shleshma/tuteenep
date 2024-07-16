<?php
session_start();
if (!isset($_SESSION['tutor_id'])) {
    header("Location: tutor_login.html");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Fetch upcoming sessions for the tutor
$tutor_id = $_SESSION['tutor_id'];
$sessions = [];
$stmt = $conn->prepare("SELECT session_id, session_date, session_time, session_duration, session_subject, session_grade, session_location, session_note, group_size, session_type, is_booked FROM tblsession WHERE tutor_id = ? AND session_date >= CURDATE()");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
$sessions = [];
while ($row = $result->fetch_assoc()) {
    $sessions[] = $row;
}
$stmt->close();

// Handle edit and delete actions
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit') {
        $session_id = $_GET['session_id'];
        $stmt = $conn->prepare("SELECT session_date, session_time, session_duration, session_subject, session_grade, session_location, session_note, session_type, group_size FROM tblsession WHERE session_id = ?");
        $stmt->bind_param("i", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $session = $result->fetch_assoc();
        $stmt->close();
    } elseif ($_GET['action'] == 'delete') {
        $session_id = $_GET['session_id'];
        // First, check if the session is booked
        $stmt = $conn->prepare("SELECT is_booked FROM tblsession WHERE session_id = ?");
        $stmt->bind_param("i", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $session = $result->fetch_assoc();
        $stmt->close();
        
        if ($session['is_booked']) {
            // Session is booked, handle accordingly
            echo '<script>alert("This session is booked and cannot be deleted."); window.history.back();</script>';
        } else {
            // Session is not booked, proceed with deletion
            $stmt = $conn->prepare("DELETE FROM tblsession WHERE session_id = ?");
            $stmt->bind_param("i", $session_id);
            $stmt->execute();
            $stmt->close();
            header("Location: sessions.php");
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['updateBtn'])) {
        $session_id = $_POST['session_id'];
        $session_date = $_POST['session_date'];
        $session_time = $_POST['session_time'];
        $session_duration = $_POST['session_duration'];
        $session_subject = $_POST['session_subject'];
        $session_grade = $_POST['session_grade'];
        $session_note = $_POST['session_note'];
        $session_location = $_POST['session_location'];
        $session_type = $_POST['session_type'];
        $group_size = ($session_type == 'One-on-one') ? 1 : $_POST['group_size'];

        // Check for time conflict
        $stmt = $conn->prepare("SELECT session_id FROM tblsession WHERE tutor_id = ? AND session_date = ? AND session_time = ? AND session_id != ?");
        $stmt->bind_param("issi", $tutor_id, $session_date, $session_time, $session_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo '<script>alert("You already have a session scheduled at this date and time."); window.history.back();</script>';
        } else {
            $stmt->close();
            $stmt = $conn->prepare("UPDATE tblsession SET session_date = ?, session_time = ?, session_duration = ?, session_subject = ?, session_grade = ?, session_note = ?, session_location = ?, session_type = ?, group_size = ? WHERE session_id = ?");
            $stmt->bind_param("sssssssssi", $session_date, $session_time, $session_duration, $session_subject, $session_grade, $session_note, $session_location, $session_type, $group_size, $session_id);
            $stmt->execute();
            $stmt->close();
            header("Location: sessions.php");
            exit();
        }
    } else {
        $session_date = $_POST['session_date'];
        $session_time = $_POST['session_time'];
        $session_duration = $_POST['session_duration'];
        $session_subject = $_POST['session_subject'];
        $session_grade = $_POST['session_grade'];
        $session_note = $_POST['session_note'];
        $session_location = $_POST['session_location'];
        $session_type = $_POST['session_type'];
        $group_size = ($session_type == 'One-on-one') ? 1 : $_POST['group_size'];

        // Check for time conflict
        $stmt = $conn->prepare("SELECT session_id FROM tblsession WHERE tutor_id = ? AND session_date = ? AND session_time = ?");
        $stmt->bind_param("iss", $tutor_id, $session_date, $session_time);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo '<script>alert("You already have a session scheduled at this date and time."); window.history.back();</script>';
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO tblsession (tutor_id, session_date, session_time, session_duration, session_subject, session_grade, session_note, session_location, session_type, group_size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississsssi", $tutor_id, $session_date, $session_time, $session_duration, $session_subject, $session_grade, $session_note, $session_location, $session_type, $group_size);
            if ($stmt->execute()) {
                echo "Session created successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
            header("Location: sessions.php");
            exit();
        }
    }
}

// Fetch the tutor's verification status
$stmt = $conn->prepare("SELECT verification_status FROM tbltutor WHERE tutor_id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
$tutor_data = $result->fetch_assoc();
$verification_status = $tutor_data['verification_status'];
$stmt->close();

// Check if tutor's verification status is approved
if ($verification_status !== 'approved') {
    echo '<script>';
    echo 'alert("Creating sessions is only accessible to tutors whose registration process has been approved.");';
    echo 'window.location.href = "tutor_dashboard.php";';
    echo '</script>';
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tutoring Sessions</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/sessions.css">
</head>
<body>
  <header>
    <div class="logo">
      <a href="tutor_dashboard.php"><img src="img/tnlogo.svg" alt="TuteeNep Logo"></a>
    </div>
    <div class="menu-container">
      <div class="menu-icon" onclick="toggleMenu()">
        <img src="img/menu.webp" alt="Menu">
      </div>
      <nav>
        <ul>
          <li><a href="registration.php">Register</a></li>
          <li><a href="sessions.php">Sessions</a></li>         
          <li><a href="booking_request.php">Booking Requests</a></li>
          <li><a href="update_tutor.php">Your Profile</a></li>
          <li><a href="logout_tutor.php">Log Out</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <div class="form-container">
      <h2>Create Tutoring Session</h2>
      <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" id="sessionForm">
        <?php if (isset($session)): ?>
          <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
        <?php endif; ?>
        <div class="form-group">
          <label for="session_date">Date:</label>
          <input type="date" id="session_date" name="session_date" min="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
          <label for="session_time">Time:</label>
          <input type="time" id="session_time" name="session_time" value="<?php echo isset($session) ? date('H:i', strtotime($session['session_time'])) : ''; ?>" required>
        </div>
        <div class="form-group">
          <label for="session_duration">Duration:</label>
          <input type="text" id="session_duration" name="session_duration" value="<?php echo isset($session) ? $session['session_duration'] : ''; ?>" required>
        </div>
        <div class="form-group">
      <label for="session_grade">Grade Level:</label>
      <select id="session_grade" name="session_grade">
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
      <label for="session_subject">Subject:</label>
      <select id="session_subject" name="session_subject"></select>
    </div>   
     <div class="form-group">
      <label for="session_location">Location:</label>
      <input type="text" id="session_location" name="session_location" value="<?php echo isset($session) ? $session['session_location'] : ''; ?>" required>
    </div>
    <div class="form-group">
      <label for="session_type">Type:</label>
      <select id="session_type" name="session_type" onchange="checkSessionType()">
        <option value="One-on-one" <?php echo isset($session) && $session['session_type'] == 'One-on-one' ? 'selected' : ''; ?>>One-on-one</option>
        <option value="Group" <?php echo isset($session) && $session['session_type'] == 'Group' ? 'selected' : ''; ?>>Group</option>
      </select>
    </div>
    <div class="form-group" id="group_size_div" style="display: <?php echo isset($session) && $session['session_type'] == 'Group' ? 'block' : 'none'; ?>">
      <label for="group_size">Group Size:</label>
      <input type="number" id="group_size" name="group_size" value="<?php echo isset($session) ? $session['group_size'] : ''; ?>">
    </div>
    <div class="form-group">
      <label for="session_note">Note:</label>
      <textarea id="session_note" name="session_note"><?php echo isset($session) ? $session['session_note'] : ''; ?></textarea>
    </div>
    <div class="form-group">
      <button type="submit" name="<?php echo isset($session) ? 'updateBtn' : 'createBtn'; ?>">
        <?php echo isset($session) ? 'Update Session' : 'Create Session'; ?>
      </button>
    </div>
  </form>
</div>

<div class="sessions-container">
  <h2>Your Upcoming Sessions</h2>
  <?php if (empty($sessions)): ?>
    <p>No upcoming sessions found. <a href="#sessionForm">Create your tutoring session now.</a></p>
  <?php else: ?>
    <ul>
      <?php foreach ($sessions as $session): ?>
        <li>
          <strong><?php echo $session['session_subject']; ?></strong><br><br>
          Date: <?php echo $session['session_date']; ?><br>
          Time: <?php echo date('h:i A', strtotime($session['session_time'])); ?><br>
          Duration: <?php echo $session['session_duration']; ?><br>
          Grade: <?php echo $session['session_grade']; ?><br>
          Location: <?php echo $session['session_location']; ?><br>
          Type: <?php echo $session['session_type']; ?><br>
          Group Size: <?php echo $session['group_size']; ?><br>
          Note: <?php echo $session['session_note']; ?><br><br>
          <a href="?action=edit&session_id=<?php echo $session['session_id']; ?>">Edit</a> |
          <a href="?action=delete&session_id=<?php echo $session['session_id']; ?>" onclick="return confirm('Are you sure you want to delete this session?')">Delete</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

  </ul>
</div>
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
    'A1 Non-Science': ['English General Paper', 'Accountancy', 'Economics', 'Business Studies', 'Computer Science', 'Mathematics'],
    'A1 Science': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'General Paper (AS)'],
    'A2 Non-Science': ['English General Paper', 'Accountancy', 'Economics', 'Business Studies', 'Computer Science', 'Mathematics'],
    'A2 Science': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'General Paper (AS)']
  };

  function populateSubjects() {
    const grade = document.getElementById('session_grade');
    const selectedGrade = grade.value;
    const subjectOptions = subjects[selectedGrade];
    const subjectSelect = document.getElementById('session_subject');
    subjectSelect.innerHTML = '';
    subjectOptions.forEach(subject => {
      const option = document.createElement('option');
      option.setAttribute('value', subject);
      option.textContent = subject;
      subjectSelect.appendChild(option);
    });
  }

  document.getElementById('session_grade').addEventListener('change', populateSubjects);
  populateSubjects();

  function toggleMenu() {
    var menuIcon = document.querySelector('.menu-icon');
    menuIcon.classList.toggle('clicked');
  }

  function checkSessionType() {
  var sessionType = document.getElementById('session_type').value;
  var groupSizeDiv = document.getElementById('group_size_div');
  if (sessionType === 'Group') {
    groupSizeDiv.style.display = 'block';
  } else {
    groupSizeDiv.style.display = 'none';
    document.getElementById('group_size').value = 1;
  }
}
</script>
</body>
</html>
</script>
</body>
</html>
