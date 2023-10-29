<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Redirect to respective homepage based on access level
    if ($_SESSION['accessLevel'] === 'admin') {
        header("Location: admin_home.php");
        exit;
    } elseif ($_SESSION['accessLevel'] === 'user') {
        header("Location: user_home.php");
        exit;
    }
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

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $enteredPasswordHash = hash('sha256', $password);

        if ($enteredPasswordHash === $row['Password']) {
            if ($row['Status'] === 'active') {
                $_SESSION['loggedin'] = true;
                $_SESSION['userID'] = $row['ID'];
                $_SESSION['accessLevel'] = $row['AccessLevel'];

                // Redirect to respective homepage based on access level
                if ($row['AccessLevel'] === 'admin') {
                    mysqli_close($conn);
                    header("Location: admin_home.php");
                    exit;
                } elseif ($row['AccessLevel'] === 'user') {
                    mysqli_close($conn);
                    header("Location: user_home.php");
                    exit;
                }
            } else {
                $loginError = "This account is disabled. Please contact the administrator.";
            }
        } else {
            $loginError = "Incorrect password. Try again!";
        }
    } else {
        $loginError = "User does not exist.";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
        h1 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="password"] {
            width: 94%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 8px 16px;
            margin-top: 15px;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center;">
            <img src="panda.png" alt="Panda" width="200" height="100" style="display: block; margin: 0 auto;">
        </div>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>

            <?php if (isset($loginError)) { ?>
                <p class="error"><?php echo $loginError; ?></p>
            <?php } ?>
        </form><br>
    </div>
</body>
</html>
