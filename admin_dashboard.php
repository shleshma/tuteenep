<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
  header("Location: admin.php");
  exit();
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: ". $conn->connect_error);
}

// Fetch tutors with pending status
$sql = "SELECT tutor_id, tutor_name, tutor_email, address, degree, university, graduation_year, subject, years_experience, description, cv, certificates FROM tbltutor WHERE verification_status = 'pending'";
$result = $conn->query($sql);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
  <link rel="stylesheet" href="css/admin_dash.css">
</head>
<body>
  <div class="container">
    <h2>Admin Dashboard - Tutor Verification</h2>
    <a href="admin_logout.php" class="logout">Log Out</a><br><br>
    <a href="admin_approval_history.php" class="approval-history"><b>Approval History</b></a><br><br>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Address</th>
          <th>Degree</th>
          <th>University</th>
          <th>Graduation Year</th>
          <th>Subject</th>
          <th>Years of Experience</th>
          <th>Description</th>
          <th>CV</th>
          <th>Certificates</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0) :?>
          <?php while($row = $result->fetch_assoc()) :?>
            <tr>
              <td><?= $row["tutor_name"]?></td>
              <td><?= $row["tutor_email"]?></td>
              <td><?= $row["address"]?></td>
              <td><?= $row["degree"]?></td>
              <td><?= $row["university"]?></td>
              <td><?= $row["graduation_year"]?></td>
              <td><?= $row["subject"]?></td>
              <td><?= $row["years_experience"]?></td>
              <td><?= $row["description"]?></td>
              <td><a href="<?= $row["cv"]?>" target="_blank">View CV</a></td>
              <td>
                <?php foreach (explode(',', $row["certificates"]) as $certificate) :?>
                  <a href="<?= $certificate?>" target="_blank">View Certificate</a><br>
                <?php endforeach;?>
              </td>
              <td>
                <form action="admin_approve.php" method="POST" style="display:inline;">
                  <input type="hidden" name="tutor_id" value="<?= $row["tutor_id"]?>">
                  <input type="hidden" name="action" value="approve">
                  <input type="submit" value="Approve">
                </form>
                <form action="admin_approve.php" method="POST" style="display:inline;">
                  <input type="hidden" name="tutor_id" value="<?= $row["tutor_id"]?>">
                  <input type="hidden" name="action" value="reject">
                  <input type="submit" value="Reject">
                </form>
              </td>
            </tr>
          <?php endwhile;?>
        <?php else :?>
          <tr><td colspan="12">No pending tutor registrations.</td></tr>
        <?php endif;?>
      </tbody>
    </table>
  </div>
</body>
</html>
