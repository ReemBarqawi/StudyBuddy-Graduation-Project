<?php
/**
 * Database Connection Configuration
 * 
 * This file establishes a connection to the MySQL database used by the Study Buddy platform.
 * It uses MySQLi (MySQL Improved) extension for database operations.
 * 
 * SECURITY NOTE: In production, store credentials in environment variables or a secure config file
 * outside the web root directory. Never commit actual credentials to version control.
 */

// Database server hostname (for InfinityFree hosting)
$servername = "sql310.infinityfree.com";

// Database username (provided by hosting provider)
$username = "if0_38928194";

// Database password (IMPORTANT: Replace with your actual password from InfinityFree control panel)
// TODO: Move to environment variable for better security
$password = "JSr7V0GTuaGI5v";

// Database name (the specific database for Study Buddy)
$dbname = "if0_38928194_studybuddy_db";

/**
 * Create MySQLi connection object
 * This establishes a connection to the MySQL server using the credentials above
 */
$conn = new mysqli($servername, $username, $password, $dbname);

/**
 * Check if the connection was successful
 * If connection fails, stop script execution and display error message
 * 
 * NOTE: In production, log errors instead of displaying them to users
 * and show a generic "database unavailable" message
 */
if ($conn->connect_error) {
    // Terminate script and display connection error
    // TODO: Replace with proper error logging and user-friendly error page
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Optional: Set character encoding to UTF-8 to support international characters
 * Uncomment the line below if you need to ensure UTF-8 encoding
 */
// $conn->set_charset("utf8mb4");

?>
