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

// Initialize variables for query results
$customerInsights = null;
$queryUpdates = null;

try {
    // Fetch unresolved queries from customerinsights (not in queryupdates)
    $unresolvedQueriesSql = "
        SELECT 
            ci.queryid,
            ci.username,
            ci.fullname,
            ci.email,
            ci.subject,
            ci.message,
            ci.createddate
        FROM 
            customerinsights ci
        LEFT JOIN 
            queryupdates qu
        ON 
            ci.queryid = qu.queryid
        WHERE 
            qu.queryid IS NULL
    ";
    $customerInsights = $conn->query($unresolvedQueriesSql);

    // Fetch resolved queries with the required attributes
    $resolvedQueriesSql = "
        SELECT 
            ci.queryid,
            ci.username,
            ci.subject,
            ci.message,
            ci.createddate,
            qu.employeeresponse,
            qu.resolveddate
        FROM 
            customerinsights ci
        JOIN 
            queryupdates qu
        ON 
            ci.queryid = qu.queryid
    ";
    $queryUpdates = $conn->query($resolvedQueriesSql);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Queries</title>
    <style>
        /* Style for the tables */
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

        .container {
            margin: 20px;
        }
    </style>
</head>
<body>

    <header class="header">
        <h1>Queries</h1>
    </header>

    <div class="container">

        <!-- Unresolved Queries Table (Pending Queries) -->
        <h2>Pending Queries:</h2>
        <table>
            <thead>
                <tr>
                    <th>Query ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Created Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($customerInsights && $customerInsights->num_rows > 0) {
                    while ($row = $customerInsights->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['queryid'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['fullname'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['subject'] . "</td>";
                        echo "<td>" . $row['message'] . "</td>";
                        echo "<td>" . $row['createddate'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No unresolved queries available.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Resolved Queries Table -->
        <h2>Resolved Queries</h2>
        <table>
            <thead>
                <tr>
                    <th>Query ID</th>
                    <th>Username</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Created Date</th>
                    <th>Employee Response</th>
                    <th>Resolved Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($queryUpdates && $queryUpdates->num_rows > 0) {
                    while ($row = $queryUpdates->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['queryid'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['subject'] . "</td>";
                        echo "<td>" . $row['message'] . "</td>";
                        echo "<td>" . $row['createddate'] . "</td>";
                        echo "<td>" . $row['employeeresponse'] . "</td>";
                        echo "<td>" . $row['resolveddate'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No resolved queries available.</td></tr>";
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
