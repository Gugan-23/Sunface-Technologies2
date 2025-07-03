<?php
// Database configuration
$host = "crossover.proxy.rlwy.net";
$port = 32488;
$username = "root";
$password = "OHtebhVoTYDpgZgrVwjtJJnBDnAUGScb";
$database = "railway";

// Create MySQLi connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
