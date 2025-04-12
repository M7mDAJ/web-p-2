<?php

// Including the connection file to interact with the database (assuming connection to the database is inside 'connect.php')
include 'connect.php';

// Deleting the 'tutor_id' cookie by setting its expiration time to one second in the past
setcookie('tutor_id', '', time() - 1, '/');

// Redirecting the user to the login page after the cookie is deleted
header('location:../admin/login.php');

?>
