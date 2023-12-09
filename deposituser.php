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

// Check if the session is active and get the current session username
if (!isset($_SESSION['user_name'])) {
    // Redirect or handle the case where the session is not active
    die("Error: Session not active or username not set.");
}
$currentUser = $_SESSION['user_name'];

// SQL query to fetch data from the WithdrawalRequests table based on the current session username
$query = "SELECT deposit_id , amount, transaction_date 
          FROM Deposits 
          WHERE username = ? 
          ORDER BY deposit_id DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $currentUser, $start, $entriesPerPage);
$stmt->execute();
$stmt->bind_result($deposit_id, $amount, $transaction_date);

// Fetch data and store in an array
$data = [];
while ($stmt->fetch()) {
    $data[] = [
        'deposit_id' => $deposit_id,
        'amount' => $amount,
        'transaction_date' => $transaction_date,
    ];
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return data as JSON
echo json_encode($data);
?>
