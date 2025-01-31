<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "Ravikumar123";
$dbname = "fieldgrower";

// Start session for employee login
session_start();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message_status = "";

// Handle resolve form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_resolve_query'])) {
    $queryid = $conn->real_escape_string($_POST['queryid']);
    $username = $conn->real_escape_string($_POST['username']);
    $employee_response = $conn->real_escape_string($_POST['employee_response']);
    $empid = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown'; // Logged-in employee's username
    $resolved_date = date('Y-m-d H:i:s');
    $status = "Resolved";

    // Check if the query is already resolved
    $check_query = "SELECT * FROM queryupdates WHERE queryid = '$queryid' AND username = '$username'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $message_status = "Query is already resolved.";
    } else {
        // Insert the resolution into queryupdates table
        $resolve_query = "INSERT INTO queryupdates (queryid, username, status, employeeresponse, empid, resolveddate)
                          VALUES ('$queryid', '$username', '$status', '$employee_response', '$empid', '$resolved_date')";

        if ($conn->query($resolve_query) === TRUE) {
            $message_status = "Query resolved successfully.";
        } else {
            $message_status = "Error: " . $resolve_query . "<br>" . $conn->error;
        }
    }
}

// Fetch all queries with their status
$query = "SELECT ci.*, 
                 IFNULL(qu.status, 'Pending') AS status
          FROM customerinsights ci
          LEFT JOIN queryupdates qu ON ci.queryid = qu.queryid AND ci.username = qu.username";
$result = $conn->query($query);

// Check if "Resolve" button was clicked to show the resolve form
$show_resolve_form = false;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['resolve_query'])) {
    $queryid = $_POST['queryid'];
    $username = $_POST['username'];
    $status = $_POST['status'];

    if ($status === "Resolved") {
        $message_status = "Query is already resolved.";
    } else {
        $show_resolve_form = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customer Queries</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #2f8f2e;
            color: #fff;
        }
        button {
            padding: 10px 15px;
            color: #fff;
            background-color: #2f8f2e;
            border: none;
            cursor: pointer;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .form-container {
            margin: 20px 0;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
    <script>
        function showMessage(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Customer Queries</h1>
        <?php if ($message_status): ?>
            <p style="color: green;"><?php echo $message_status; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Query ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['queryid']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['fullname']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['createddate']; ?></td>
                        <td><?php echo $row['subject']; ?></td>
                        <td><?php echo $row['message']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <?php if ($row['status'] === "Resolved"): ?>
                                <button disabled>Resolved</button>
                            <?php else: ?>
                                <form action="" method="POST">
                                    <input type="hidden" name="queryid" value="<?php echo $row['queryid']; ?>">
                                    <input type="hidden" name="username" value="<?php echo $row['username']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $row['status']; ?>">
                                    <button type="submit" name="resolve_query">Resolve</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Resolve Form -->
        <?php if ($show_resolve_form): ?>
            <div class="form-container">
                <h2>Resolve Query</h2>
                <form action="" method="POST">
                    <input type="hidden" name="queryid" value="<?php echo $queryid; ?>">
                    <input type="hidden" name="username" value="<?php echo $username; ?>">
                    <label for="employee_response">Employee Response:</label>
                    <textarea id="employee_response" name="employee_response" rows="4" required></textarea>
                    <button type="submit" name="submit_resolve_query">Resolve</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
