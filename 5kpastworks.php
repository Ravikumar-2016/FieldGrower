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

// Display all past works
echo "<h1>Past Works</h1>";
$stmt = $conn->prepare("SELECT username, cropname, worktype, dateofwork, `Amount(per labour)`, totallabourshired, labourtype, completiondate, laboursrequired FROM allpastworks");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table class='styled-table'>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Crop Name</th>
                    <th>Work Type</th>
                    <th>Date of Work</th>
                    <th>Amount</th>
                    <th>Labours Required</th>
                    <th>Labour Type</th>
                    <th>Total Labours Hired</th>
                    <th>Completion Date</th>
                    <th>Percentage of Labours Hired</th> <!-- New column -->
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        // Calculate the percentage of labours hired
        $percentage_hired = ($row['laboursrequired'] > 0) ? ($row['totallabourshired'] / $row['laboursrequired']) * 100 : 0;

        echo "<tr>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['cropname']) . "</td>
                <td>" . htmlspecialchars($row['worktype']) . "</td>
                <td>" . htmlspecialchars($row['dateofwork']) . "</td>
                <td>" . htmlspecialchars($row['Amount(per labour)']) . "</td>
                <td>" . htmlspecialchars($row['laboursrequired']) . "</td>
                <td>" . htmlspecialchars($row['labourtype']) . "</td>
                <td>" . htmlspecialchars($row['totallabourshired']) . "</td>
                <td>" . htmlspecialchars($row['completiondate']) . "</td>
                <td>" . number_format($percentage_hired, 2) . "%</td> <!-- Show percentage -->
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No past works available.</p>";
}
?>

<style>
    /* Style for the table */
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 18px;
        text-align: left;
    }
    .styled-table th, .styled-table td {
        padding: 12px;
        border: 1px solid #ddd;
    }
    .styled-table th {
        background-color: #4CAF50;
        color: white;
    }
    .styled-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .styled-table tr:hover {
        background-color: #ddd;
    }
    .styled-table td {
        color: #555;
    }
    h1 {
        text-align: center;
        color: #4CAF50;
    }
</style>
