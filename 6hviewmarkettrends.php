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

// Fetch market data
$sql = "SELECT * FROM allregionmarket";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Market Trends</title>
    <style>
        /* Style for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <header class="header">
        <!-- Add your header content here, similar to the previous page -->
        <h1>Market Trends</h1>
    </header>

    <!-- Table to display all region market data -->
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Market ID</th>
                    <th>Market Type</th>
                    <th>Crop Name</th>
                    <th>Price</th>
                    <th>Region</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data for each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['marketid'] . "</td>";
                        echo "<td>" . $row['markettype'] . "</td>";
                        echo "<td>" . $row['cropname'] . "</td>";
                        echo "<td>" . $row['price'] . "</td>";
                        echo "<td>" . $row['region'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
