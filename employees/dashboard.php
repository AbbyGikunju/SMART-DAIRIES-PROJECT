<?php
// Start session to ensure user is logged in and is an employee
session_start();

// Check if the user is logged in and if they are an employee
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "employee") {
    // If not logged in or not an employee, redirect to login page
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

<!-- Navbar with Logout -->
<nav class="bg-green-600 p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <a href="#" class="text-white text-2xl font-bold">Smart Dairies</a>
        <div class="flex items-center space-x-4">
            <span class="text-white text-lg"><?php echo $_SESSION["username"]; ?></span> <!-- Display username -->
            <a href="logout.php" class="text-white">
                <span class="material-icons text-3xl">logout</span> <!-- Logout Icon -->
            </a>
        </div>
    </div>
</nav>

<!-- Employee Dashboard Container -->
<div class="container mx-auto p-6">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-semibold">Employee Dashboard</h1>
        <p class="text-lg mt-2">Welcome, <?php echo $_SESSION["username"]; ?>! What would you like to do today?</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Record Milk Purchase Button -->
        <div class="bg-white shadow-lg p-6 rounded-lg text-center hover:shadow-2xl transition duration-300">
            <a href="milk.php" class="block text-center text-blue-500">
                <span class="material-icons text-6xl">local_drink</span>
                <h2 class="text-xl font-medium mt-2">Record Milk Purchase</h2>
            </a>
            <p class="text-sm mt-2">Record milk purchases from farmers and manage details.</p>
        </div>

        <!-- Sales Management Button -->
        <div class="bg-white shadow-lg p-6 rounded-lg text-center hover:shadow-2xl transition duration-300">
            <a href="sales.php" class="block text-center text-green-500">
                <span class="material-icons text-6xl">storefront</span>
                <h2 class="text-xl font-medium mt-2">Sales Management</h2>
            </a>
            <p class="text-sm mt-2">Manage milk sales, inventory, and customer transactions.</p>
        </div>

        <!-- View Milk Amount Button -->
        <div class="bg-white shadow-lg p-6 rounded-lg text-center hover:shadow-2xl transition duration-300">
            <a href="view.php" class="block text-center text-orange-500">
                <span class="material-icons text-6xl">check_box</span>
                <h2 class="text-xl font-medium mt-2">View Milk Amount</h2>
            </a>
            <p class="text-sm mt-2">View current milk inventory levels and stock amounts.</p>
        </div>

        <!-- Generate Reports Button -->
        <div class="bg-white shadow-lg p-6 rounded-lg text-center hover:shadow-2xl transition duration-300">
            <a href="reports.php" class="block text-center text-purple-500">
                <span class="material-icons text-6xl">assessment</span>
                <h2 class="text-xl font-medium mt-2">Generate Reports</h2>
            </a>
            <p class="text-sm mt-2">Generate reports on milk purchases, sales, and inventory.</p>
        </div>

        <!-- Logout Button -->
        <div class="bg-white shadow-lg p-6 rounded-lg text-center hover:shadow-2xl transition duration-300">
            <a href="logout.php" class="block text-center text-red-500">
                <span class="material-icons text-6xl">logout</span>
                <h2 class="text-xl font-medium mt-2">Logout</h2>
            </a>
            <p class="text-sm mt-2">Log out of the system and return to the login page.</p>
        </div>
    </div>
</div>

</body>
</html>
