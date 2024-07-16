<?php
session_start();
if (!isset($_SESSION['tutor_id'])) {
    header("Location: tutor_login.html");
    exit();
}

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
    $tutor_id = $_SESSION['tutor_id'];
    $tutor_email = $_POST['tutor_email'];
    $tutor_name = $_POST['tutor_name'];
    $address = $_POST['address'];
    $degree = $_POST['degree'];
    $university = $_POST['university'];
    $graduation_year = $_POST['graduation_year'];
    $subject = $_POST['subject'];
    $years_experience = $_POST['years_experience'];
    $description = $_POST['description'];

    // Handle file upload
    if ($_FILES['photo']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check === false) {
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["photo"]["size"] > 500000) {
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_url = $target_file;

                // Update the tutor's profile with new photo URL
                $update_stmt = $conn->prepare("UPDATE tbltutor SET tutor_name = ?, tutor_email = ?, address = ?, degree = ?, university = ?, graduation_year = ?, subject = ?, years_experience = ?, description = ?, photo_url = ? WHERE tutor_id = ?");
                $update_stmt->bind_param("sssssisissi", $tutor_name, $tutor_email, $address, $degree, $university, $graduation_year, $subject, $years_experience, $description, $photo_url, $tutor_id);

                // Execute the update statement
                if ($update_stmt->execute()) {
                    echo '<script>';
                    echo 'alert("Profile updated successfully.");';
                    echo 'window.location.href = "tutor_dashboard.php";';
                    echo '</script>';
                } else {
                    echo "Error: " . $update_stmt->error;
                }

                $update_stmt->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // Update the tutor's profile without changing the photo URL
        $update_stmt = $conn->prepare("UPDATE tbltutor SET tutor_name = ?, tutor_email = ?, address = ?, degree = ?, university = ?, graduation_year = ?, subject = ?, years_experience = ?, description = ? WHERE tutor_id = ?");
        $update_stmt->bind_param("sssssisisi", $tutor_name, $tutor_email, $address, $degree, $university, $graduation_year, $subject, $years_experience, $description, $tutor_id);

        // Execute the update statement
        if ($update_stmt->execute()) {
            echo '<script>';
            echo 'alert("Profile updated successfully.");';
            echo 'window.location.href = "tutor_dashboard.php";';
            echo '</script>';
        } else {
            echo "Error: " . $update_stmt->error;
        }

        $update_stmt->close();
    }
}

$conn->close();
?>
