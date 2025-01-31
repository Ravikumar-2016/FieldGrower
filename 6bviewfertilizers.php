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

// Fetch fertilizers
$sql = "SELECT cropname, fertilizer, fertilizertype, price, quantity, applications FROM fertilizers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fertilizers</title>
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
    <h1 style="text-align: center;">Fertilizers</h1>
    <table>
        <thead>
            <tr>
                <th>Crop Name</th>
                <th>Fertilizer</th>
                <th>Fertilizer Type</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>applications</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['cropname']; ?></td>
                        <td><?php echo $row['fertilizer']; ?></td>
                        <td><?php echo $row['fertilizertype']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['applications']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No fertilizers found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
