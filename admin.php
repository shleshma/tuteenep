<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tutor_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminUsername = $_POST['username'];
    $adminPassword = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM tbladmin WHERE BINARY admin_username = :username AND BINARY admin_password = :password");
    $stmt->bindParam(':username', $adminUsername);
    $stmt->bindParam(':password', $adminPassword);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Set session variable for admin
        $_SESSION['admin_username'] = $adminUsername;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $loginError = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="icon" type="image/svg+xml" href="img/tnlogo.svg">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="login-container">
        <form class="login-form" method="POST" action="admin.php">
            <h2>Admin Login</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <?php if (!empty($loginError)): ?>
                    <p id="passwordMessage" style="color:red;"><?php echo $loginError; ?></p>
                <?php endif; ?>
            </div>
            <center><button type="submit">Log in</button></center>
        </form>
    </div>
</body>
</html>
