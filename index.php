<?php
// If needed, you can add PHP code here for session management, etc.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Purchase System</title>
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
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="bg-green-600 p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <a href="#" class="text-white text-2xl font-bold">Smart Dairies</a>
        <div>
            <a href="login.php" class="text-white px-4 py-2 rounded hover:bg-green-500">Log In</a>
            <a href="register.php" class="text-white px-4 py-2 rounded hover:bg-green-500">Register</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto px-4 py-10">
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Welcome to SmartDairies Management System</h1>
    
    <!-- Authentication Buttons (Optional for users not logged in) -->
    <div class="text-center mb-8">
        <?php
        // If the user is logged in, display a personalized greeting and a link to the dashboard
        if (isset($_SESSION['user_id'])) {
            echo "<p class='text-lg text-gray-700'>Hello, " . $_SESSION['username'] . "! You are logged in.</p>";
            echo "<a href='dashboard.php' class='bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-500'>Go to Dashboard</a>";  
        } else {
            // This block is removed as per your request
        }
        ?>
    </div>

    <!-- Cards Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        <!-- Milk Purchase Tracking Card -->
        <div class="card w-full p-6 rounded-xl">
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-blue-500">local_shipping</span> <!-- Blue Icon -->
            </div>
            <h5 class="text-xl font-semibold mb-4">Milk Purchase Tracking</h5>
            <p class="text-gray-600">Track milk purchases from farmers, including details such as quantity, quality, and price. Generate reports for analysis and record-keeping.</p>
        </div>

        <!-- Sales Management Card -->
        <div class="card w-full p-6 rounded-xl">
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-green-500">storefront</span> <!-- Green Icon -->
            </div>
            <h5 class="text-xl font-semibold mb-4">Sales Management</h5>
            <p class="text-gray-600">Record internal milk sales to customers with different payment methods. Manage available products and inventory efficiently.</p>
        </div>

        <!-- Employee Management Card -->
        <div class="card w-full p-6 rounded-xl">
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-orange-500">group</span> <!-- Orange Icon -->
            </div>
            <h5 class="text-xl font-semibold mb-4">Employee Management</h5>
            <p class="text-gray-600">Enable employees to access and manage data related to milk transactions. Generate downloadable PDF records for farmers.</p>
        </div>

        <!-- Data Analysis Card -->
        <div class="card w-full p-6 rounded-xl">
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-purple-500">insights</span> <!-- Purple Icon -->
            </div>
            <h5 class="text-xl font-semibold mb-4">Data Analysis</h5>
            <p class="text-gray-600">Monitor sales trends, inventory levels, and farmer performance. Generate reports to assist in decision-making for better business management.</p>
        </div>

        <!-- Notifications Card -->
        <div class="card w-full p-6 rounded-xl">
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-red-500">notifications</span> <!-- Red Icon -->
            </div>
            <h5 class="text-xl font-semibold mb-4">Notifications</h5>
            <p class="text-gray-600">Automated notifications to farmers regarding collection schedules and payment status. Alerts to employees for low inventory levels.</p>
        </div>

        <!-- System Overview Card -->
        <div class="card w-full p-6 rounded-xl">
            <div class="flex justify-center mb-4">
                <span class="material-icons text-6xl text-teal-500">build</span> <!-- Teal Icon -->
            </div>
            <h5 class="text-xl font-semibold mb-4">System Overview</h5>
            <p class="text-gray-600">An integrated system that enhances the management of dairy operations by connecting farmers, employees, and the business. Streamline milk sourcing, inventory, and sales management in one platform.</p>
        </div>
    </div>
</div>

</body>
</html>
