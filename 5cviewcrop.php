<?php
// Start session to manage session variables
session_start();

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "Ravikumar123";
$dbname = "fieldgrower";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ''; // Message to display for errors or success

// Fetch all crops from the cropdetails table
$sql = "SELECT * FROM cropdetails";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Crops - Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #f4f4f9;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color: #007bff;
            color: white;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .message {
            text-align: center;
            margin-bottom: 10px;
        }

        .message span {
            display: inline-block;
            padding: 10px;
            margin: 10px;
            font-size: 1.1rem;
        }

    </style>
</head>
<body>

<h2>View Crops</h2>

<?php if ($message != ''): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<!-- Table displaying crop details -->
<?php if ($result->num_rows > 0): ?>
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
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['cropname']; ?></td>
                    <td><?php echo $row['croptype']; ?></td>
                    <td><?php echo $row['suitablemethod']; ?></td>
                    <td><?php echo $row['cropdescription']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No crops found in the database.</p>
<?php endif; ?>

</body>
</html>
