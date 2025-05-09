<?php
// Database connection parameters
$db_host = '127.0.0.1';
$db_user = 'ira';
$db_pass = 'P@ssw0rd';
$db_name = 'ddb250465';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8");
?>

