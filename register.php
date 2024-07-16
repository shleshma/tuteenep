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
  if (isset($_SESSION['tutor_id'])) {
    $session_email = $_SESSION['tutor_id'];

    // Retrieve form data
    $tutor_name = $_POST['tutor_name'];
    $tutor_email = $_POST['tutor_email'];
    $address = $_POST['address'];
    $degree = $_POST['degree'];
    $university = $_POST['university'];
    $graduation_year = $_POST['graduation_year'];
    $subject = $_POST['subject'];
    $years_experience = $_POST['years_experience'];
    $description = $_POST['description'];

    // Ensure the uploads directory exists
    if (!file_exists('uploads')) {
      mkdir('uploads', 0777, true);
    }

    // Handle file upload for CV
    $cv_uploaded = false;
    $cv_folder = '';
    if ($_FILES['cv']['error'] === UPLOAD_ERR_OK) {
      $cv = $_FILES['cv']['name'];
      $cv_tmp = $_FILES['cv']['tmp_name'];
      $cv_folder = 'uploads/' . basename($cv);
      if (move_uploaded_file($cv_tmp, $cv_folder)) {
        $cv_uploaded = true;
      } else {
        echo "Failed to upload CV.";
      }
    }

    // Handle file uploads for certificates
    $certificates_uploaded = false;
    $certificates = [];

    if (!empty($_FILES['certificates']['name'])) {
      foreach ($_FILES['certificates']['name'] as $key => $name) {
        if ($_FILES['certificates']['error'][$key] === UPLOAD_ERR_OK) {
          $tmp_name = $_FILES['certificates']['tmp_name'][$key];
          $folder = 'uploads/' . basename($name);
          if (move_uploaded_file($tmp_name, $folder)) {
            $certificates[] = $folder;
          } else {
            echo "Failed to upload certificate: $name";
          }
        }
      }
      $certificates_uploaded = !empty($certificates);
    }

    if ($certificates_uploaded) {
      $certificates_list = implode(',', $certificates);
    } else {
      $certificates_list = '';
    }

      // Check if the tutor already exists
      $stmt = $conn->prepare("SELECT tutor_email FROM tbltutor WHERE tutor_email = ?");
      $stmt->bind_param("s", $tutor_email);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->num_rows > 0) {
        // Tutor exists, update their information and set status to pending
        $stmt->close();
        $stmt = $conn->prepare("UPDATE tbltutor SET tutor_name = ?, address = ?, degree = ?, university = ?, graduation_year = ?, subject = ?, years_experience = ?, description = ?, cv = ?, certificates = ?, verification_status = 'pending' WHERE tutor_email = ?");
        $stmt->bind_param("ssssisissss", $tutor_name, $address, $degree, $university, $graduation_year, $subject, $years_experience, $description, $cv_folder, $certificates_list, $tutor_email);
        $stmt->execute();
        $stmt->close();
      } else {
        // Tutor does not exist, insert new record and set status to pending
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO tbltutor (tutor_name, tutor_email, address, degree, university, graduation_year, subject, years_experience, description, cv, certificates, verification_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ssssisissss", $tutor_name, $tutor_email, $address, $degree, $university, $graduation_year, $subject, $years_experience, $description, $cv_folder, $certificates_list);
        $stmt->execute();
        $stmt->close();
      }

      echo '<script>';
      echo 'alert("Your registration has been submitted. Please wait for the registration approval.");';
      echo 'window.location.href = "tutor_dashboard.php";';
      echo '</script>';
    }
  }

$conn->close();
?>