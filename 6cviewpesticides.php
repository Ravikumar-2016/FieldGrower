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

// Fetch pesticides
$sql = "SELECT cropname, diseasename, diseasetype, suitablepesticide, price, quantity FROM pesticides";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesticides</title>
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
    <h1 style="text-align: center;">Pesticides</h1>
    <table>
        <thead>
            <tr>
                <th>Crop Name</th>
                <th>Disease Name</th>
                <th>Disease Type</th>
                <th>Suitable Pesticide</th>
                <th>Price</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['cropname']; ?></td>
                        <td><?php echo $row['diseasename']; ?></td>
                        <td><?php echo $row['diseasetype']; ?></td>
                        <td><?php echo $row['suitablepesticide']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No pesticides found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
