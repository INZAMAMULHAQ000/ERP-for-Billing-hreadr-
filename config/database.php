<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'u443790490_Billing');
define('DB_PASSWORD', 'AndroCoder2020');
define('DB_NAME', 'u443790490_Billing');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?> 