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
    $password = $_POST['password'];
    $employeename = $_POST['employeename'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $salary = $_POST['salary'];

    try {
        // Check if username already exists in allloginusers
        $check_user_sql = "SELECT * FROM allloginusers WHERE username = '$username'";
        $check_user_result = $conn->query($check_user_sql);

        if (!$check_user_result) {
            throw new Exception("Error checking username: " . $conn->error);
        }

        if ($check_user_result->num_rows > 0) {
            $message = "Username already exists! Please use a different username.";
        } else {
            // Insert into allloginusers table
            $insert_user_sql = "INSERT INTO allloginusers (username, password, usertype) 
                                VALUES ('$username', '$password', 'Employee')";
            
            if (!$conn->query($insert_user_sql)) {
                throw new Exception("Error inserting into allloginusers: " . $conn->error);
            }

            // Insert into employee table
            $insert_employee_sql = "INSERT INTO employee (username, password, employeename, email, phone, salary) 
                                    VALUES ('$username', '$password', '$employeename', '$email', '$phone', '$salary')";

            if (!$conn->query($insert_employee_sql)) {
                throw new Exception("Error inserting into employee: " . $conn->error);
            }

            $message = "Employee added successfully!";
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
    <title>Add Employee</title>
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
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
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
    <h1 style="text-align: center;">Add Employee</h1>

    <!-- Feedback message -->
    <?php if ($message): ?>
        <div class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="employeename">Employee Name:</label>
        <input type="text" name="employeename" id="employeename" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone" required>

        <label for="salary">Salary:</label>
        <input type="number" name="salary" id="salary" required>

        <button type="submit">Add Employee</button>
    </form>
</body>
</html>
