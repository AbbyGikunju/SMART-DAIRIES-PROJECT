<?php
// Start session to ensure user is logged in and is an admin
session_start();

// Check if the user is logged in and if they are an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== "admin") {
    // If not logged in or not an admin, redirect to login page
    header("location: login.php");
    exit;
}

// Logout functionality (in case the user clicks the logout icon)
if (isset($_GET['logout'])) {
    // Destroy the session to log out the user
    session_destroy();
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

<!-- Admin Dashboard Container -->
<div class="container mx-auto p-6">
  <!-- Top Navigation with Logout Icon -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-4xl font-semibold">Admin Dashboard</h1>
        <p class="text-lg mt-2">Welcome, <?php echo $_SESSION["username"]; ?>! What would you like to do today?</p>
    </div>
    <!-- Logout Icon -->
    <a href="logout.php" class="text-red-500">
        <span class="material-icons text-3xl">logout</span>
    </a>
</div>

    <!-- Dashboard Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Manage Users Button -->
        <div class="bg-white shadow-lg p-6 rounded-lg text-center hover:shadow-2xl transition duration-300">
            <a href="users.php" class="block text-center text-blue-500">
                <span class="material-icons text-6xl">person_add</span>
                <h2 class="text-xl font-medium mt-2">Manage Users</h2>
            </a>
            <p class="text-sm mt-2">Add, edit, or remove users from the system.</p>
        </div>

        <!-- Generate Reports Button -->
        <div class="bg-white shadow-lg p-6 rounded-lg text-center hover:shadow-2xl transition duration-300">
            <a href="reports.php" class="block text-center text-green-500">
                <span class="material-icons text-6xl">assessment</span>
                <h2 class="text-xl font-medium mt-2">Generate Reports</h2>
            </a>
            <p class="text-sm mt-2">Create and download system reports.</p>
        </div>

        <!-- Alerts Button -->
       
    </div>
</div>

</body>
</html>
