<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "Ravikumar123";
$dbname = "fieldgrower";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for valid session
if (!isset($_SESSION['username'])) {
    header("Location: 2signinpage.php");
    exit();
}

// Get the logged-in username
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input values
    $cropname = htmlspecialchars($_POST['cropname']);
    $worktype = htmlspecialchars($_POST['worktype']);
    $dateofwork = $_POST['dateofwork'];
    $timeofwork = $_POST['timeofwork'];
    $laboursrequired = (int)$_POST['laboursrequired'];
    $amount = (float)$_POST['amount'];
    $labourtype = htmlspecialchars($_POST['labourtype']);

    // Combine date and time
    $datetimeofwork = $dateofwork . ' ' . $timeofwork;

    // Validate datetime
    if (strtotime($datetimeofwork) <= time()) {
        echo "<p style='color: red;'>Date and time of work must be in the future.</p>";
    } else {
        $conn->begin_transaction();

        try {
            // Insert into addnewworks table
            $stmt = $conn->prepare(
                "INSERT INTO addnewworks (username, cropname, worktype, dateofwork, laboursrequired, amount, labourtype, createddate) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)"
            );
            $stmt->bind_param("ssssiss", $username, $cropname, $worktype, $datetimeofwork, $laboursrequired, $amount, $labourtype);

            if (!$stmt->execute()) {
                throw new Exception("Error inserting into addnewworks.");
            }

            // Insert into allworkupdates table
            $stmt_update = $conn->prepare(
                "INSERT INTO allworkupdates (username, cropname, worktype, dateofwork, laboursrequired, amount, laboursapplied, labourdetails) 
                 VALUES (?, ?, ?, ?, ?, ?, 0, NULL)"
            );
            $stmt_update->bind_param("ssssii", $username, $cropname, $worktype, $datetimeofwork, $laboursrequired, $amount);

            if (!$stmt_update->execute()) {
                throw new Exception("Error inserting into allworkupdates.");
            }

            $conn->commit();
            echo "<p style='color: green;'>Work added successfully!</p>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p style='color: red;'>Transaction failed: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Work</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .form-container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); max-width: 600px; margin: 0 auto; }
        label { display: block; margin: 10px 0 5px; }
        input, select { width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 5px; }
        input[type="submit"] { background-color: #4CAF50; color: white; border: none; cursor: pointer; padding: 12px; font-size: 16px; }
        input[type="submit"]:hover { background-color: #45a049; }
    </style>
    <script>
        window.onload = function () {
            let today = new Date().toISOString().split('T')[0];
            document.getElementById("dateofwork").setAttribute("min", today);
        };
    </script>
</head>
<body>
<div class="form-container">
    <h2>Add New Work</h2>
    <form method="POST" action="">
        <label for="cropname">Crop Name:</label>
        <input type="text" id="cropname" name="cropname" required>

        <label for="worktype">Work Type:</label>
        <input type="text" id="worktype" name="worktype" required>

        <label for="dateofwork">Date of Work:</label>
        <input type="date" id="dateofwork" name="dateofwork" required>

        <label for="timeofwork">Time of Work:</label>
        <input type="time" id="timeofwork" name="timeofwork" min="09:00" max="18:00" required>

        <label for="laboursrequired">Labours Required:</label>
        <input type="number" id="laboursrequired" name="laboursrequired" required>

        <label for="amount">Amount (Per Labour):</label>
        <input type="number" id="amount" name="amount" step="0.01" required>

        <label for="labourtype">Labour Type:</label>
        <input type="text" id="labourtype" name="labourtype" required>

        <input type="submit" value="Add Work">
    </form>
</div>
</body>
</html>
