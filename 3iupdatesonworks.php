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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Updates on Works</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Updates on Works</h1>

    <?php
    // Fetch updates for the logged-in user
    $stmt = $conn->prepare("SELECT cropname, worktype, dateofwork, laboursrequired, laboursapplied, labourdetails FROM allworkupdates WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any updates
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<thead>
                <tr>
                    <th>Crop Name</th>
                    <th>Work Type</th>
                    <th>Date of Work</th>
                    <th>Labours Required</th>
                    <th>Labours Applied</th>
                    <th>Labour Details</th>
                </tr>
              </thead>";
        echo "<tbody>";

        // Display each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['cropname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['worktype']) . "</td>";
            echo "<td>" . htmlspecialchars($row['dateofwork']) . "</td>";
            echo "<td>" . htmlspecialchars($row['laboursrequired']) . "</td>";
            echo "<td>" . htmlspecialchars($row['laboursapplied']) . "</td>";
            echo "<td>" . htmlspecialchars($row['labourdetails']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p style='text-align: center; color: red;'>No updates available for your works.</p>";
    }
    ?>
</body>
</html>
