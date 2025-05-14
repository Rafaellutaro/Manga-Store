<?php
$dbhost = '192.168.1.79';  
$dbuser = 'dolibarruser1';     
$dbpass = 'dolibarr';     
$dbname = 'dolibarr';     

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
