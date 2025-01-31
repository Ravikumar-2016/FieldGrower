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

// Fetch crop details
$sql = "SELECT cropname, croptype, suitablemethod, cropdescription FROM cropdetails";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Details</title>
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
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
    </style>
</head>
<body>
    <h1 style="text-align: center;">Crop Details</h1>
    <table>
        <thead>
            <tr>
                <th>Crop Name</th>
                <th>Crop Type</th>
                <th>Suitable Method</th>
                <th>Crop Description</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['cropname']; ?></td>
                        <td><?php echo $row['croptype']; ?></td>
                        <td><?php echo $row['suitablemethod']; ?></td>
                        <td><?php echo $row['cropdescription']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No crop details found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
