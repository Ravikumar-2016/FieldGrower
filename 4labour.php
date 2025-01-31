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

if (isset($_POST['logout'])) {
    // Destroy the session
    session_unset();
    session_destroy();
    
    // Redirect to the login page
    header("Location: 2signinpage.php");
    exit();
}

// Get the logged-in username from the session
$username = $_SESSION['username'];
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

        /* Form Styles */
        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .form-group button {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        /* Success and Error Messages */
        .success-message {
            color: green;
            font-weight: bold;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .update-form {
            display: none; /* Initially hidden */
            margin-top: 10px;
        }

        .update-form.active {
            display: block; /* Visible when active class is toggled */
        }

        
        /* Center container */
        .center-container {
            display: flex;
            justify-content: center;
            align-items:center;
            
            height: 100vh;
            gap: 20px;
        }
        
        /* Main button styles */
        .main-button {
            position: relative;
            padding: 15px 30px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }

        /* Dropdown menu styles */
        .main-button:hover .submenu {
            display: block;
        }
        
        .submenu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #333;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .submenu a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            background-color: #333;
        }

        .submenu a:hover {
            background-color: #555;
        }
        .logout-btn {
    margin-top: 10px;
    padding: 8px 16px;
    background-color: #f44336; /* Red color for logout */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    text-align: center;
}

.logout-btn:hover {
    background-color: #d32f2f; /* Darker red on hover */
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
            <!-- Update Profile Button -->
            <button class="update-profile-btn" onclick="toggleUpdateForm()">Update Profile</button>

            <!-- Hidden Update Form -->
            <form method="POST" action="" class="update-form" id="update-form">
                <div class="form-group">
                    <label for="mobile">Update Mobile</label>
                    <input type="text" id="mobile" name="mobile" placeholder="New Mobile">
                </div>
                <div class="form-group">
                    <label for="email">Update Email</label>
                    <input type="email" id="email" name="email" placeholder="New Email">
                </div>
                <button type="submit" name="update">Update</button>
            </form>
            <form method="POST" action="" class="logout-form">
                <button type="submit" name="logout" class="logout-btn">Log Out</button>
            </form>
        </div>
    </div>
</header>

<?php
// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $new_mobile = trim($_POST['mobile']);
    $new_email = trim($_POST['email']);

    if (!empty($new_mobile) || !empty($new_email)) {
        $update_sql = "UPDATE agrarian SET ";
        $params = [];
        $types = "";

        if (!empty($new_mobile)) {
            $update_sql .= "mobile = ?, ";
            $params[] = $new_mobile;
            $types .= "s";
        }

        if (!empty($new_email)) {
            $update_sql .= "email = ?, ";
            $params[] = $new_email;
            $types .= "s";
        }

        $update_sql = rtrim($update_sql, ", ");
        $update_sql .= " WHERE username = ?";
        $params[] = $username;
        $types .= "s";

        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo "<p class='success-message' id='update-message'>Profile updated successfully.</p>";
        } else {
            echo "<p class='error-message' id='update-message'>Failed to update profile.</p>";
        }
    } else {
        echo "<p class='error-message' id='update-message'>Please fill at least one field to update.</p>";
    }
}
?>
<div class="center-container">
    <div class="main-button">
        WorkFarm
        <div class="submenu">
            <a href="4aworkannouncements.php">Work Announcements</a>
        </div>

</div>
<button class="main-button" onclick="location.href='3jqueries.php'">Queries</button>

<script>
    function toggleUpdateForm() {
        const updateForm = document.getElementById('update-form');
        updateForm.classList.toggle('active');
    }

    // Automatically hide the success or error message after 5 seconds
    setTimeout(() => {
        const message = document.getElementById('update-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 700);

    </script>