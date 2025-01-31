<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "Ravikumar123";
$dbname = "fieldgrower";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure session username is set
if (!isset($_SESSION['username'])) {
    header("Location: 2signinpage.php"); // Redirect to login page if not logged in
    exit();
}

// Initialize message variable
$message_status = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    // Get the form input values
    $marketid = $conn->real_escape_string($_POST['marketid']);
    $cropname = $conn->real_escape_string($_POST['cropname']);

    // Check if the marketid and cropname combination exists
    $check_query = "SELECT * FROM allregionmarket WHERE marketid = '$marketid' AND cropname = '$cropname'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows == 0) {
        // Record does not exist
        $message_status = "Error: The market ID and crop name combination does not exist in the database.";
    } else {
        // Delete the record from the database
        $delete_query = "DELETE FROM allregionmarket WHERE marketid = '$marketid' AND cropname = '$cropname'";

        if ($conn->query($delete_query) === TRUE) {
            $message_status = "Market crop deleted successfully!";
        } else {
            // Error occurred while deleting
            $message_status = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Market Crop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #d32f2f;
        }
        .message {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #e7f7e7;
            color: #4CAF50;
            border: 1px solid #4CAF50;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Delete Market Crop</h1>

    <?php if ($message_status): ?>
        <div class="message"><?php echo $message_status; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form action="5fdeletemarket.php" method="POST">
            <label for="marketid">Market ID:</label>
            <input type="text" id="marketid" name="marketid" required>

            <label for="cropname">Crop Name:</label>
            <input type="text" id="cropname" name="cropname" required>

            <button type="submit" name="delete">Delete Market Crop</button>
        </form>
    </div>

    <a href="5employee.php" style="display: block; text-align: center; margin-top: 20px; font-size: 16px; color: #4CAF50; text-decoration: none;">Back to Dashboard</a>
</body>
</html>
