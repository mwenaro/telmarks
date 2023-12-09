<?php
// Your database connection parameters
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$idToDelete = (int)$_GET['id'] ?? 0;

// Perform the delete operation using prepared statements
$deleteSql = "DELETE FROM data WHERE id = ?";
$stmt = $conn->prepare($deleteSql);

// Check if the prepare statement was successful
if ($stmt) {
    $stmt->bind_param("i", $idToDelete);
    $stmt->execute();

    if ($stmt->error) {
        // Log an error if execution fails
        error_log("Error executing delete statement: " . $stmt->error);
    }

    $stmt->close();
} else {
    // Log an error if the prepare statement fails
    error_log("Error preparing delete statement: " . $conn->error);
}

$conn->close();
?>
