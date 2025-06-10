<?php
$dbhost = 'localhost';  
$dbuser = 'SuperMan';     
$dbpass = 'Dolibarr!@399';     
$dbname = 'dolibarr';     

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
