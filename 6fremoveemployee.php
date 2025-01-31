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

$message = ""; // Variable to hold feedback messages

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    try {
        // Check if the employee exists in the employee table
        $check_employee_sql = "SELECT * FROM employee WHERE username = '$username'";
        $check_employee_result = $conn->query($check_employee_sql);

        if (!$check_employee_result) {
            throw new Exception("Error checking employee: " . $conn->error);
        }

        if ($check_employee_result->num_rows === 0) {
            $message = "Employee with username '$username' does not exist.";
        } else {
            // Delete from employee table
            $delete_employee_sql = "DELETE FROM employee WHERE username = '$username'";
            if (!$conn->query($delete_employee_sql)) {
                throw new Exception("Error deleting from employee table: " . $conn->error);
            }

            // Delete from allloginusers table
            $delete_user_sql = "DELETE FROM allloginusers WHERE username = '$username'";
            if (!$conn->query($delete_user_sql)) {
                throw new Exception("Error deleting from allloginusers table: " . $conn->error);
            }

            $message = "Employee with username '$username' has been successfully removed.";
        }
    } catch (Exception $e) {
        $message = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Employee</title>
    <style>
        form {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        label {
            display: block;
            font-size: 18px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #d32f2f;
        }

        .message {
            text-align: center;
            font-size: 18px;
            color: #333;
            margin: 10px auto;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Remove Employee</h1>

    <!-- Feedback message -->
    <?php if ($message): ?>
        <div class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <button type="submit">Remove Employee</button>
    </form>
</body>
</html>
