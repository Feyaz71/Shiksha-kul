<?php
$host = "127.0.0.1";  // Use IP instead of "localhost"
$user = "root";
$pass = "";  // Empty password
$dbname = "classroom";
$port = 1055; // Use your custom port

$start_time = microtime(true);  // Start time before connection

$conn = new mysqli($host, $user, $pass, $dbname, $port);

$end_time = microtime(true);  // End time after connection
$connection_time = $end_time - $start_time;

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    // echo "✅ Connected successfully in " . round($connection_time, 4) . " seconds";
}
?>
