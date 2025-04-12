<?php

   // Include the database connection file
   include 'connect.php';

   // Set the 'user_id' cookie to expire immediately (removing the cookie)
   setcookie('user_id', '', time() - 1, '/');

   // Redirect the user to the home page after logging out
   header('location:../home.php');

?>
