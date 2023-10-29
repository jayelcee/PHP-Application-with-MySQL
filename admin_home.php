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
    <title>Admin Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 20px auto;
            width: 95%;
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
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Home Page</h2>
        <form>
            <p>Welcome <b><?php echo $user['Username']; ?></b>!</p>
            <img class="profile-img" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="Profile Picture">
            <br><button class="upload-btn" type="button" onclick="location.href='admin_image.php'">Change Profile Picture</button>
            <p><b>Full Name:</b><?php echo ' ' . $user['Firstname'] . ' ' . $user['Middlename'] . ' ' . $user['Lastname']; ?></p>
            <p><b>User Level:</b> <?php echo $user['AccessLevel']; ?></p>
            <p><b>Birthday:</b> <?php echo $user['Birthday']; ?></p>
            <p><b>Mobile Number:</b> <?php echo $user['MobileNumber']; ?></p>
            <p><b>Email:</b> <?php echo $user['Email']; ?></p>
            
            <h2>Records</h2>
            <button class="add-user-btn" type="button" onclick="location.href='admin_adduser.php'">Add New User</button>
            <button class="edit-status-btn" type="button" onclick="location.href='admin_editstatus.php'">Manage User Status</button>
            <table>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Mobile No.</th>
                    <th>Email</th>
                    <th>Birthday</th>
                    <th>Username</th>
                    <th>Access Level</th>
                    <th>Status</th>
                </tr>
                <!-- Fetch and display user records from the database -->
                <?php
                $conn = mysqli_connect($servername, $username, $password, $dbname);

                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                $query = "SELECT * FROM users";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['ID'] . "</td>";
                        echo "<td>" . $row['Firstname'] . "</td>";
                        echo "<td>" . $row['Middlename'] . "</td>";
                        echo "<td>" . $row['Lastname'] . "</td>";
                        echo "<td>" . $row['MobileNumber'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td>" . $row['Birthday'] . "</td>";
                        echo "<td>" . $row['Username'] . "</td>";
                        echo "<td>" . $row['AccessLevel'] . "</td>";
                        echo "<td>" . $row['Status'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No records found.</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </table>
            
            <button class="reset-btn" type="button" onclick="location.href='admin_changepass.php'">Reset Password</button>
            <button class="logout-btn" type="button" onclick="location.href='logout.php'">Logout</button>
        </form><br>
    </div>
</body>
</html>
