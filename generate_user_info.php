<?php
// Your database connection parameters
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information from the database (assuming a table named 'Users' with columns 'username' and 'password')
$query = "SELECT username, email, phone_number FROM Users";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch the data into an associative array
$userInfo = array();
while ($row = $result->fetch_assoc()) {
    $userInfo[] = "Username: " . $row['username'] . ", Email: " . $row['email'] . ", Phone Number: " . $row['phone_number'];
}

// Close the database connection
$conn->close();

// Send the user information to the specified email address
$to = "info@telmarkagencies.com";
$subject = "User Information";
$message = implode("\n", $userInfo);
$headers = "From: admin@telmarkagencies.com"; // Replace with your email address

// Use the mail() function to send the email (Ensure your server is configured for mail sending)
mail($to, $subject, $message, $headers);

// Return a response (optional)
echo "User information sent to info@telmarkagencies.com successfully!";
?>
