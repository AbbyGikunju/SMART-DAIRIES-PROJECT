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
$username = $password = "";
$username_err = $password_err = "";

// Process form when it's submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check for any errors before querying the database
    if (empty($username_err) && empty($password_err)) {
        
        // Prepare SQL query to fetch user details
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("s", $param_username);
            $param_username = $username;

            // Execute the query
            $stmt->execute();
            $stmt->store_result();

            // Check if the username exists
            if ($stmt->num_rows == 1) {
                // Bind result variables
                $stmt->bind_result($id, $username, $hashed_password, $role);

                if ($stmt->fetch()) {
                    // Verify password
                    if (password_verify($password, $hashed_password)) {
                        // Start session and store user data
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["role"] = $role;

                        // Redirect based on user role
                        if ($role == "admin") {
                            header("location: admin/dashboard.php");
                        } elseif ($role == "employee") {
                            header("location: employees/dashboard.php");
                        } elseif ($role == "farmer") {
                            header("location: farmers/dashboard.php");
                        } else {
                            echo "Role not recognized!";
                        }
                    } else {
                        // Incorrect password
                        $password_err = "The password you entered was not correct.";
                    }
                }
            } else {
                // Username does not exist
                $username_err = "No account found with that username.";
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
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<!-- Login Form -->
<div class="max-w-lg mx-auto bg-white p-8 mt-10 shadow-xl rounded">
    <h2 class="text-2xl font-semibold mb-6 text-center">Login</h2>
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

        <!-- Submit Button -->
        <div class="mb-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Login</button>
        </div>

        <!-- Register Link -->
        <div class="text-center text-sm">
            Don't have an account? <a href="register.php" class="text-blue-600">Register here</a>.
        </div>

    </form>
</div>

</body>
</html>
