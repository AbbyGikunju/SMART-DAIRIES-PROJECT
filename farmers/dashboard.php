<?php
// Start session to ensure user is logged in
session_start();

// Check if the user is logged in and if they are a farmer (or the appropriate role)
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "farmer") {
    // If not logged in or not a farmer, redirect to login page
    header("location: login.php");
    exit;
}

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'biodairy';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in farmer's username from session
$farmer_username = $_SESSION['username'];

// Query to count the number of pending milk purchases for the farmer
$sql = "SELECT COUNT(*) AS pending_count FROM milk_purchases WHERE farmer_username = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $farmer_username);  // Bind the session username
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$pending_count = $row['pending_count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Material Icons CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Custom background color */
        body {
            background-color: #f9fafb; /* Light gray background */
        }
        .card {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add transition for hover effects */
            background-color: #ffffff; /* White background for better contrast */
        }
        .card:hover {
            transform: translateY(-5px); /* Slightly lift the card on hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* Enhance shadow on hover */
        }
        .icon {
            color: black; /* Set icon color to black */
        }
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #f87171; /* Red background for the badge */
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="bg-green-600 p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <a href="#" class="text-white text-2xl font-bold">Smart Dairies</a>
        <div class="flex items-center space-x-4">
            <!-- Display the logged-in username at the top right -->
            <p class="text-white font-medium"><?php echo $_SESSION["username"]; ?></p>
            <!-- Log Out Button -->
            <a href="logout.php" class="text-white px-4 py-2 rounded hover:bg-green-500">Log Out</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto px-4 py-10">
    <!-- Welcome Message -->
    <div class="text-center mb-8">
        <p class="text-xl font-medium text-gray-800">Welcome, <?php echo $_SESSION["username"]; ?>!</p>
    </div>
    
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Farmer Dashboard</h1>
    
    <!-- Cards Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        <!-- Milk Tracking Card -->
        <div class="card w-full p-6 rounded-xl">
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-blue-500">local_drink</span> <!-- Blue Icon for Milk Tracking -->
            </div>
            <h5 class="text-xl font-semibold mb-4">Milk Tracking</h5>
            <p class="text-gray-600">Track your milk production or delivery, including details such as quantity, quality, and collection schedule.</p>
            <a href="milk.php" class="text-green-600 font-semibold">Go to Milk Tracking</a>
        </div>

        <!-- Notifications Card -->
        <div class="card w-full p-6 rounded-xl relative">
            <!-- Notification Badge for Pending -->
            <?php if ($pending_count > 0): ?>
                <div class="notification-badge"><?php echo $pending_count; ?></div>
            <?php endif; ?>
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-red-500">notifications</span> <!-- Red Icon for Notifications -->
            </div>
            <h5 class="text-xl font-semibold mb-4">Notifications</h5>
            <p class="text-gray-600">View your alerts regarding collection schedules, payment statuses, and other important information.</p>
            <a href="notifications.php" class="text-green-600 font-semibold">View Notifications</a>
        </div>

        <!-- Generate Report Card -->
        <div class="card w-full p-6 rounded-xl">
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-purple-500">assessment</span> <!-- Purple Icon for Generate Report -->
            </div>
            <h5 class="text-xl font-semibold mb-4">Generate Report</h5>
            <p class="text-gray-600">Generate reports on your milk production, sales, and performance. Get detailed insights to optimize your farming operations.</p>
            <a href="report.php" class="text-green-600 font-semibold">Generate Report</a>
        </div>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection at the very end of the script
$stmt->close();
$conn->close();
?>
