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

// Fetch user count and employee count
$user_count_sql = "SELECT COUNT(*) AS total_users FROM agrarian";
$employee_count_sql = "SELECT COUNT(*) AS total_employees FROM employee";

$user_result = $conn->query($user_count_sql);
$employee_result = $conn->query($employee_count_sql);

$user_count = $user_result->fetch_assoc()['total_users'];
$employee_count = $employee_result->fetch_assoc()['total_employees'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            width: 20%;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 60%;
            padding-left: 280px;
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
            padding-left: 0px;
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
            margin-left: 0px;
            color: #4CAF50;
        }

        .user-profile .profile-detail {
            margin-bottom: 8px;
            color: #555;
            font-size: 0.9em;
        }

.circle-container {
    display: flex;
    justify-content: center;
    gap: 300px;
    margin-top: 100px;
}

.circle {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background-color: #4CAF50;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 30px;
    font-weight: bold;
    position: relative;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: all 2s ease;  /* Added transition to animate circle size */
}


/* Circle grow animation */
@keyframes circleGrow {
    0% {
        transform: scale(0.5);
    }
    100% {
        transform: scale(1.5);
    }
}

.circle.grow {
    animation: circleGrow 2s ease forwards; /* Synchronize with 2 seconds */
}



/* Count animation */
@keyframes countAnimation {
    0% {
        content: '0';
    }
    100% {
        content: attr(data-count);
    }
}

.circle span {
    font-size: 36px;
    margin-bottom: 10px; /* Space between number and text */
    color: #FFD700; /* Add your desired color for the count number */
    transition: transform 2s ease; /* Smooth animation for scaling */
}

.circle p {
    font-size: 18px; /* Adjust font size for the label text */
    margin: 0;
    text-align: center;
    color: white; /* Keep text inside the circle styled differently */
}


.container {
            display: flex;
            justify-content: center;
            align-items: center;
             height: 45vh;
             gap: 40px;
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
            font-size: 21px;
            text-align: center;
            height: 30px;
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
        </nav>
    </div>
    <div class="user-container">
        <img src="userlogo.jpeg" alt="User Logo" class="user-logo" id="user-logo">
        <div class="user-profile">
    <?php
    // Fetch admin details from the database
    $stmt = $conn->prepare("SELECT fullname, email, mobile, role FROM masterlogin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo "<h4>{$admin['fullname']}</h4>";
        echo "<div class='profile-detail'>Role: {$admin['role']}</div>";
        echo "<div class='profile-detail'>Mobile: {$admin['mobile']}</div>";
        echo "<div class='profile-detail'>Email: {$admin['email']}</div>";
    } else {
        echo "<p>Admin details not found.</p>";
    }
?>
 <!-- Update Profile Button -->
 <button class="update-profile-btn" onclick="toggleUpdateForm()">Update Profile</button>

 <form method="POST" action="" class="update-form" id="update-form" style="display:none;">
                <div class="form-group">
                    <label for="mobile">Update mobile</label>
                    <input type="text" id="mobile" name="mobile" placeholder="New mobile">
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
    $new_mobile = trim($_POST['mobile']);
    $new_email = trim($_POST['email']);

    if (!empty($new_mobile) || !empty($new_email)) {
        $update_sql = "UPDATE masterlogin SET ";
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


<div class="container">
    <!-- Animated User Count and Employee Count Circles -->
    <div class="circle-container">
        <div class="circle" id="user-circle">
            <span id="user-count" data-count="<?php echo $user_count; ?>">0</span>
            <p>User Count</p>
        </div>
        <div class="circle" id="employee-circle">
            <span id="employee-count" data-count="<?php echo $employee_count; ?>">0</span>
            <p>Employee Count</p>
        </div>
    </div>
</div>

<div class="container">
        <!-- Crops -->
        <div class="main-button">
               Crops
            <div class="submenu">
                <a href="6aviewcrops.php">Crops</a>
                <a href="6bviewfertilizers.php">Ferilizers</a>
                <a href="6cviewpesticides.php">Pesticides</a>
            </div>
        </div>

        <!-- Manage Market -->
        <div class="main-button">
            Manage Employee
            <div class="submenu">
                <a href="6dviewemployee.php">View Employees</a>
                <a href="6eaddemployee.php">Add Employee</a>
                <a href="6fremoveemployee.php">Remove Employee</a>
                <a href="6gupdatesalary.php">Update Salary</a>
            </div>
        </div>

        <div class="main-button">
        View Market Trends
        <div class="submenu">
            <a href="6hviewmarkettrends.php">Market Trends</a>
        </div>
    </div>

    <!-- New Button 2: User Queries -->
    <div class="main-button">
        User Queries
        <div class="submenu">
            <a href="6iqueries.php">View Queries</a>
        </div>
    </div>
        
</div>
<script>

    // Automatically hide the success or error message after 5 seconds
    setTimeout(() => {
        const message = document.getElementById('update-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 700);


    function toggleUpdateForm() {
    const updateForm = document.getElementById('update-form');
    if (updateForm.style.display === "none" || updateForm.style.display === "") {
        updateForm.style.display = "block"; // Show the form
    } else {
        updateForm.style.display = "none"; // Hide the form
    }
}




    // Animate the counters on page load
window.onload = function() {
    let userCount = document.getElementById("user-count");
    let employeeCount = document.getElementById("employee-count");
    let userCircle = document.getElementById("user-circle");
    let employeeCircle = document.getElementById("employee-circle");

    let userTotal = parseInt(userCount.getAttribute('data-count'));
    let employeeTotal = parseInt(employeeCount.getAttribute('data-count'));

    let userCounter = 0;
    let employeeCounter = 0;

    // Animate circle size and number count for user count
    let userInterval = setInterval(function() {
        if (userCounter < userTotal) {
            userCounter++;
            userCount.textContent = userCounter;
            if (userCounter === 1) {
                userCircle.classList.add('grow'); // Start the circle grow animation
            }
        } else {
            clearInterval(userInterval);
        }
    }, 60);

    // Animate circle size and number count for employee count
    let employeeInterval = setInterval(function() {
        if (employeeCounter < employeeTotal) {
            employeeCounter++;
            employeeCount.textContent = employeeCounter;
            if (employeeCounter === 1) {
                employeeCircle.classList.add('grow'); // Start the circle grow animation
            }
        } else {
            clearInterval(employeeInterval);
        }
    }, 60);
};

</script>