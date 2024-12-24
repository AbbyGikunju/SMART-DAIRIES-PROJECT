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

// Check if the download button was clicked
if (isset($_GET['download']) && $_GET['download'] == 'txt') {
    // Fetch the data again for download
    $sql_download = "SELECT * FROM milk_purchases WHERE farmer_username = ?";
    $stmt_download = $conn->prepare($sql_download);
    $stmt_download->bind_param('s', $farmer_username);  // Bind the session username
    $stmt_download->execute();
    $download_result = $stmt_download->get_result();
    
    // Create the download content
    $content = "Milk Purchases Report\n\n";
    $content .= "ID | Milk Quantity (L) | Milk Price (per L) | Total Price | Purchase Date | Purchase Time | Status | Created At\n";
    $content .= "--------------------------------------------------------------------------\n";
    
    // Append data to the content
    while ($row = $download_result->fetch_assoc()) {
        $content .= $row['id'] . " | " . $row['milk_quantity'] . " | " . $row['milk_price'] . " | " . $row['total_price'] . " | " . $row['purchase_date'] . " | " . $row['purchase_time'] . " | " . ucfirst($row['status']) . " | " . $row['created_at'] . "\n";
    }

    // Set headers to download the file
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="milk_purchases_report.txt"');
    echo $content;
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Purchases - Farmer Report</title>
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
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Milk Purchases Report</h1>

    <!-- Displaying Milk Purchase Records -->
    <div class="overflow-x-auto mb-4">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Milk Quantity (L)</th>
                    <th class="px-4 py-2">Milk Price (per L)</th>
                    <th class="px-4 py-2">Total Price</th>
                    <th class="px-4 py-2">Purchase Date</th>
                    <th class="px-4 py-2">Purchase Time</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display the milk purchases for the logged-in farmer
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='px-4 py-2'>" . $row['id'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['milk_quantity'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['milk_price'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['total_price'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['purchase_date'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['purchase_time'] . "</td>";
                        echo "<td class='px-4 py-2'>" . ucfirst($row['status']) . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['created_at'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='px-4 py-2 text-center'>No purchases found for this farmer.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Download Button -->
    <div class="flex justify-center mt-4">
        <a href="?download=txt" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-500">Download as TXT</a>
    </div>

</div>

</body>
</html>

<?php
// Close the database connection at the very end of the script
$stmt->close();
$conn->close();
?>
