<?php
// your_notification_endpoint.php

// Simulated database connection
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Simulated query to fetch notifications with notification_type = 'notification'
$sql = "SELECT message FROM Notifications WHERE notification_type = 'notification'";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch data and add to the notifications array
    $row = $result->fetch_assoc();

    // Close the database connection
    $conn->close();

    // Return JSON response with the message
    header('Content-Type: application/json');
    echo json_encode(array('message' => $row['message']));
} else {
    // No notifications found
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode(array('message' => ''));
}
?>
