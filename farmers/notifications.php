<?php
session_start();

// Check if user is logged in and if they are a farmer
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'farmer') {
    header('Location: login.php');
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

// Fetch milk purchases for the logged-in farmer
$sql = "SELECT * FROM milk_purchases WHERE farmer_username = ? ORDER BY purchase_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $farmer_username);  // Bind the session username
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Purchases - Status Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-green-600 p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <a href="#" class="text-white text-2xl font-bold">Smart Dairies</a>
        <div class="flex items-center space-x-4">
            <!-- Display the logged-in username at the top right -->
            <p class="text-white font-medium"><?php echo $_SESSION['username']; ?></p>
            <!-- Log Out Button -->
            <a href="logout.php" class="text-white px-4 py-2 rounded hover:bg-green-500">Log Out</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto px-4 py-10">
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Milk Purchases - Status Notifications</h1>

    <!-- Displaying Milk Purchase Status -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="px-4 py-2">Purchase ID</th>
                    <th class="px-4 py-2">Purchase Date</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display the milk purchases for the logged-in farmer
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = $row['status'];
                        echo "<tr>";
                        echo "<td class='px-4 py-2'>" . $row['id'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['purchase_date'] . "</td>";
                        
                        // Display notification for pending status
                        echo "<td class='px-4 py-2'>";
                        if ($status === 'pending') {
                            // Notification style for 'pending' status
                            echo "<div class='bg-yellow-300 text-black font-semibold p-2 rounded-md'>Pending</div>";
                        } else {
                            // Display normal status for approved or rejected
                            echo "<span class='text-green-600 font-semibold'>" . ucfirst($status) . "</span>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='px-4 py-2 text-center'>No purchases found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection at the very end of the script
$stmt->close();
$conn->close();
?>
