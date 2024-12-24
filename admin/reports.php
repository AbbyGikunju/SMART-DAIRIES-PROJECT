<?php
session_start();

// Check if the user is logged in and if they are an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
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

// Fetch sales data from the database
$sales_sql = "SELECT * FROM sales ORDER BY sale_date DESC";
$sales_result = $conn->query($sales_sql);

// Fetch milk purchases data from the database
$milk_sql = "SELECT * FROM milk_purchases ORDER BY purchase_date DESC";
$milk_result = $conn->query($milk_sql);

// Fetch list of farmers for the dropdown
$farmer_sql = "SELECT DISTINCT farmer_username FROM milk_purchases";
$farmer_result = $conn->query($farmer_sql);

// Handle the download request for a full report
if (isset($_POST['download_report'])) {
    // Get the selected farmer's username
    $selected_farmer = $_POST['farmer_username'];

    // Start with an empty report
    $report = "Sales Report for Farmer: $selected_farmer\n\n";
    
    // Fetch and append sales data for the selected farmer
    if ($sales_result->num_rows > 0) {
        while ($row = $sales_result->fetch_assoc()) {
            // Only include sales related to the selected farmer
            if ($row['customer_username'] === $selected_farmer) {
                $report .= "ID: " . $row['id'] . "\n";
                $report .= "Product Name: " . $row['product_name'] . "\n";
                $report .= "Quantity: " . $row['quantity'] . "\n";
                $report .= "Price: " . $row['price'] . "\n";
                $report .= "Total Price: " . $row['total_price'] . "\n";
                $report .= "Sale Date: " . $row['sale_date'] . "\n";
                $report .= "Customer Username: " . $row['customer_username'] . "\n";
                $report .= "Status: " . $row['status'] . "\n";
                $report .= "Created At: " . $row['created_at'] . "\n\n";
            }
        }
    } else {
        $report .= "No sales records found.\n\n";
    }

    $report .= "Milk Purchases Report for Farmer: $selected_farmer\n\n";

    // Fetch and append milk purchase data for the selected farmer
    if ($milk_result->num_rows > 0) {
        while ($row = $milk_result->fetch_assoc()) {
            // Only include milk purchases for the selected farmer
            if ($row['farmer_username'] === $selected_farmer) {
                $report .= "ID: " . $row['id'] . "\n";
                $report .= "Milk Quantity: " . $row['milk_quantity'] . "\n";
                $report .= "Milk Price: " . $row['milk_price'] . "\n";
                $report .= "Total Price: " . $row['total_price'] . "\n";
                $report .= "Purchase Date: " . $row['purchase_date'] . "\n";
                $report .= "Farmer Username: " . $row['farmer_username'] . "\n";
                $report .= "Status: " . $row['status'] . "\n";
                $report .= "Created At: " . $row['created_at'] . "\n";
                // Skip adding purchase_time if it's not set or empty
                if (isset($row['purchase_time']) && !empty($row['purchase_time'])) {
                    $report .= "Purchase Time: " . $row['purchase_time'] . "\n\n";
                } else {
                    $report .= "Purchase Time: N/A\n\n"; // Or leave it out completely
                }
            }
        }
    } else {
        $report .= "No milk purchase records found.\n\n";
    }

    // Set headers to download the report as a .txt file
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="full_report_' . $selected_farmer . '.txt"');
    echo $report;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-green-600 p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <a href="#" class="text-white text-2xl font-bold">Smart Dairies</a>
        <div class="flex items-center space-x-4">
            <p class="text-white font-medium"><?php echo $_SESSION['username']; ?></p>
            <a href="logout.php" class="text-white px-4 py-2 rounded hover:bg-green-500">Log Out</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto px-4 py-10">
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Admin Reports</h1>

    <!-- Farmer Selection -->
    <form method="POST" action="" class="mb-8 text-center">
        <label for="farmer_username" class="block text-lg font-semibold mb-2">Select Farmer:</label>
        <select name="farmer_username" id="farmer_username" class="border p-2 rounded mb-4">
            <option value="">Select a farmer</option>
            <?php
            // Display farmers in the dropdown
            if ($farmer_result->num_rows > 0) {
                while ($row = $farmer_result->fetch_assoc()) {
                    echo "<option value='" . $row['farmer_username'] . "'>" . $row['farmer_username'] . "</option>";
                }
            } else {
                echo "<option value=''>No farmers available</option>";
            }
            ?>
        </select>
        <button type="submit" name="download_report" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-500">Download Full Report</button>
    </form>

    <!-- Sales Report Section -->
    <h2 class="text-2xl font-semibold mb-4">Sales Report</h2>
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Product Name</th>
                    <th class="px-4 py-2">Quantity</th>
                    <th class="px-4 py-2">Price</th>
                    <th class="px-4 py-2">Total Price</th>
                    <th class="px-4 py-2">Sale Date</th>
                    <th class="px-4 py-2">Customer Username</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Displaying Sales Data
                if ($sales_result->num_rows > 0) {
                    while ($row = $sales_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='px-4 py-2'>" . $row['id'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['product_name'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['quantity'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['price'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['total_price'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['sale_date'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['customer_username'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['status'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['created_at'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='px-4 py-2 text-center'>No sales records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Milk Purchases Report Section -->
    <h2 class="text-2xl font-semibold mb-4">Milk Purchases Report</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Milk Quantity</th>
                    <th class="px-4 py-2">Milk Price</th>
                    <th class="px-4 py-2">Total Price</th>
                    <th class="px-4 py-2">Purchase Date</th>
                    <th class="px-4 py-2">Farmer Username</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Created At</th>
                    <th class="px-4 py-2">Purchase Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Displaying Milk Purchases Data
                if ($milk_result->num_rows > 0) {
                    while ($row = $milk_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='px-4 py-2'>" . $row['id'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['milk_quantity'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['milk_price'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['total_price'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['purchase_date'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['farmer_username'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['status'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['created_at'] . "</td>";
                        // Skip displaying purchase_time if it's not set or empty
                        if (isset($row['purchase_time']) && !empty($row['purchase_time'])) {
                            echo "<td class='px-4 py-2'>" . $row['purchase_time'] . "</td>";
                        } else {
                            echo "<td class='px-4 py-2'>N/A</td>"; // Or leave it empty if you prefer
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='px-4 py-2 text-center'>No milk purchase records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
