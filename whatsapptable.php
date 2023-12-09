<?php
// Start the session at the beginning of the script
session_start();

// Database connection details for mysqli
$dbHost = "localhost";
$dbUser = "telmarka_db";
$dbPassword = "Benbrian@01";
$dbName = "telmarka_db";

// Create a database connection using mysqli
$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    $loggedInUser = $_SESSION['user_name'];

    // Pagination
    $entriesPerPage = isset($_GET['entries']) ? intval($_GET['entries']) : 10;
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($currentPage - 1) * $entriesPerPage;

    // Fetch entries with the same session name and WhatsApp entry
    $checkWhatsAppQuery = "SELECT points_to_withdraw, request_date, status FROM WithdrawalRequests WHERE username = ? AND whatsapp IS NOT NULL LIMIT ?, ?";
    $checkWhatsAppStmt = $conn->prepare($checkWhatsAppQuery);
    $checkWhatsAppStmt->bind_param('sii', $loggedInUser, $offset, $entriesPerPage);
    $checkWhatsAppStmt->execute();
    $checkWhatsAppResult = $checkWhatsAppStmt->get_result();

    // Prepare an array to hold the data
    $data = [];

    // Fetch data into the array
    while ($row = $checkWhatsAppResult->fetch_assoc()) {
        $data[] = $row;
    }

    // Convert the array to JSON and echo it
    echo json_encode($data);
}

// Close the mysqli connection
$conn->close();
?>
