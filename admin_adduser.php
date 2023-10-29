<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "technical4_db";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $mobilenumber = $_POST['mobilenumber'];

    // Verify that passwords match
    if ($password !== $confirmpassword) {
        die("Passwords do not match.");
    }

    // Hash the password for security
    $hashedpassword = hash('sha256', $password);

    $query = "INSERT INTO users (Firstname, Middlename, Lastname, Username, Password, Birthday, Email, MobileNumber, AccessLevel, Status) VALUES ('$firstname', '$middlename', '$lastname', '$username', '$hashedpassword', '$birthday', '$email', '$mobilenumber', 'user', 'active')";

    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        header("Location: admin_home.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
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
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            margin-left: 5px;
        }
        input[type="text"],
        input[type="password"],
        input[type="date"] {
            width: 95%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 8px 16px;
        }
        .back-btn {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center;">
            <img src="panda.png" alt="Panda" width="200" height="100" style="display: block; margin: 0 auto;">
        </div>
        <form method="POST">
            <label for="firstname">First Name:</label>
            <input type="text" name="firstname" required>

            <label for="middlename">Middle Name:</label>
            <input type="text" name="middlename">

            <label for="lastname">Last Name:</label>
            <input type="text" name="lastname" required>

            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <label for="confirmpassword">Confirm Password:</label>
            <input type="password" name="confirmpassword" required>

            <label for="birthday">Birthday:</label>
            <input type="date" name="birthday" required>

            <label for="email">Email:</label>
            <input type="text" name="email" required>

            <label for="mobilenumber">Mobile Number:</label>
            <input type="text" name="mobilenumber" required>

            <button type="submit">Submit</button>
            <button class="back-btn" type="button" onclick="location.href='admin_home.php'">Cancel</button>
        </form><br>
    </div>
</body>
</html>
