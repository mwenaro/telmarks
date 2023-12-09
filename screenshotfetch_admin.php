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

// Extract the phone number without leading zeros or country code
$searchNumber = ltrim($search, '+0');

$start = ($page - 1) * $entriesPerPage;

// Fetch data from the database based on the search criteria
$sql = "SELECT username, phone_number, views, result as earnings FROM data WHERE username LIKE '%$search%' OR REPLACE(phone_number, '+', '') LIKE '%$searchNumber%' ORDER BY id DESC LIMIT $start, $entriesPerPage";
$result = $conn->query($sql);

$rows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
        echo "<tr>";
        echo "<td class='line'>" . $row['username'] . "</td>";
        echo "<td class='line'>" . $row['phone_number'] . "</td>";
        echo "<td class='line'>" . $row['views'] . "</td>";
        echo "<td class='line'>" . $row['earnings'] . "</td>";
        echo "</tr>";
    }
} else {
    // If no results are found, display null in all entries
    echo "<tr><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td></tr>";
}

// Calculate and return the total earnings
$totalSql = "SELECT SUM(result) as total FROM data";
$totalResult = $conn->query($totalSql);

if (!$totalResult) {
    die(); // Stop execution on error
}

$totalRow = $totalResult->fetch_assoc();

// Output the HTML with the dynamically calculated total earnings
echo <<<HTML
<div>Total Earnings: <span id="total-earnings">{$totalRow['total']}</span></div>
HTML;

$conn->close();
?>
