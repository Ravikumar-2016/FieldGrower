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

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: 2signinpage.php");
    exit();
}

// Get logged-in employee username
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
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
            padding-left: 240px;
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
            width: 200px; /* Reduced size of the profile box */
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

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .main-button {
            position: relative;
            background-color: #4CAF50;
            color: white;
            padding: 20px 40px;
            margin: 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            text-align: center;
        }

        .main-button:hover .submenu {
            display: block;
        }

        .submenu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .submenu a {
            display: block;
            padding: 10px 20px;
            color: black;
            text-decoration: none;
            background-color: white;
        }

        .submenu a:hover {
            background-color: #ddd;
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
        </nav>
    </div>
    <div class="user-container">
        <img src="userlogo.jpeg" alt="User Logo" class="user-logo" id="user-logo">
        <div class="user-profile">
            <?php
            // Fetch user details from the 'employee' table
            $stmt = $conn->prepare("SELECT employeename, phone, email, salary FROM employee WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo "<h4>{$user['employeename']}</h4>";
                echo "<div class='profile-detail'>Phone: {$user['phone']}</div>";
                echo "<div class='profile-detail'>Email: {$user['email']}</div>";
                echo "<div class='profile-detail' id='salary' style='display:none;'>Salary: {$user['salary']}</div>"; // Hidden salary until update is clicked
            } else {
                echo "<p>User details not found.</p>";
            }
            ?>
            <!-- Update Profile Button -->
            <button class="update-profile-btn" onclick="toggleUpdateForm()">Update Profile</button>

            <!-- Hidden Update Form -->
            <form method="POST" action="" class="update-form" id="update-form" style="display:none;">
                <div class="form-group">
                    <label for="phone">Update Phone</label>
                    <input type="text" id="phone" name="phone" placeholder="New Phone">
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

            <!-- Transition message -->
            <?php if (isset($update_message)) { ?>
                <div class="update-message <?php echo isset($update_message) ? 'show' : ''; ?>">
                    <?php echo $update_message; ?>
                </div>
            <?php } ?>
        </div>
    </div>
</header>

<?php
// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $new_phone = trim($_POST['phone']);
    $new_email = trim($_POST['email']);

    if (!empty($new_phone) || !empty($new_email)) {
        $update_sql = "UPDATE employee SET ";
        $params = [];
        $types = "";

        if (!empty($new_phone)) {
            $update_sql .= "phone = ?, ";
            $params[] = $new_phone;
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


<script>
    function toggleUpdateForm() {
        // Toggle the update form visibility
        var form = document.getElementById("update-form");
        var salary = document.getElementById("salary");
        if (form.style.display === "none") {
            form.style.display = "block";
            salary.style.display = "none"; // Hide salary when form is shown
        } else {
            form.style.display = "none";
            salary.style.display = "block"; // Show salary after update
        }
    }
    window.onload = function() {
        // Check if there's a message to hide
        var message = document.getElementById('update-message');
        if (message) {
            setTimeout(function() {
                message.style.display = 'none';
            }, 1000); // Hide message after 1 second
        }
    };
</script>

    
    <div class="container">
        <!-- Manage Crops -->
        <div class="main-button">
            Manage Crops
            <div class="submenu">
                <a href="5aaddcrop.php">Add Crop</a>
                <a href="5bdeletecrop.php">Delete Crop</a>
                <a href="5cviewcrop.php">View Crops</a>
            </div>
        </div>

        <!-- Manage Market -->
        <div class="main-button">
            Manage Market
            <div class="submenu">
                <a href="5dviewmarket.php">View Market</a>
                <a href="5eaddmarket.php">Add Market Crop</a>
                <a href="5fdeletemarket.php">Delete Market Crop</a>
                <a href="5gupdatemarket.php">Update Price</a>
            </div>
        </div>

        <!-- Work Farm -->
        <div class="main-button">
            WorkFarm
            <div class="submenu">
                <a href="5jcurrentworks.php">Current Works</a>
                <a href="5kpastworks.php">Completed Works</a>
            </div>
        </div>

        <!-- Customer Insights -->
        <div class="main-button">
            Customer Insights
            <div class="submenu">
                <a href="5hviewquery.php">Customer Queries</a>
            </div>
        </div>
    </div>
</body>
</html>
