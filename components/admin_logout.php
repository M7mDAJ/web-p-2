<?php

include 'connect.php';

// Start the session (if authentication uses sessions)
session_start();

// Check if the cookie exists before attempting to delete it
if (isset($_COOKIE['tutor_id'])) {
    // Delete the cookie by setting its expiration time in the past
    setcookie('tutor_id', '', time() - 3600, '/', '', false, true);
}

// Destroy the session if it is active
session_unset();
session_destroy();

// Redirect to the login page
header('Location: ../admin/login.php');
exit();

?>
