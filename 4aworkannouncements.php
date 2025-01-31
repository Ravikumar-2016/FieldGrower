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

// Fetch the logged-in user's region from the agrarian table
$stmt = $conn->prepare("SELECT area, state FROM agrarian WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_region = null;

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_region = $user['area'] . ', ' . $user['state']; // Combine area and state to form region
}

// If no region is found, exit
if (!$user_region) {
    echo "Region not found for the user.";
    exit();
}

// Handle work application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $cropname = $_POST['cropname'];
    $worktype = $_POST['worktype'];
    $labour_name = trim($_POST['labour_name']);
    $labour_mobile = trim($_POST['labour_mobile']);
    
    // Fetch the existing labour details, count of labours applied, and labours required for the specific work
    $stmt = $conn->prepare("SELECT username, labourdetails, laboursapplied, laboursrequired FROM allworkupdates WHERE cropname = ? AND worktype = ?");
    $stmt->bind_param("ss", $cropname, $worktype);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $work = $result->fetch_assoc();
        $work_username = $work['username']; // Get the farmer's username from the allworkupdates table
        $labourdetails = json_decode($work['labourdetails'], true); // Decode the existing labour details into a variable
        
        // If JSON is invalid or empty, initialize an empty array
        if ($labourdetails === null) {
            $labourdetails = [];
        }

        $laboursapplied = $work['laboursapplied'];
        $laboursrequired = $work['laboursrequired']; // Get the required number of labours

        // Check if the labour has already applied
        foreach ($labourdetails as $labour) {
            if ($labour['labour_name'] === $labour_name && $labour['labour_mobile'] === $labour_mobile) {
                echo "<p class='error-message'>You have already applied for this work.</p>";
                exit();
            }
        }

        // Check if the laboursapplied is less than laboursrequired
        if ($laboursapplied >= $laboursrequired) {
            echo "<p class='error-message'>The maximum number of labours for this work has already been reached. You cannot apply.</p>";
            exit();
        }

        // Fetch area and state using the username from the agrarian table
        $region_stmt = $conn->prepare("SELECT area, state FROM agrarian WHERE username = ?");
        $region_stmt->bind_param("s", $work_username);
        $region_stmt->execute();
        $region_result = $region_stmt->get_result();

        if ($region_result->num_rows > 0) {
            // Fetch region details
            $region_data = $region_result->fetch_assoc();
            $work_region = $region_data['area'] . ', ' . $region_data['state']; // Use the variable

            // Check if the work region matches the user's region
            if ($work_region === $user_region) {
                // Add new labour details to the JSON array
                $labourdetails[] = [
                    'labour_name' => $labour_name,
                    'labour_mobile' => $labour_mobile
                ];

                // Increment the count of labours applied
                $laboursapplied++;

                // Encode the updated labourdetails back to JSON
                $encoded_labourdetails = json_encode($labourdetails);  // Assign to a variable first

                // Update the labourdetails JSON and laboursapplied count in the database
                $stmt = $conn->prepare("UPDATE allworkupdates SET labourdetails = ?, laboursapplied = ? WHERE cropname = ? AND worktype = ?");
                $stmt->bind_param("siss", $encoded_labourdetails, $laboursapplied, $cropname, $worktype);

                if ($stmt->execute()) {
                    echo "<p class='success-message'>You have successfully applied for the work.</p>";
                } else {
                    echo "<p class='error-message'>Failed to apply for the work. Please try again later.</p>";
                }
            } else {
                echo "<p class='error-message'>You cannot apply for work outside your region.</p>";
            }
        } else {
            echo "<p class='error-message'>Region details for the farmer not found.</p>";
        }
    } else {
        echo "<p class='error-message'>Work not found or invalid work details.</p>";
    }
}

// Fetch work announcements for the user's region
$sql = "SELECT wu.username, wu.cropname, wu.worktype, wu.dateofwork, wu.amount, wu.laboursrequired, a.fullname, a.mobile 
        FROM allworkupdates wu 
        JOIN agrarian a ON a.username = wu.username 
        WHERE CONCAT(a.area, ', ', a.state) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_region);
$stmt->execute();
$work_announcements = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Announcements</title>
    <style>
        /* Your styles here */
        .table-container {
            width: 100%;
            margin-top: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .apply-form {
            display: none;
            margin-top: 20px;
            text-align: center;
        }

        .apply-form.active {
            display: block;
        }

        .apply-form input {
            margin: 5px;
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
    <h2>Work Announcements</h2>

    <?php if ($work_announcements->num_rows > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Farmer Name</th>
                        <th>Farmer Mobile</th>
                        <th>Crop Name</th>
                        <th>Work Type</th>
                        <th>Date of Work</th>
                        <th>Amount</th>
                        <th>Apply</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($work = $work_announcements->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $work['fullname']; ?></td>
                            <td><?php echo $work['mobile']; ?></td>
                            <td><?php echo $work['cropname']; ?></td>
                            <td><?php echo $work['worktype']; ?></td>
                            <td><?php echo $work['dateofwork']; ?></td>
                            <td><?php echo $work['amount']; ?></td>
                            <td><a href="#" class="apply-link" data-cropname="<?php echo $work['cropname']; ?>" data-worktype="<?php echo $work['worktype']; ?>">Apply</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No work announcements found for your region.</p>
    <?php endif; ?>

    <div class="apply-form" id="apply-form">
        <h3>Apply for Work</h3>
        <form method="POST">
            <input type="hidden" name="cropname" id="cropname">
            <input type="hidden" name="worktype" id="worktype">
            <label for="labour_name">Labour Name</label><br>
            <input type="text" name="labour_name" id="labour_name" required><br>
            <label for="labour_mobile">Labour Mobile</label><br>
            <input type="text" name="labour_mobile" id="labour_mobile" required><br>
            <button type="submit" name="apply">Submit Application</button>
        </form>
    </div>

    <script>
        document.querySelectorAll('.apply-link').forEach(link => {
            link.addEventListener('click', function() {
                const cropname = this.getAttribute('data-cropname');
                const worktype = this.getAttribute('data-worktype');
                document.getElementById('cropname').value = cropname;
                document.getElementById('worktype').value = worktype;
                document.getElementById('apply-form').classList.add('active');
            });
        });
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
