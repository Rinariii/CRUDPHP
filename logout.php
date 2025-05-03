<?php
session_start();

// Destroy the session to log out the user
session_unset();  // Removes all session variables
session_destroy();  // Destroys the session

// Redirect to login page after logout
header("Location: login.php");
exit;
?>
