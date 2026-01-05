<?php
/**
 * Database Connection Configuration
 * Library Management System
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'librarysystem');

/**
 * Get database connection
 * @return mysqli Database connection object
 */
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        // Create connection
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to utf8mb4
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

/**
 * Close database connection
 */
function closeDBConnection() {
    global $conn;
    if (isset($conn) && $conn !== null) {
        $conn->close();
    }
}

// Auto-connect on include
$conn = getDBConnection();
?>

