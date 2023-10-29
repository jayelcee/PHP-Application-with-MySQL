<?php
session_start();
// Check if user is logged in
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

// Convert BLOB image data to base64 for display
$imageData = base64_encode($user['Image']);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Home</title>
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
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .add-user-btn {
            margin-bottom: 10px;
        }
        .logout-btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form>
            <h2>User Home Page</h2>
            <p>Welcome <b><?php echo $user['Username']; ?></b>!</p>
            <img class="profile-img" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="Profile Picture">
            <br><button class="upload-btn" type="button" onclick="location.href='user_image.php'">Change Profile Picture</button>
            <p><b>Full Name:</b><?php echo ' ' . $user['Firstname'] . ' ' . $user['Middlename'] . ' ' . $user['Lastname']; ?></p>
            <p><b>User Level:</b> <?php echo $user['AccessLevel']; ?></p>
            <p><b>Birthday:</b> <?php echo $user['Birthday']; ?></p>
            <p><b>Mobile Number:</b> <?php echo $user['MobileNumber']; ?></p>
            <p><b>Email:</b> <?php echo $user['Email']; ?></p>
            
            <button class="reset-btn" type="button" onclick="location.href='user_changepass.php'">Reset Password</button>
            <button class="logout-btn" type="button" onclick="location.href='logout.php'">Logout</button>
        </form><br>
    </div>
</body>
</html>
