<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Retrieve user information from the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "technical4_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$userID = $_SESSION['userID'];

$query = "SELECT * FROM users WHERE ID = $userID";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) < 1) {
    die("Error retrieving user information");
}

$user = mysqli_fetch_assoc($result);

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['current-password']) && isset($_POST['new-password']) && isset($_POST['confirm-password'])) {
    $currentPassword = mysqli_real_escape_string($conn, $_POST['current-password']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new-password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm-password']);

    // Verify current password
    $hashedCurrentPassword = hash('sha256', $currentPassword);
    if (hash_equals($hashedCurrentPassword, $user['Password'])) {
        // Verify new password and confirm password match
        if ($newPassword === $confirmPassword) {
            // Encrypt the new password with SHA2
            $hashedNewPassword = hash('sha256', $newPassword);

            // Update the password in the database
            $updateQuery = "UPDATE users SET Password = '$hashedNewPassword' WHERE ID = $userID";

            if (mysqli_query($conn, $updateQuery)) {
                mysqli_close($conn);
                header("Location: admin_home.php");
                exit;
            } else {
                echo "Error updating password: " . mysqli_error($conn);
            }
        } else {
            echo "New password and confirm password do not match";
        }
    } else {
        echo "Incorrect current password";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Admin Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 20px auto;
            width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
        }
        .profile-img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 15%;
            margin-left: 5px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            margin-left: 5px;
        }
        h2 {
            margin-left: 5px;
        }
        p {
            margin-left: 5px;
        }
        input[type="password"] {
            width: 95%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 8px 16px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST">
            <h2>Reset Password</h2>
            <img class="profile-img" src="data:image/jpeg;base64,<?php echo base64_encode($user['Image']); ?>" alt="Profile Picture">
            <p><b>Full Name:</b><?php echo ' ' . $user['Firstname'] . ' ' . $user['Middlename'] . ' ' . $user['Lastname']; ?></p>
            <p><b>User Level:</b> <?php echo $user['AccessLevel']; ?></p>
            <p><b>Birthday:</b> <?php echo $user['Birthday']; ?></p>
            <p><b>Mobile Number:</b> <?php echo $user['MobileNumber']; ?></p>
            <p><b>Email:</b> <?php echo $user['Email']; ?></p><br>
            
            <label for="current-password">Current Password</label>
            <input type="password" id="current-password" name="current-password" required>

            <label for="new-password">New Password</label>
            <input type="password" id="new-password" name="new-password" required>

            <label for="confirm-password">Confirm New Password</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <button type="submit">Reset Password</button>
            <button class="back-btn" type="button" onclick="history.back()">Cancel</button>
        </form><br>
    </div>
</body>
</html>
