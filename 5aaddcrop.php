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
    // Get the form data
    $cropname = trim($_POST['cropname']);
    $croptype = $_POST['croptype'];
    $suitablemethod = $_POST['suitablemethod'];
    $cropdescription = $_POST['cropdescription'];

    // Check if the crop already exists
    $stmt_check = $conn->prepare("SELECT * FROM cropdetails WHERE cropname = ?");
    $stmt_check->bind_param("s", $cropname);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Crop already exists
        $message = "<span style='color: red;'>Error: Crop already exists. Please use a different name.</span>";
    } else {
        // Insert new crop into the cropdetails table
        $stmt_insert = $conn->prepare("INSERT INTO cropdetails (cropname, croptype, suitablemethod, cropdescription) 
                                      VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $cropname, $croptype, $suitablemethod, $cropdescription);
        if ($stmt_insert->execute()) {
            // Success message
            $message = "<span style='color: green;'>Crop added successfully!</span>";
        } else {
            // Error message
            $message = "<span style='color: red;'>Error: Unable to add crop. Please try again later.</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Crop - Employee</title>
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

        input[type="text"], select, textarea {
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

<h2>Add New Crop</h2>

<?php if ($message != ''): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="">
    <h3>Crop Details</h3>

    <label for="cropname">Crop Name:</label>
    <input type="text" id="cropname" name="cropname" required>

    <label for="croptype">Crop Type:</label>
    <select id="croptype" name="croptype" required>
        <option value="Food">Food</option>
        <option value="Cash">Cash</option>
        <option value="Spices">Spices</option>
        <option value="Oilseeds">Oilseeds</option>
    </select>

    <label for="suitablemethod">Suitable Method of Cultivation:</label>
    <input type="text" id="suitablemethod" name="suitablemethod" required>

    <label for="cropdescription">Crop Description:</label>
    <textarea id="cropdescription" name="cropdescription" rows="4" required></textarea>

    <button type="submit">Add Crop</button>
</form>

</body>
</html>
