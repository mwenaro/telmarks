<?php
// Connect to the database
$db = new PDO("mysql:host=localhost;dbname=telmarka_db", "telmarka_db", "Benbrian@01");

// Fetch the latest filename from the uploaded_files table
$stmt = $db->prepare("SELECT filename FROM uploaded_files ORDER BY id DESC LIMIT 1");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Return the result as JSON
echo json_encode($result);
?>
