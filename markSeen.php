<?php
// Assume a connection to your database
$db = new PDO("mysql:host=localhost;dbname=telmarka_db", "telmarka_db", "Benbrian@01");

// Mark the notification as seen
$notificationId = $_GET['id'];
$stmt = $db->prepare("UPDATE Notifications SET seen = 1 WHERE id = :id");
$stmt->bindParam(':id', $notificationId);
$stmt->execute();

// Return the updated notification
$stmt = $db->prepare("SELECT * FROM Notifications WHERE id = :id");
$stmt->bindParam(':id', $notificationId);
$stmt->execute();
$updatedNotification = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($updatedNotification);
?>
