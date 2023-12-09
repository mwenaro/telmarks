<?php
$host = "localhost"; // Database host
$username = "telmarka_db"; // Database username
$password = "Benbrian@01"; // Database password
$database = "telmarka_db"; // Database name

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
