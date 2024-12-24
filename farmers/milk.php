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

// If a status update is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    // Get the milk purchase ID and new status from the form
    $purchase_id = $_POST['purchase_id'];
    $new_status = $_POST['status'];

    // Update the status of the milk purchase
    $update_sql = "UPDATE milk_purchases SET status = ? WHERE id = ? AND farmer_username = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('sis', $new_status, $purchase_id, $farmer_username);
    if ($stmt->execute()) {
        $notification_message = "Status updated successfully!";
    } else {
        $notification_message = "Failed to update the status. Please try again.";
    }
}

// Query to fetch the milk purchases for the farmer
$sql = "SELECT * FROM milk_purchases WHERE farmer_username = ? ORDER BY purchase_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $farmer_username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - View Milk Purchases</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #f9fafb; 
        }
        .card {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #ffffff; 
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="bg-green-600 p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <a href="#" class="text-white text-2xl font-bold">Smart Dairies</a>
        <div class="flex items-center space-x-4">
            <p class="text-white font-medium"><?php echo $_SESSION["username"]; ?></p>
            <a href="logout.php" class="text-white px-4 py-2 rounded hover:bg-green-500">Log Out</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto px-4 py-10">
    <div class="text-center mb-8">
        <p class="text-xl font-medium text-gray-800">Welcome, <?php echo $_SESSION["username"]; ?>!</p>
    </div>
    
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Milk Purchases - View and Update Status</h1>

    <!-- Display Notification -->
    <?php if (isset($notification_message)): ?>
        <div class="bg-green-500 text-white p-4 rounded-md mb-6 text-center">
            <?php echo $notification_message; ?>
        </div>
    <?php endif; ?>

    <!-- Milk Purchases Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Milk Quantity</th>
                    <th class="px-4 py-2">Milk Price</th>
                    <th class="px-4 py-2">Total Price</th>
                    <th class="px-4 py-2">Purchase Date</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='px-4 py-2'>" . $row['id'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['milk_quantity'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['milk_price'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['total_price'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['purchase_date'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['status'] . "</td>";
                        echo "<td class='px-4 py-2'>
                            <form action='milk.php' method='POST'>
                                <input type='hidden' name='purchase_id' value='" . $row['id'] . "'>
                                <select name='status' class='border rounded p-2'>
                                    <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                    <option value='approved' " . ($row['status'] == 'approved' ? 'selected' : '') . ">Approved</option>
                                    <option value='rejected' " . ($row['status'] == 'rejected' ? 'selected' : '') . ">Rejected</option>
                                </select>
                                <button type='submit' name='update_status' class='ml-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-500'>Update</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='px-4 py-2 text-center'>No milk purchases found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
