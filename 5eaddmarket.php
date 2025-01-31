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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get the form input values
    $marketid = $conn->real_escape_string($_POST['marketid']);
    $markettype = $conn->real_escape_string($_POST['markettype']);
    $region = $conn->real_escape_string($_POST['region']);
    $cropname = $conn->real_escape_string($_POST['cropname']);
    $price = $conn->real_escape_string($_POST['price']);

    // Check if the marketid and cropname combination already exists
    $check_query = "SELECT * FROM allregionmarket WHERE marketid = '$marketid' AND cropname = '$cropname'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // Record exists, handle primary key violation
        $message_status = "Error: The market ID and crop name combination already exists in the database. Please try again with different values.";
    } else {
        // Insert the new market crop into the database
        $query = "INSERT INTO allregionmarket (marketid, markettype, region, cropname, price) 
                  VALUES ('$marketid', '$markettype', '$region', '$cropname', '$price')";

        if ($conn->query($query) === TRUE) {
            $message_status = "Market crop added successfully!";
        } else {
            // Other error, e.g., database issues
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
    <title>Add Market Crop</title>
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
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
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
    <h1>Add Market Crop</h1>

    <?php if ($message_status): ?>
        <div class="message"><?php echo $message_status; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form action="5eaddmarket.php" method="POST">
            <label for="marketid">Market ID:</label>
            <input type="text" id="marketid" name="marketid" required>

            <label for="markettype">Market Type:</label>
            <select id="markettype" name="markettype" required>
                <option value="Retail">Retail</option>
                <option value="Wholesale">Wholesale</option>
                <option value="Export">Export</option>
            </select>

            <label for="region">Region:</label>
            <input type="text" id="region" name="region" required>

            <label for="cropname">Crop Name:</label>
            <input type="text" id="cropname" name="cropname" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <button type="submit" name="submit">Add Market Crop</button>
        </form>
    </div>

    <a href="5employee.php" style="display: block; text-align: center; margin-top: 20px; font-size: 16px; color: #4CAF50; text-decoration: none;">Back to Dashboard</a>
</body>
</html>
