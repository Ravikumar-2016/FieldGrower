<?php 
// Start session to manage session variables
session_start();

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "Ravikumar123";
$dbname = "fieldgrower";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ''; // Message to display for errors or success

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mode = $_POST['mode'];  // Mode determines the action (sign-in, forgot password, etc.)

    if ($mode == 'signin') {
        // Handle Sign In
        $username = trim($_POST['username']); // Trim to avoid extra spaces
        $password = $_POST['password']; // Using plain text password

        // Check the username and password in the allloginusers table
        $stmt = $conn->prepare("SELECT * FROM allloginusers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
    
            // Directly compare the password (without hashing)
            if ($username === $row['username']) { // Compare plain text password
                $usertype = $row['usertype']; // Get the usertype from the database
    
                // Check usertype and redirect to respective pages
                if ($usertype === "Farmer") {
                    $_SESSION['username'] = $username; // Store username in session
                    $_SESSION['usertype'] = $usertype; // Store usertype in session
                    header("Location: 3farmer.php"); // Redirect to Farmer Dashboard
                    exit();
                } elseif ($usertype === "Labour") {
                    $_SESSION['username'] = $username;
                    $_SESSION['usertype'] = $usertype;
                    header("Location: 4labour.php"); // Redirect to Labour Dashboard
                    exit();
                } elseif ($usertype === "Employee") {
                    $_SESSION['username'] = $username;
                    $_SESSION['usertype'] = $usertype;
                    header("Location: 5employee.php"); // Redirect to Employee Dashboard
                    exit();
                } elseif ($usertype === "Admin") {
                    $_SESSION['username'] = $username;
                    $_SESSION['usertype'] = $usertype;
                    header("Location: 6admin.php"); // Redirect to Admin Dashboard
                    exit();
                } else {
                    $message = "Invalid user type. Please contact support.";
                }
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "Username not found.";
        }
    } elseif ($mode == 'forgot_password') {
        // Handle Forgot Password
        $username = $_POST['username'];
        $phone = $_POST['phone'];

        $stmt = $conn->prepare("SELECT * FROM agrarian WHERE username = ? AND mobile = ?");
        $stmt->bind_param("ss", $username, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $new_password = $_POST['new_password']; // No hashing here, plain text password
            $update_sql = "UPDATE agrarian SET password='$new_password' WHERE username='$username'";
            if ($conn->query($update_sql) === TRUE) {
                $message = "<span style='color: green;'>Password updated successfully.</span>";
            } else {
                $message = "Error updating password.";
            }
        } else {
            $message = "Username and phone number do not match.";
        }
    } elseif ($mode == 'signup') {
        // Handle New Account Creation for Farmers and Labourers only
        $username = $_POST['username'];
        $usertype = $_POST['usertype'];
        $password = $_POST['password']; // Use plain text password
        $fullname = $_POST['fullname'];
        $mobile = $_POST['mobile'];
        $email = $_POST['email'];
        $area = $_POST['area'];
        $state = $_POST['state'];
        $zipcode = $_POST['zipcode'];

        // Check if the username already exists in both agrarian and allloginusers tables
        $stmt_check = $conn->prepare("SELECT * FROM allloginusers WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $message = "Username already exists. Please choose a different username.";
        } else {
            // Insert into agrarian table and allloginusers table
            try {
                // Insert into allloginusers table for login purposes
                $stmt_login = $conn->prepare("INSERT INTO allloginusers (username, password, usertype) VALUES (?, ?, ?)");
                $stmt_login->bind_param("sss", $username, $password, $usertype);
                $stmt_login->execute();
                
                $stmt_agrarian = $conn->prepare("INSERT INTO agrarian (username, usertype, password, fullname, mobile, email, area, state, zipcode)
                                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_agrarian->bind_param("sssssssss", $username, $usertype, $password, $fullname, $mobile, $email, $area, $state, $zipcode);
                $stmt_agrarian->execute();

                $message = "<span style='color: green;'>Account created successfully.</span>";
            } catch (mysqli_sql_exception $e) {
                $message = "An error occurred: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agrarian Project - Sign In</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #f4f4f9;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        form {
            border: 1px solid #ddd;
            padding: 20px;
            background: #fff;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        form h3 {
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"], input[type="email"], select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 1rem;
        }

        button {
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            border-radius: 3px;
        }

        button:hover {
            background: #0056b3;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        .message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>

</head>
<body>

<h2>Field Grower</h2>

<?php if ($message != ''): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<?php
// Get mode (signin, forgot password, signup)
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'signin';

if ($mode == 'signin'): ?>
    <!-- Sign In Form -->
    <form method="POST" action="">
        <input type="hidden" name="mode" value="signin">
        <h3>Sign In</h3>
        <label>Username:</label>
        <input type="text" name="username" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Sign In</button>
    </form>
    <p><a href="?mode=forgot_password">Forgot Password?</a> | <a href="?mode=signup">Create New Account</a></p>

<?php elseif ($mode == 'forgot_password'): ?>
    <!-- Forgot Password Form -->
    <form method="POST" action="">
        <input type="hidden" name="mode" value="forgot_password">
        <h3>Forgot Password</h3>
        <label>Username:</label>
        <input type="text" name="username" required><br>

        <label>Phone:</label>
        <input type="text" name="phone" required><br>

        <label>New Password:</label>
        <input type="password" name="new_password" required><br>

        <button type="submit">Reset Password</button>
    </form>

<?php elseif ($mode == 'signup'): ?>
    <!-- Account Creation Form -->
    <form method="POST" action="">
        <input type="hidden" name="mode" value="signup">
        <h3>Create New Account</h3>
        <label>Username:</label>
        <input type="text" name="username" required><br>

        <label>User Type:</label>
        <select name="usertype" required>
            <option value="Farmer">Farmer</option>
            <option value="Labour">Labour</option>
        </select><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <label>Full Name:</label>
        <input type="text" name="fullname" required><br>

        <label>Mobile:</label>
        <input type="text" name="mobile" required><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Area:</label>
        <input type="text" name="area" required><br>

        <label>State:</label>
        <input type="text" name="state" required><br>

        <label>Zip Code:</label>
        <input type="text" name="zipcode" required><br>

        <button type="submit">Create Account</button>
    </form>

<?php endif; ?>

</body>
</html>
