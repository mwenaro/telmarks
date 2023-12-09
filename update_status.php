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

$requestId = $_GET['request_id'] ?? '';
$newStatus = $_GET['new_status'] ?? '';

// Update the status in the database
$updateSql = "UPDATE WithdrawalRequests SET status = '$newStatus' WHERE request_id = '$requestId'";
$updateResult = $conn->query($updateSql);

if (!$updateResult) {
    die("Error updating status: " . $conn->error);
}

echo "Status updated successfully";

$conn->close();
?>
