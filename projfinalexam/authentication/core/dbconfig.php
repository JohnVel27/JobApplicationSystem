<?php  
session_start();

// Database configuration
$host = "localhost"; // Try '127.0.0.1' if 'localhost' doesn't work
$user = "root"; // Ensure this is the correct username for your MySQL setup
$password = ""; // Ensure this is the correct password for your MySQL setup (empty by default in XAMPP)
$dbname = "projfinalexam_database"; // Your database name

// DSN (Data Source Name) - using '127.0.0.1' instead of 'localhost' (or specify port if needed)
$dsn = "mysql:host=127.0.0.1;dbname={$dbname};charset=utf8"; // Added charset=utf8 for better character encoding handling

// Try to establish the database connection
try {
    // Create a PDO instance
    $pdo = new PDO($dsn, $user, $password);
    
    // Set the PDO error mode to exception for debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set the time zone
    $pdo->exec("SET time_zone = '+08:00';");
    
    // If the connection is successful, you can proceed with your queries
} catch (PDOException $e) {
    // If there's an error, display it
    echo "Connection failed: " . $e->getMessage();
    exit; // Stop execution if unable to connect
}
?>
