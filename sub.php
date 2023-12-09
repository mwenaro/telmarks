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

// Initialize variables with null values
$id = 'null';
$username = 'null';
$phone_number = 'null';
$views = 'null';
$earnings = 'null';

// Fetch data from the database based on the search criteria
$sql = "SELECT id, username, phone_number, views, result as earnings FROM data WHERE username LIKE '%$search%' OR REPLACE(phone_number, '+', '') LIKE '%$searchNumber%' ORDER BY id DESC LIMIT $start, $entriesPerPage";
$result = $conn->query($sql);

// Check if results are available
if ($result !== false) {
    while ($row = $result->fetch_assoc()) {
        // If results are found, update variables with actual values
        $id = $row['id'];
        $username = $row['username'];
        $phone_number = $row['phone_number'];
        $views = $row['views'];
        $earnings = $row['earnings'];

        // Output the HTML for each row
        echo <<<HTML
        <tr>
            <td class='line'>$id</td>
            <td class='line'>$username</td>
            <td class='line'>$phone_number</td>
            <td class='line'>$views</td>
            <td class='line'>$earnings</td>
            <td class='line'><button class='delete-btn' data-id='$id'>Delete</button></td>
        </tr>
HTML;
    }
}

// If no results are found, display null for all columns
if ($result->num_rows === 0) {
    echo <<<HTML
    <tr>
        <td class='line'>$id</td>
        <td class='line'>$username</td>
        <td class='line'>$phone_number</td>
        <td class='line'>$views</td>
        <td class='line'>$earnings</td>
        <td class='line'><button class='delete-btn' data-id='$id'>Delete</button></td>
    </tr>
HTML;
}

$conn->close();
?>
