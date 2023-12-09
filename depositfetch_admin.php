<?php
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Specify the desired table and columns
$tableName = "Deposits";
$columns = ["deposit_id", "username", "transaction_date", "amount"];

// Initialize an associative array to store the data
$dat = [];

// Pagination parameters
$entriesPerPage = isset($_GET['entriesPerPage']) ? intval($_GET['entriesPerPage']) : 10;
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($currentPage - 1) * $entriesPerPage;

// Search term
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch data from 'Deposits' table with pagination
$sql = "SELECT " . implode(", ", $columns) . " FROM $tableName WHERE username LIKE ? ORDER BY transaction_date DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $searchParam = "%" . $search . "%";
    $stmt->bind_param("sii", $searchParam, $offset, $entriesPerPage);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dat[] = $row;
        }
    } else {
        // If no deposits are found, display a row with null values
        $dat[] = array_fill_keys($columns, 'null');
    }

    $stmt->close();
} else {
    // Handle the case where the prepared statement fails
    echo json_encode(['error' => 'Prepare statement failed']);
    $conn->close();
    exit();
}

// Close the database connection
$conn->close();

// Generate HTML elements with data
$html = '';
foreach ($dat as $entry) {
    $html .= "<tr>";
    foreach ($entry as $value) {
        $html .= "<td class='line'>" . $value . "</td>";
    }
    $html .= "</tr>";
}

echo $html;
?>
