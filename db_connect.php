<?php
$host = "localhost"; // Change if needed
$username = "root"; // Your database username
$password = "root"; // Your database password
$database = "booking_system"; // Your database name

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($host, $username, $password, $database); // Removed asterisks
    $conn->set_charset("utf8mb4");
} catch (Exception $e) { // Removed asterisks
    exit("Connection failed: " . $e->getMessage());
}
?>