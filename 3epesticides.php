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

// Ensure session username is set
if (!isset($_SESSION['username'])) {
    header("Location: 2signinpage.php"); // Redirect to login page if not logged in
    exit();
}

// Get the logged-in username from the session
$username = $_SESSION['username'];

// Handle searching for pesticides
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchdiseases'])) {
    // Get form input
    $cropname = trim($_POST['cropname']);

    // Initialize variables for search results
    $disease_results = [];

    if (!empty($cropname)) {
        // Prepare SQL to search pesticides by cropname
        $search_sql = "SELECT diseasename, diseasetype, suitablepesticide, quantity, price
                       FROM pesticides
                       WHERE cropname LIKE ?";

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare($search_sql);
        $search_term = "%$cropname%"; // Allow partial matches
        $stmt->bind_param("s", $search_term);

        try {
            // Execute the query
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if any results are found
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $disease_results[] = $row;
                }
            } else {
                $error_message = "No pesticides found for the specified crop!";
            }
        } catch (mysqli_sql_exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    } else {
        $error_message = "Please enter a crop name to search for pesticides.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
    <style>
       body {
            font-family: "Bona Nova SC", serif;
            font-weight: 400;
            font-style: normal;
            margin: 0;
            padding: 0;
            background-size: cover;
            background:white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            overflow-x: hidden;
        }

        
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 30px;
            width: 100%;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-left: 200px;
            gap: 20px;
        }

        .website-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }

        .website-name {
            font-size: 50px;
            font-weight: bold;
            margin-left: 10px;
            color:green;
        }

        .nav-container {
            display: flex;
            justify-content: space-around;
            width: 30%;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding-left: 100px;
        }

        .nav-links a {
            color: blue;
            text-decoration: none;
            font-weight: 400;
            font-size: 20px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #FFD700;
        }

        /* User Profile Styles */
        .user-container {
            position: relative;
            display: inline-block;
            margin-right: 200px;
            padding-left: 10px;
        }

        .user-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ddd;
            border: 2px solid #fff;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .user-logo:hover {
            transform: scale(1.1);
        }

        .user-profile {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 250px;
        }

        .user-container:hover .user-profile {
            display: block;
        }

        .user-profile h4 {
            margin-right: 8px;
            margin-left: 10px;
            color: #4CAF50;
        }

        .user-profile .profile-detail {
            margin-bottom: 8px;
            color: #555;
            font-size: 0.9em;
        }

        .form-container {
            margin-top: 20px;
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        .disease-table {
            margin-top: 30px;
            width: 100%;
            max-width: 800px;
            border-collapse: collapse;
        }

        .disease-table th, .disease-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .disease-table th {
            background-color: #4CAF50;
            color: white;
        }

        .error-message, .success-message {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>
<header class="header">
    <div class="logo">
        <img src="logo.jpg" alt="Website Logo" class="website-logo">
        <span class="website-name">Field Grower</span>
    </div>

    <div class="nav-container">
        <nav class="nav-links">
            <a href="1homepage.html">Home</a>
            <a href="0aboutus.html">About Us</a>
            <a href="0contactus.php">Contact Us</a>
        </nav>
    </div>

    <div class="user-container">
        <img src="userlogo.jpeg" alt="User Logo" class="user-logo" id="user-logo">
        <div class="user-profile">
            <?php
            // Fetch user details from the database
            $stmt = $conn->prepare("SELECT fullname, mobile, email, area, state, zipcode FROM agrarian WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo "<h4>{$user['fullname']}</h4>";
                echo "<div class='profile-detail'>Mobile: {$user['mobile']}</div>";
                echo "<div class='profile-detail'>Email: {$user['email']}</div>";
                echo "<div class='profile-detail'>Address: {$user['area']}, {$user['state']}, {$user['zipcode']}</div>";
            } else {
                echo "<p>User details not found.</p>";
            }
            ?>
        </div>
    </div>
</header>

<div class="form-container">
    <form method="POST" action="">
        <div class="form-group">
            <label for="cropname">Crop Name</label>
            <input type="text" id="cropname" name="cropname" placeholder="Enter crop name to search for pesticides" required>
        </div>
        <div class="form-group">
            <button type="submit" name="searchdiseases">Search Pesticides</button>
        </div>
    </form>
</div>

<?php
if (isset($error_message)) {
    echo "<p class='error-message'>{$error_message}</p>";
} elseif (isset($disease_results) && !empty($disease_results)) {
    echo "<table class='disease-table'>";
    echo "<tr>
            <th>Disease Name</th>
            <th>Disease Type</th>
            <th>Suitable Pesticide</th>
            <th>Quantity</th>
            <th>Price</th>
          </tr>";
    foreach ($disease_results as $disease) {
        echo "<tr>
                <td>{$disease['diseasename']}</td>
                <td>{$disease['diseasetype']}</td>
                <td>{$disease['suitablepesticide']}</td>
                <td>{$disease['quantity']}</td>
                <td>{$disease['price']}</td>
              </tr>";
    }
    echo "</table>";
}
?>
</body>
</html>
