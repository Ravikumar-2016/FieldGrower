<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's username
$username = $_SESSION['username'];

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

// Fetch unresolved queries
$unresolved_query_sql = "
    SELECT 
        ci.queryid, ci.subject, ci.message, ci.createddate, 'Pending' AS status 
    FROM 
        customerinsights ci
    LEFT JOIN 
        queryupdates qu ON ci.queryid = qu.queryid
    WHERE 
        ci.username = ? AND qu.queryid IS NULL
";

$unresolved_stmt = $conn->prepare($unresolved_query_sql);
$unresolved_stmt->bind_param("s", $username);
$unresolved_stmt->execute();
$unresolved_result = $unresolved_stmt->get_result();

// Fetch resolved queries
$resolved_query_sql = "
    SELECT 
        ci.queryid, ci.subject, ci.message, ci.createddate, 
        qu.employeeresponse, qu.resolveddate 
    FROM 
        customerinsights ci
    INNER JOIN 
        queryupdates qu ON ci.queryid = qu.queryid
    WHERE 
        ci.username = ?
";

$resolved_stmt = $conn->prepare($resolved_query_sql);
$resolved_stmt->bind_param("s", $username);
$resolved_stmt->execute();
$resolved_result = $resolved_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Queries</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
    </style>
</head>
<body>

<h1>Unresolved Queries</h1>
<table>
    <thead>
        <tr>
            <th>Query ID</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Created Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $unresolved_result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['queryid']); ?></td>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td><?php echo htmlspecialchars($row['message']); ?></td>
                <td><?php echo htmlspecialchars($row['createddate']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h1>Resolved Queries</h1>
<table>
    <thead>
        <tr>
            <th>Query ID</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Created Date</th>
            <th>Employee Response</th>
            <th>Resolved Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $resolved_result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['queryid']); ?></td>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td><?php echo htmlspecialchars($row['message']); ?></td>
                <td><?php echo htmlspecialchars($row['createddate']); ?></td>
                <td><?php echo htmlspecialchars($row['employeeresponse']); ?></td>
                <td><?php echo htmlspecialchars($row['resolveddate']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>

<?php
// Close the statements and connection
$unresolved_stmt->close();
$resolved_stmt->close();
$conn->close();
?>
