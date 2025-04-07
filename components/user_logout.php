<?php

// Include database connection
include 'connect.php';

// Delete user_id cookie
setcookie('user_id', '', time() - 3600, '/', '', false, true);

// Redirect to home page
header('Location: ../home.php');
exit;

?>
