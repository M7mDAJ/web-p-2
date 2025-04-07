<?php

// Database connection settings
$db_host = 'localhost';
$db_name = 'course_db';
$db_user = 'root';
$db_password = '';

try {
    // Establish a secure connection using PDO with error handling
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch data as an associative array
        PDO::ATTR_EMULATE_PREPARES => false // Disable emulation for prepared statements
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage()); // Stop script and show error if connection fails
}

/**
 * Generate a secure unique identifier
 *
 * @return string A unique ID of 20 characters
 */
function unique_id() {
    return bin2hex(random_bytes(10)); // Generates a 20-character hexadecimal string
}

?>
