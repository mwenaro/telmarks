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

$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';
$entriesPerPage = $_GET['entries'] ?? 10;

$start = ($page - 1) * $entriesPerPage;

// Fetch data from the database based on the search criteria
$sql = "SELECT request_id, points_to_withdraw, request_date, username, status FROM WithdrawalRequests WHERE username LIKE '%$search%' AND (whatsapp IS NULL OR whatsapp = '') ORDER BY request_date DESC LIMIT $start, $entriesPerPage";
$result = $conn->query($sql);

$rows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
        echo "<tr>";
        echo "<td class='line'>" . $row['request_id'] . "</td>";
        echo "<td class='line'>" . $row['points_to_withdraw'] . "</td>";
        echo "<td class='line'>" . $row['request_date'] . "</td>";
        echo "<td class='line'>" . $row['username'] . "</td>";
        // Display an editable status input field
        echo "<td class='line' contenteditable='true' data-id='" . $row['request_id'] . "' id='status_" . $row['request_id'] . "'>" . $row['status'] . "</td>";
        // Add a button for updating the status
        echo "<td class='line'><button class='update-btn' data-id='" . $row['request_id'] . "'>Update</button></td>";
        echo "</tr>";
    }
} else {
    // If no results are found, display null in all entries
    echo "<tr><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td></tr>";
}

// Calculate and return the total withdrawables only for those with 'whatsapp' as null
$totalSql = "SELECT SUM(points_to_withdraw) as total FROM WithdrawalRequests WHERE (whatsapp IS NULL OR whatsapp = '')";
$totalResult = $conn->query($totalSql);

if (!$totalResult) {
    die(); // Stop execution on error
}

$totalRow = $totalResult->fetch_assoc();

// Output the HTML with the dynamically calculated total withdrawables
echo <<<HTML
<div>Total Withdrawables: <span id="total-withdrawables">{$totalRow['total']}</span></div>
HTML;

$conn->close();
?>
