<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "Ravikumar123";
$dbname = "fieldgrower";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message_status = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect form inputs
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);
    $date = date('Y-m-d H:i:s');

    // Fetch username from the agrarian table using fullname and email
    $user_check_query = "SELECT Username FROM agrarian WHERE FullName = '$fullname' AND Email = '$email'";
    $result = $conn->query($user_check_query);

    if ($result->num_rows > 0) {
        // Username found, fetch the username
        $row = $result->fetch_assoc();
        $username = $row['Username'];

        // Insert query into the customerinsights table
        $insert_query = "INSERT INTO customerinsights (username, fullname, email, createddate, subject, message) 
                         VALUES ('$username', '$fullname', '$email', '$date', '$subject', '$message')";

        if ($conn->query($insert_query) === TRUE) {
            $message_status = "Your query has been submitted successfully.";
        } else {
            $message_status = "Error: " . $insert_query . "<br>" . $conn->error;
        }
    } else {
        // User not found
        $message_status = "User not found. Please ensure your details are correct.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        #contactus {
            padding: 50px;
            background-color: #e9f5e9;
        }
        h2 {
            font-size: 32px;
            color: #2f8f2e;
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #333;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #2f8f2e;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #276a25;
        }
        .message {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <section id="contactus">
        <h2>Contact Us</h2>

        <!-- Display message status -->
        <?php if ($message_status): ?>
            <p class="message"><?php echo $message_status; ?></p>
        <?php endif; ?>

        <!-- Contact form -->
        <form action="0contactus.php" method="POST">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
            
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="6" required></textarea>
            
            <button type="submit">Send Message</button>
        </form>
    </section>
</body>
</html>