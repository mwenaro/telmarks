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

// Fetch data from the "Transfers" table
// Fetch data from the "Transfers" table
$sql = "SELECT transfer_id, sender_username, recipient_username, amount, transaction_date 
        FROM Transfers 
        WHERE sender_username LIKE '%$search%' OR recipient_username LIKE '%$search%'
        ORDER BY transaction_date DESC 
        LIMIT $start, $entriesPerPage";


$result = $conn->query($sql);

$rows = [];
$totalAmount = 0; // Variable to store the total amount transferred

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
        echo "<tr>";
        echo "<td class='line'>" . $row['transfer_id'] . "</td>";
        echo "<td class='line'>" . $row['sender_username'] . "</td>";
        echo "<td class='line'>" . $row['recipient_username'] . "</td>";
        echo "<td class='line'>" . $row['amount'] . "</td>";
        echo "<td class='line'>" . $row['transaction_date'] . "</td>";
        echo "</tr>";

        // Accumulate the total amount
        $totalAmount += $row['amount'];
    }
} else {
    // If no results are found, display null in all entries
    echo "<tr><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td><td class='line'>null</td></tr>";
}

// Display the total amount at the end of the table
echo "<tr>";
echo "<td class='line'></td>";
echo "<td class='line'></td>";
echo "<td class='line'></td>";
echo "<td class='line'>Total Amount: </td>";
echo "<td class='line'>" . $totalAmount . "</td>";
echo "</tr>";

$conn->close();
?>