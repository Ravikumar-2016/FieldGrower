<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "Ravikumar123";
$dbname = "fieldgrower";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure session username is set
if (!isset($_SESSION['username'])) {
    header("Location: 2signinpage.php"); // Redirect to login page if not logged in
    exit();
}

// Get the logged-in username from the session
$username = $_SESSION['username'];

// Handle adding new crop
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_crop'])) {
    // Get form inputs
    $cropname = trim($_POST['cropname']);
    $cultivationarea = trim($_POST['cultivationarea']);
    $cropdescription = trim($_POST['cropdescription']);
    $soiltype = trim($_POST['soiltype']);

    // Validate the inputs
    if (!empty($cropname) && !empty($cultivationarea) && !empty($cropdescription) && !empty($soiltype)) {
        // Prepare SQL to insert crop details
        $insert_sql = "INSERT INTO allusercropdetails (username, cropname, cultivationarea, cropdescription, soiltype)
                       VALUES (?, ?, ?, ?, ?)";
        
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sssss", $username, $cropname, $cultivationarea, $cropdescription, $soiltype);

        try {
            // Try to execute the statement
            if ($stmt->execute()) {
                $success_message = "Crop added successfully!";
            }
        } catch (mysqli_sql_exception $e) {
            // Catch duplicate entry error (e.g., user2-wheat)
            if ($e->getCode() == 1062) { // MySQL duplicate entry error code
                $error_message = "You have already added this crop!";
            } else {
                $error_message = "An error occurred: " . $e->getMessage();
            }
        }
    } else {
        $error_message = "All fields are required to add a crop.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
    <style>
        body {
            font-family: "Bona Nova SC", serif;
            font-weight: 400;
            font-style: normal;
            margin: 0;
            padding: 0;
            background-size: cover;
            background:white;
            height: 250px;
            overflow-x: hidden;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 30px;
            width: 100%;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-left: 200px;
            gap: 20px;
        }

        .website-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }

        .website-name {
            font-size: 50px;
            font-weight: bold;
            margin-left: 10px;
            color:green;
        }

        .nav-container {
            display: flex;
            justify-content: space-around;
            width: 30%;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding-left: 100px;
        }

        .nav-links a {
            color: blue;
            text-decoration: none;
            font-weight: 400;
            font-size: 20px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #FFD700;
        }

        /* User Profile Styles */
        .user-container {
            position: relative;
            display: inline-block;
            margin-right: 200px;
            padding-left: 10px;
        }

        .user-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ddd;
            border: 2px solid #fff;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .user-logo:hover {
            transform: scale(1.1);
        }

        .user-profile {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 250px;
        }

        .user-container:hover .user-profile {
            display: block;
        }

        .user-profile h4 {
            margin-right: 8px;
            margin-left: 10px;
            color: #4CAF50;
        }

        .user-profile .profile-detail {
            margin-bottom: 8px;
            color: #555;
            font-size: 0.9em;
        }

        /* Add Crop Form Styles */
        .add-crop-form {
            width: 80%;
            margin: 20px auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
        }

        /* Center the Add Crop Button */
        .submitbtn {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-top: 20px;
        }

        .search-button {
            width: 200px;
            height: 50px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .search-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<header class="header">
    <div class="logo">
        <img src="logo.jpg" alt="Website Logo" class="website-logo">
        <span class="website-name">Field Grower</span>
    </div>

    <div class="nav-container">
        <nav class="nav-links">
            <a href="1homepage.html">Home</a>
            <a href="0aboutus.html">About Us</a>
            <a href="0contactus.php">Contact Us</a>
        </nav>
    </div>

    <div class="user-container">
        <img src="userlogo.jpeg" alt="User Logo" class="user-logo" id="user-logo">
        <div class="user-profile">
            <?php
            // Fetch user details from the database
            $stmt = $conn->prepare("SELECT fullname, mobile, email, area, state, zipcode FROM agrarian WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo "<h4>{$user['fullname']}</h4>";
                echo "<div class='profile-detail'>Mobile: {$user['mobile']}</div>";
                echo "<div class='profile-detail'>Email: {$user['email']}</div>";
                echo "<div class='profile-detail'>Address: {$user['area']}, {$user['state']}, {$user['zipcode']}</div>";
            } else {
                echo "<p>User details not found.</p>";
            }
            ?>
        </div>
    </div>
</header>

<!-- Crop Adding Form -->
<div class="center-container">
    <form class="add-crop-form" method="POST" action="">
        <div class="form-group">
            <label for="cropname">Crop Name</label>
            <input type="text" id="cropname" name="cropname" placeholder="Enter crop name" required>
        </div>
        <div class="form-group">
            <label for="cultivationarea">Cultivation Area</label>
            <input type="text" id="cultivationarea" name="cultivationarea" placeholder="Enter cultivation area" required>
        </div>
        <div class="form-group">
            <label for="cropdescription">Crop Description</label>
            <textarea id="cropdescription" name="cropdescription" placeholder="Enter crop description like farming method,irrigation,budget,crop period..." required></textarea>
        </div>
        <div class="form-group">
            <label for="soiltype">Soil Type</label>
            <input type="text" id="soiltype" name="soiltype" placeholder="Enter soil type" required>
        </div>

        <div class="submitbtn">
            <button type="submit" class="search-button" name="add_crop">Add Crop</button>
        </div>
    </form>

    <?php
    // Display success or error message
    if (isset($success_message)) {
        echo "<p class='success-message'>{$success_message}</p>";
    } elseif (isset($error_message)) {
        echo "<p class='error-message'>{$error_message}</p>";
    }
    ?>
</div>

<script>
    // Automatically hide the success message after 700ms
    setTimeout(() => {
        const message = document.querySelector('.success-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 1100);
</script>

</body>
</html>
