<?php
/**
 * Database Connection Configuration
 * FloodGuard Project - Centralized database connection
 */

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "floodguard";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed. Please contact administrator.");
}

// Set charset to prevent encoding issues
$conn->set_charset("utf8");

/**
 * Function to get database connection
 * @return mysqli
 */
function getDbConnection() {
    global $conn;
    return $conn;
}
?>
