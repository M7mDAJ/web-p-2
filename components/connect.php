<?php

// Database connection details
$db_name = 'mysql:host=localhost;dbname=course_db';  // Database connection string
$user_name = 'root';  // Database username
$user_password = '';  // Database password (empty for default XAMPP setup)

// Creating a new PDO connection to the database
$conn = new PDO($db_name, $user_name, $user_password);

// Function to generate a unique ID
function unique_id() {
    // Define a string containing all characters and digits that can be part of the ID
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $rand = array();  // Array to hold the random characters
    $length = strlen($str) - 1;  // Get the last index of the string

    // Loop to generate 20 random characters
    for ($i = 0; $i < 20; $i++) {
        // Generate a random number between 0 and the length of the string (excluding last character)
        $n = mt_rand(0, $length);
        // Add the randomly selected character to the array
        $rand[] = $str[$n];
    }

    // Return the unique ID as a string
    return implode($rand);
}

?>
