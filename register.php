<?php
// Start session to handle user authentication later
session_start();

// Database connection settings
$servername = "localhost";  // Usually localhost for Laragon
$username_db = "root";      // Default username for Laragon
$password_db = "";          // Default password for Laragon (blank)
$dbname = "biodairy";       // The database name

// Create a connection to the database
$mysqli = new mysqli($servername, $username_db, $password_db, $dbname);

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize variables and error messages
$username = $password = $role = "";
$username_err = $password_err = $role_err = "";

// Process form when it's submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Check if username is already taken
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["username"]);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $username_err = "This username is already taken.";
            } else {
                $username = trim($_POST["username"]);
            }
            $stmt->close();
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 4) { // Changed to 4 characters
        $password_err = "Password must have at least 4 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate role selection
    if (empty($_POST["role"])) {
        $role_err = "Please select a role.";
    } else {
        $role = $_POST["role"];
    }

    // Check for any errors before inserting into the database
    if (empty($username_err) && empty($password_err) && empty($role_err)) {

        // Prepare the insert statement
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Hash the password before storing it
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt->bind_param("sss", $param_username, $param_password, $param_role);

            // Set parameters
            $param_username = $username;
            $param_password = $password_hash;
            $param_role = $role;

            // Execute statement
            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("location: login.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    // Close database connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<!-- Registration Form -->
<div class="max-w-lg mx-auto bg-white p-8 mt-10 shadow-xl rounded">
    <h2 class="text-2xl font-semibold mb-6 text-center">Register</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <!-- Username -->
        <div class="mb-4">
            <label for="username" class="block text-sm font-medium">Username</label>
            <input type="text" name="username" id="username" class="border w-full p-2 rounded" value="<?php echo $username; ?>">
            <span class="text-red-500 text-sm"><?php echo $username_err; ?></span>
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium">Password</label>
            <input type="password" name="password" id="password" class="border w-full p-2 rounded">
            <span class="text-red-500 text-sm"><?php echo $password_err; ?></span>
        </div>

        <!-- Role Selection -->
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium">Role</label>
            <select name="role" id="role" class="border w-full p-2 rounded">
                <option value="farmer" <?php echo ($role == 'farmer') ? 'selected' : ''; ?>>Farmer</option>
                <option value="employee" <?php echo ($role == 'employee') ? 'selected' : ''; ?>>Employee</option>
                <option value="admin" <?php echo ($role == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
            <span class="text-red-500 text-sm"><?php echo $role_err; ?></span>
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Register</button>
        </div>
    </form>
</div>

</body>
</html>
