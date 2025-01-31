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

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: 2signinpage.php"); // Redirect to login page if not logged in
    exit();
}

// Get the logged-in username
$username = $_SESSION['username'];

// Automated transfer of completed works to allpastworks
$query = "
    SELECT a.*, w.laboursapplied 
    FROM addnewworks a
    LEFT JOIN allworkupdates w 
    ON a.cropname = w.cropname 
    AND a.username = w.username 
    AND a.worktype = w.worktype
    WHERE NOW() >= DATE_ADD(DATE(a.dateofwork), INTERVAL 6 HOUR)"; // Updated to move work after 6:00 AM on dateofwork
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {

    // Check if the entry already exists in allpastworks
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM allpastworks WHERE username = ? AND cropname = ? AND worktype = ? AND dateofwork = ?");
    $check_stmt->bind_param("ssss", $row['username'], $row['cropname'], $row['worktype'], $row['dateofwork']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_row();

    if ($check_row[0] == 0) {
        // Determine the correct value for 'totallabourshired', which is the minimum between laboursapplied and laboursrequired
        $total_labour_hired = min($row['laboursapplied'], $row['laboursrequired']);

        // Insert work into allpastworks table if it does not already exist
        $stmt = $conn->prepare("INSERT INTO allpastworks (username, cropname, worktype, dateofwork, Amount(per labour), totallabourshired, labourtype, completiondate, laboursrequired) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param(
            "ssssdisi",  // Adjusted bind types: s for string, d for double, i for integer
            $row['username'],
            $row['cropname'],
            $row['worktype'],
            $row['dateofwork'],
            $row['amount'], // Assuming 'amount' is a decimal (d)
            $total_labour_hired, // Use the correct value for total labours hired
            $row['labourtype'], // Assuming it's a string (s)
            $row['laboursrequired'] // Add laboursrequired
        );
        $stmt->execute();
    }

    // Delete work from addnewworks
    $stmt_delete = $conn->prepare("DELETE FROM addnewworks WHERE cropname = ? AND username = ? AND worktype = ?");
    $stmt_delete->bind_param("sss", $row['cropname'], $row['username'], $row['worktype']);
    $stmt_delete->execute();

    // Delete work from allworkupdates
    $stmt_delete_updates = $conn->prepare("DELETE FROM allworkupdates WHERE cropname = ? AND username = ? AND worktype = ?");
    $stmt_delete_updates->bind_param("sss", $row['cropname'], $row['username'], $row['worktype']);
    $stmt_delete_updates->execute();
}

// Remove outdated works from addnewworks and allworkupdates after completion
$conn->query("DELETE FROM addnewworks WHERE dateofwork < CURDATE()");
$conn->query("DELETE FROM allworkupdates WHERE dateofwork < CURDATE()");

// Display past works
echo "<h1>Past Works</h1>";
$stmt = $conn->prepare("SELECT cropname, worktype, dateofwork, `Amount(per labour)`, totallabourshired, labourtype, completiondate, laboursrequired FROM allpastworks WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table class='styled-table'>
            <thead>
                <tr>
                    <th>Crop Name</th>
                    <th>Work Type</th>
                    <th>Date of Work</th>
                    <th>Amount</th>
                    <th>Labours Required</th>
                   
                    <th>Labour Type</th>
                     <th>Total Labours Hired</th>
                    <th>Completion Date</th>
                    <th>Percentage of Labours Hired</th> <!-- Added Percentage Column -->
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        // Calculate the percentage of labours hired
        $percentage_hired = ($row['laboursrequired'] > 0) ? ($row['totallabourshired'] / $row['laboursrequired']) * 100 : 0;

        echo "<tr>
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