<?php
// Start session and destroy it for logout
session_start();
session_destroy();

// Redirect to login page
header("location: ../index.php");
exit;
?>
