<?php
// Start session to ensure user is logged in
session_start();

// Database configuration
$host = 'localhost'; // Database host
$username = 'root'; // Database username
$password = ''; // Database password
$dbname = 'biodairy'; // Database name

// Create connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the user is logged in and if they are an employee
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "employee") {
    // If not logged in or not an employee, redirect to login page
    header("location: login.php");
    exit;
}

// Initialize variables
$product_name = "";
$quantity = "";
$price = "";
$total_price = 0;
$sale_date = date('Y-m-d'); // Default to today's date
$customer_username = "";
$customer_options = "";
$status = "pending"; // Default status is "pending"

// Fetch customers or farmers from the users table
$sql = "SELECT username FROM users WHERE role IN ('farmer', 'customer')";
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customer_options .= "<option value='" . $row['username'] . "'>" . $row['username'] . "</option>";
    }
} else {
    echo "No customers or farmers found in the system.";
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_name'])) {
    // Sanitize and assign form data
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $sale_date = $_POST['sale_date'];
    $customer_username = $_POST['customer_username'];
    $username = $_SESSION['username']; // Get logged-in employee username

    // Calculate total price (quantity * price per unit)
    $total_price = $quantity * $price;

    // SQL query to insert sales record
    $sql = "INSERT INTO sales (product_name, quantity, price, total_price, sale_date, customer_username, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("siddsss", $product_name, $quantity, $price, $total_price, $sale_date, $customer_username, $status);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect after successful entry
            header("location: sales.php");
            exit();
        } else {
            echo "Error: Could not execute the query. " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Error: Could not prepare the query. " . $mysqli->error;
    }
}

// Handle sales record filtering (optional)
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// SQL query for filtering sales by date range
$sales_sql = "SELECT * FROM sales WHERE sale_date BETWEEN ? AND ? ORDER BY sale_date DESC";
if ($stmt = $mysqli->prepare($sales_sql)) {
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "Error: Could not prepare the query. " . $mysqli->error;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

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
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Record Sale</h1>

    <!-- Sales Record Form -->
    <form method="POST" action="sales.php" class="max-w-lg mx-auto bg-white p-6 rounded shadow-lg">
        <div class="mb-4">
            <label for="product_name" class="block text-lg font-medium text-gray-700">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="w-full p-3 border rounded-lg mt-2" value="<?php echo $product_name; ?>" required>
        </div>
        <div class="mb-4">
            <label for="quantity" class="block text-lg font-medium text-gray-700">Quantity (in units)</label>
            <input type="number" id="quantity" name="quantity" class="w-full p-3 border rounded-lg mt-2" value="<?php echo $quantity; ?>" required>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-lg font-medium text-gray-700">Price per Unit</label>
            <input type="number" id="price" name="price" class="w-full p-3 border rounded-lg mt-2" value="<?php echo $price; ?>" required>
        </div>
        <div class="mb-4">
            <label for="sale_date" class="block text-lg font-medium text-gray-700">Sale Date</label>
            <input type="date" id="sale_date" name="sale_date" class="w-full p-3 border rounded-lg mt-2" value="<?php echo $sale_date; ?>" required>
        </div>

        <div class="mb-4">
            <label for="customer_username" class="block text-lg font-medium text-gray-700">Select Customer or Farmer</label>
            <select id="customer_username" name="customer_username" class="w-full p-3 border rounded-lg mt-2" required>
                <option value="">-- Select Customer or Farmer --</option>
                <?php echo $customer_options; ?>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-500">Record Sale</button>
        </div>
    </form>
</div>

<!-- Sales Records Filter Form -->
<div class="container mx-auto px-4 py-10 mt-8">
    <h2 class="text-2xl text-center font-semibold text-gray-800 mb-8">Filter Sales Records</h2>
    <form method="POST" action="sales.php" class="max-w-lg mx-auto bg-white p-6 rounded shadow-lg">
        <div class="mb-4">
            <label for="start_date" class="block text-lg font-medium text-gray-700">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="w-full p-3 border rounded-lg mt-2" value="<?php echo $start_date; ?>">
        </div>
        <div class="mb-4">
            <label for="end_date" class="block text-lg font-medium text-gray-700">End Date</label>
            <input type="date" id="end_date" name="end_date" class="w-full p-3 border rounded-lg mt-2" value="<?php echo $end_date; ?>">
        </div>
        <div class="text-center">
            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-500">Filter</button>
        </div>
    </form>
</div>

<!-- Display Sales Records -->
<div class="container mx-auto px-4 py-10 mt-8">
    <h2 class="text-2xl text-center font-semibold text-gray-800 mb-8">Sales Records</h2>
    <table class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-green-600 text-white">
                <th class="px-4 py-2">Product Name</th>
                <th class="px-4 py-2">Quantity</th>
                <th class="px-4 py-2">Price per Unit</th>
                <th class="px-4 py-2">Total Price</th>
                <th class="px-4 py-2">Sale Date</th>
                <th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='px-4 py-2'>" . $row['product_name'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['quantity'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['price'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['total_price'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['sale_date'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['customer_username'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['status'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='px-4 py-2 text-center'>No sales records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close the database connection
$mysqli->close();
?>
