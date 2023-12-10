<?php
// Database connection details
$servername = "192.185.160.175";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if(conn){
    echo "dabase cpnnedted";

}else{
    echo " Connection failure ";
}