<?php
session_start();

// Check if user is logged in and has a valid session
if (!isset($_SESSION['username'])) {
    die("You are not logged in.");
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

// Get the logged-in user's username from session
$username = $_SESSION['username'];

// Query for milk purchase data for the logged-in user
$sql = "SELECT * FROM milk_purchases WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);  // Bind the session username
$stmt->execute();
$result = $stmt->get_result();

// Define the CSV file name
$filename = "milk_purchase_report_" . $username . "_" . date('Y-m-d') . ".csv";

// Open file in write mode
$file = fopen('php://output', 'w');

// Set headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Write the header row
fputcsv($file, ['ID', 'Username', 'Milk Quantity', 'Milk Price', 'Total Price', 'Purchase Date', 'Purchase Time']);

// Write data to CSV
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($file, [
            $row['id'], 
            $row['username'],  // Add the username to the CSV
            $row['milk_quantity'], 
            $row['milk_price'], 
            $row['total_price'], 
            $row['purchase_date'],
            $row['purchase_time']  // Add purchase time (morning/evening) if needed
        ]);
    }
} else {
    fputcsv($file, ['No data available']);
}

fclose($file);  // Close the file

$stmt->close();
$conn->close();
?>
