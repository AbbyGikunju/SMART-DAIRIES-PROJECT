<?php
// Start session to ensure user is logged in
session_start();

// Check if the user is logged in and if they are an employee
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "employee") {
    // If not logged in or not an employee, redirect to login page
    header("location: login.php");
    exit;
}

// Database connection
$host = "localhost"; // Database host (usually localhost)
$dbname = "biodairy"; // Database name
$username = "root"; // Database username (change this to your actual username)
$password = ""; // Database password (change this to your actual password)

$mysqli = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize variables
$milk_quantity = "";
$milk_price = "";
$purchase_date = "";
$farmer_username = "";
$farmer_options = "";
$total_price = 0;

// Fetch farmers from the users table
$sql = "SELECT username FROM users WHERE role = 'farmer'";
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $farmer_options .= "<option value='" . $row['username'] . "'>" . $row['username'] . "</option>";
    }
} else {
    echo "No farmers found in the system.";
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and assign form data
    $milk_quantity = $_POST['milk_quantity'];
    $milk_price = $_POST['milk_price'];
    $purchase_date = $_POST['purchase_date'];
    $farmer_username = $_POST['farmer_username'];

    $username = $_SESSION['username']; // Get logged-in employee username

    // Calculate total price (milk quantity * price per liter)
    $total_price = $milk_quantity * $milk_price;

    // SQL query to insert milk purchase record
    $sql = "INSERT INTO milk_purchases (milk_quantity, milk_price, purchase_date, username, farmer_username, total_price) 
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters (order should match placeholders in the SQL query)
       // Bind parameters (order should match placeholders in the SQL query)
        $stmt->bind_param("ddsssd", $milk_quantity, $milk_price, $purchase_date, $username, $farmer_username, $total_price);


        // Execute the statement
        if ($stmt->execute()) {
            // Get the last inserted ID for later use to update the status if needed
            $purchase_id = $stmt->insert_id;

            // Redirect after successful entry (without status)
            header("location: milk.php?status=pending&id=$purchase_id");
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

// Fetch and display the top 5 most recent milk purchases
$sql = "SELECT * FROM milk_purchases ORDER BY purchase_date DESC LIMIT 5"; // Limit to 5 most recent records
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Milk Purchase</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

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
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Record Milk Purchase</h1>

    <!-- Form to Record Milk Purchase -->
    <form method="POST" action="milk.php" class="max-w-lg mx-auto bg-white p-6 rounded shadow-lg">
        <div class="mb-4">
            <label for="milk_quantity" class="block text-lg font-medium text-gray-700">Milk Quantity (in liters)</label>
            <input type="number" id="milk_quantity" name="milk_quantity" class="w-full p-3 border rounded-lg mt-2" required>

        </div>
        <div class="mb-4">
            <label for="milk_price" class="block text-lg font-medium text-gray-700">Price per Liter (in your currency)</label>
            <input type="number" id="milk_price" name="milk_price" class="w-full p-3 border rounded-lg mt-2" required>
        </div>
        <div class="mb-4">
            <label for="purchase_date" class="block text-lg font-medium text-gray-700">Purchase Date</label>
            <input type="date" id="purchase_date" name="purchase_date" class="w-full p-3 border rounded-lg mt-2" required>
        </div>

        <div class="mb-4">
            <label for="farmer_username" class="block text-lg font-medium text-gray-700">Select Farmer</label>
            <select id="farmer_username" name="farmer_username" class="w-full p-3 border rounded-lg mt-2" required>
                <option value="">-- Select Farmer --</option>
                <?php echo $farmer_options; ?>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-500">Record Purchase</button>
        </div>
    </form>
</div>

<!-- Displaying Top 5 Recent Purchase Records -->
<div class="container mx-auto px-4 py-10 mt-8">
    <h2 class="text-2xl text-center font-semibold text-gray-800 mb-8">Top 5 Recent Milk Purchase Records</h2>
    <table class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-green-600 text-white">
                <th class="px-4 py-2">Farmer Username</th>
                <th class="px-4 py-2">Milk Quantity</th>
                <th class="px-4 py-2">Price per Liter</th>
                <th class="px-4 py-2">Total Price</th>
                <th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='px-4 py-2'>" . $row['farmer_username'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['milk_quantity'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['milk_price'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['total_price'] . "</td>";
                    echo "<td class='px-4 py-2'>" . $row['status'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='px-4 py-2 text-center'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close database connection
$mysqli->close();
?>
