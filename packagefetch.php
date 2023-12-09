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

session_start();

// Check if the session variable is set and not empty
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $sessionUsername = $_SESSION['user_name'];

    // Specify the desired package names
    $desiredPackages = ['Star', 'Platinum', 'Diamond'];

    // Initialize an associative array to store the data
    $dat = [];

    // Pagination parameters
    $entriesPerPage = 10;
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($currentPage - 1) * $entriesPerPage;

    // Fetch data from 'Packages' table for the specified packages and logged-in user with pagination
    foreach ($desiredPackages as $package) {
        $sql = "SELECT name, price, date FROM Packages WHERE username = ? AND name = ? LIMIT ?, ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssii", $sessionUsername, $package, $offset, $entriesPerPage);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $dat[] = $row;
                }
            } else {
                // If the package is not available, add 'N/A' entries
                $dat[] = ['name' => $package, 'price' => 'N/A', 'date' => 'N/A'];
            }

            $stmt->close();
        } else {
            // Handle the case where the prepared statement fails
            echo json_encode(['error' => 'Prepare statement failed']);
            $conn->close();
            exit();
        }
    }

    // Close the database connection
    $conn->close();

    // Generate HTML elements with data
    $html = '';
    foreach ($dat as $entry) {
        $html .= "<tr>";
        $html .= "<td class='line'>" . $entry['name'] . "</td>";
        $html .= "<td class='line'>" . $entry['date'] . "</td>";
        $html .= "</tr>";
    }

    echo $html;
} else {
    // Handle the case where the session username is not set or empty
    echo json_encode(['error' => 'Session username not set or empty']);
    $conn->close();
    exit();
}
?>
