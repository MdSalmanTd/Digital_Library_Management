<?php
// Read the database settings from the .env file
$env = parse_ini_file(__DIR__ . "/.env");

if ($env === false) {
  die("The .env file is missing. Copy .env.example to .env and fill in your database settings.");
}

$servername = $env["DB_HOST"];
$username = $env["DB_USER"];
$password = $env["DB_PASS"];
$dbname = $env["DB_NAME"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set("Asia/Dhaka");
?>
