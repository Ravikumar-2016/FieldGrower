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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cropname = $_POST['cropname']; // Crop name to delete

    // Check if the crop exists in cropdetails table
    $stmt_check = $conn->prepare("SELECT * FROM cropdetails WHERE cropname = ?");
    $stmt_check->bind_param("s", $cropname);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Crop exists, proceed with deletion

        // Delete related records from fertilizers table
        $stmt_delete_fertilizers = $conn->prepare("DELETE FROM fertilizers WHERE cropname = ?");
        $stmt_delete_fertilizers->bind_param("s", $cropname);
        $stmt_delete_fertilizers->execute();

        // Delete related records from pesticides table
        $stmt_delete_pesticides = $conn->prepare("DELETE FROM pesticides WHERE cropname = ?");
        $stmt_delete_pesticides->bind_param("s", $cropname);
        $stmt_delete_pesticides->execute();

        // Now delete the crop from cropdetails table
        $stmt_delete_crop = $conn->prepare("DELETE FROM cropdetails WHERE cropname = ?");
        $stmt_delete_crop->bind_param("s", $cropname);

        if ($stmt_delete_crop->execute()) {
            $message = "<span style='color: green;'>Crop and related data (fertilizers & pesticides) deleted successfully.</span>";
        } else {
            $message = "<span style='color: red;'>Error deleting crop. Please try again.</span>";
        }
    } else {
        $message = "<span style='color: red;'>Crop not found in the database.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Crop - Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #f4f4f9;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        form {
            border: 1px solid #ddd;
            padding: 20px;
            background: #fff;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        form h3 {
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 1rem;
        }

        button {
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            border-radius: 3px;
        }

        button:hover {
            background: #0056b3;
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

<h2>Delete Crop</h2>

<?php if ($message != ''): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<!-- Form for deleting crop -->
<form method="POST" action="">
    <h3>Enter Crop Name to Delete</h3>
    <label>Crop Name:</label>
    <input type="text" name="cropname" required><br>

    <button type="submit">Delete Crop</button>
</form>

</body>
</html>
