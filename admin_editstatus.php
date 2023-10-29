<?php
session_start();

// Check if user is logged in and has admin access level
if (!isset($_SESSION['loggedin']) || $_SESSION['accessLevel'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "technical4_db";

// Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve all users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) < 1) {
    mysqli_close($conn);
    die("No users found");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userID = $_POST['userID'];
    $newStatus = $_POST['status'];

    // Update the user status in the database
    $updateQuery = "UPDATE users SET Status = '$newStatus' WHERE ID = $userID";

    if (mysqli_query($conn, $updateQuery)) {
        mysqli_close($conn);
        header("Location: admin_home.php");
        exit;
    } else {
        $updateError = "Error updating user status: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User Status</title>
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
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 8px 16px;
            margin-top: 10px;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit User Status</h2>
        <form method="POST">
            <label for="user">Select User:</label>
            <select id="user" name="userID" required>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['ID'] . "'>" . $row['Username'] . "</option>";
                }
                ?>
            </select>

            <label for="new-status">New Status:</label>
            <select id="new-status" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit">Save</button>

            <?php if (isset($updateError)) { ?>
                <p class="error"><?php echo $updateError; ?></p>
            <?php } ?>
        </form><br>
    </div>
</body>
</html>
