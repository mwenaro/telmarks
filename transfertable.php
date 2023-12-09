<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$database = "telmarka_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination parameters
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$entriesPerPage = isset($_GET['entriesPerPage']) ? $_GET['entriesPerPage'] : 10;

// Calculate start and end positions for the SQL query
$start = ($currentPage - 1) * $entriesPerPage;
$end = $start + $entriesPerPage;

// Check if the session is active and get the current session username
if (!isset($_SESSION['user_name'])) {
    // Redirect or handle the case where the session is not active
    die("Error: Session not active or username not set.");
}
$currentUser = $_SESSION['user_name'];

// SQL query to fetch data from the Transfers table based on the current session username
$query = "SELECT transfer_id, sender_username, recipient_username, amount, transaction_date 
          FROM Transfers 
          WHERE sender_username = ? OR recipient_username = ? 
          ORDER BY transaction_date DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssii", $currentUser, $currentUser, $start, $entriesPerPage);
$stmt->execute();
$stmt->bind_result($transferId, $senderUsername, $recipientUsername, $amount, $transactionDate);

// Fetch data and store in an array
$data = [];
while ($stmt->fetch()) {
    $data[] = [
        'transferId' => $transferId,
        'senderUsername' => $senderUsername,
        'recipientUsername' => $recipientUsername,
        'amount' => $amount,
        'transactionDate' => $transactionDate,
    ];
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return data as JSON
echo json_encode($data);
?>
