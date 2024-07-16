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

// Fetch tutors with approved or rejected status
$sql = "SELECT tutor_id, tutor_name, tutor_email, verification_status, address, degree, university, graduation_year, subject, years_experience, cv, certificates FROM tbltutor WHERE verification_status IN ('approved', 'rejected')";
$result = $conn->query($sql);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Approval History</title>
    <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
    <link rel="stylesheet" href="css/admin_approval_history.css">
    <script>
        function searchByName() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("approvalHistoryTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Change index to match column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard - Tutor Approval History</h2>
        <a href="admin_logout.php" class="logout">Log Out</a>
        <a href="admin_dashboard.php" class="back">Back to Dashboard</a>
        <br><br>
        <div class="search-container">
            <label for="searchInput">Search by Tutor Name:</label>
            <input type="text" id="searchInput" onkeyup="searchByName()" placeholder="Enter tutor's name...">
        </div>
        <table id="approvalHistoryTable">
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
                    <th>CV</th>
                    <th>Certificates</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0) :?>
                    <?php while($row = $result->fetch_assoc()) :?>
                        <tr>
                            <td><?= htmlspecialchars($row["tutor_name"])?></td>
                            <td><?= htmlspecialchars($row["tutor_email"])?></td>
                            <td><?= htmlspecialchars($row["address"])?></td>
                            <td><?= htmlspecialchars($row["degree"])?></td>
                            <td><?= htmlspecialchars($row["university"])?></td>
                            <td><?= htmlspecialchars($row["graduation_year"])?></td>
                            <td><?= htmlspecialchars($row["subject"])?></td>
                            <td><?= htmlspecialchars($row["years_experience"])?></td>
                             <td><a href="<?= $row["cv"]?>" target="_blank">View CV</a></td>
              <td>
                <?php foreach (explode(',', $row["certificates"]) as $certificate) :?>
                  <a href="<?= $certificate?>" target="_blank">View Certificate</a><br>
                <?php endforeach;?>
              </td>
                            <td><?= htmlspecialchars($row["verification_status"])?></td>
                        </tr>
                    <?php endwhile;?>
                <?php else :?>
                    <tr><td colspan="10">No records found.</td></tr>
                <?php endif;?>
            </tbody>
        </table>
    </div>
</body>
</html>
