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

// Specify the desired package names
$desiredPackages = ['Academic Writing', 'Article Writing'];

// Initialize an associative array to store the data
$dat = [];

// Pagination parameters
$entriesPerPage = isset($_GET['entriesPerPage']) ? intval($_GET['entriesPerPage']) : 10;
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($currentPage - 1) * $entriesPerPage;

// Search term
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare an array for the IN clause
$placeholders = implode(',', array_fill(0, count($desiredPackages), '?'));

// Fetch data from 'Packages' table for the specified packages and search term with pagination
$sql = "SELECT username, name AS package, date, id FROM Packages WHERE username LIKE ? AND name IN ($placeholders) ORDER BY date DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $searchParam = "%" . $search . "%";
    $params = array_merge([$searchParam], $desiredPackages, [$offset, $entriesPerPage]);

    // Construct the types string dynamically based on the number of parameters
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dat[] = $row;
        }
    } else {
        // If no packages are found, display a row with null values
        $dat[] = ['username' => 'null', 'package' => 'null', 'date' => 'null', 'id' => 'null'];
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
    $html .= "<td class='line'>" . $entry['username'] . "</td>";
    $html .= "<td class='line'>" . $entry['package'] . "</td>";
    $html .= "<td class='line'>" . $entry['date'] . "</td>";
    $html .= "<td class='line'>" . $entry['id'] . "</td>";
    $html .= "</tr>";
}

echo $html;
?>
