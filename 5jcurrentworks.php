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

// Handle logout (if logout button is implemented)
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: 2signinpage.php");
    exit();
}

// Query to fetch all current works except the `labourdetails` column
$sql = "SELECT username, cropname, worktype, createddate, dateofwork, laboursrequired, amount, laboursapplied FROM allworkupdates";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Works</title>
    <style>
        /* Apply the same styles from the previous example */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
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
        td {
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Current Works</h1>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Crop Name</th>
                <th>Work Type</th>
                <th>Created Date</th>
                <th>Date of Work</th>
                <th>Labours Required</th>
                <th>Amount</th>
                <th>Labours Applied</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                // Fetch rows and display in table
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['cropname']) . "</td>
                        <td>" . htmlspecialchars($row['worktype']) . "</td>
                        <td>" . htmlspecialchars($row['createddate']) . "</td>
                        <td>" . htmlspecialchars($row['dateofwork']) . "</td>
                        <td>" . htmlspecialchars($row['laboursrequired']) . "</td>
                        <td>" . htmlspecialchars($row['amount']) . "</td>
                        <td>" . htmlspecialchars($row['laboursapplied']) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No current works found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
