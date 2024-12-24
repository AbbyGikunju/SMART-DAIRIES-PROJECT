<?php
session_start();

// Check if user is logged in and if they are an admin
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

// Handle the delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Ensure the admin is not trying to delete themselves
    if ($_SESSION['username'] == $delete_id) {
        echo "<script>alert('You cannot delete your own account!'); window.location.href='users.php';</script>";
        exit;
    }

    // Delete the user from the database
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param('i', $delete_id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!'); window.location.href='users.php';</script>";
    }
}

// Fetch all users from the database
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Users</title>
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
    <h1 class="text-4xl text-center font-semibold text-gray-800 mb-8">Manage Users</h1>

    <!-- Displaying Users Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Username</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Created At</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display the users
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='px-4 py-2'>" . $row['id'] . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['username'] . "</td>";
                        echo "<td class='px-4 py-2'>" . ucfirst($row['role']) . "</td>";
                        echo "<td class='px-4 py-2'>" . $row['created_at'] . "</td>";
                        
                        // Display delete button, but prevent deleting the admin
                        echo "<td class='px-4 py-2'>";

                        if ($row['role'] != 'admin') {
                            echo "<a href='?delete_id=" . $row['id'] . "' class='bg-red-600 text-white px-4 py-2 rounded hover:bg-red-500'>Delete</a>";
                        } else {
                            echo "Admin cannot be deleted";
                        }

                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='px-4 py-2 text-center'>No users found.</td></tr>";
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
$conn->close();
?>
