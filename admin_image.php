<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Increase execution time and memory limit
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

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
    mysqli_close($conn);
    die("Error retrieving user information");
}

$user = mysqli_fetch_assoc($result);

// Convert BLOB image data to base64 for display
$imageData = base64_encode($user['Image']);

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    $imageData = file_get_contents($_FILES['image']['tmp_name']); // Read the uploaded image file
    $imageData = mysqli_real_escape_string($conn, $imageData); // Escape special characters

    $updateQuery = "UPDATE users SET Image = '$imageData' WHERE ID = $userID";

    if (mysqli_query($conn, $updateQuery)) {
        mysqli_close($conn);
        header("Location: admin_home.php");
        exit;
    } else {
        echo "Error uploading image: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Image</title>
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
        label {
            display: block;
            margin-bottom: 10px;
        }
        .file-upload {
            display: none;
        }
        .file-upload-label {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
            cursor: pointer;
        }
        .file-upload-label::after {
            content: 'Choose File';
        }
        .file-upload-input {
            display: none;
        }
        .file-upload-filename {
            margin-top: 5px;
            font-size: 14px;
            color: #888;
        }
        .upload-btn {
            margin-top: 10px;
            padding: 8px 16px;
        }
        .back-btn {
            margin-top: 10px;
            padding: 8px 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <h2>Update Profile Picture</h2>
            <img class="profile-img" src="data:image/jpeg;base64,<?php echo $imageData; ?>" alt="Profile Picture">
            <p><b>Full Name:</b><?php echo ' ' . $user['Firstname'] . ' ' . $user['Middlename'] . ' ' . $user['Lastname']; ?></p>
            <p><b>User Level:</b> <?php echo $user['AccessLevel']; ?></p>
            <p><b>Birthday:</b> <?php echo $user['Birthday']; ?></p>
            <p><b>Mobile Number:</b> <?php echo $user['MobileNumber']; ?></p>
            <p><b>Email:</b> <?php echo $user['Email']; ?></p>

            <label for="image" class="file-upload-label"></label>
            <input type="file" id="image" name="image" class="file-upload-input" accept="image/*">
            <div class="file-upload-filename" id="file-upload-filename">No file chosen</div>

            <button class="upload-btn" type="submit">Upload Image</button>
            <button class="back-btn" type="button" onclick="history.back()">Cancel</button>
        </form><br>
    </div>

    <script>
        const fileUpload = document.getElementById('image');
        const fileUploadFilename = document.getElementById('file-upload-filename');

        fileUpload.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                fileUploadFilename.textContent = file.name;
            } else {
                fileUploadFilename.textContent = 'No file chosen';
            }
        });
    </script>
</body>
</html>
