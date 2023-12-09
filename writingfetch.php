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
    $desiredPackages = ['Academic Writing', 'Article Writing'];

    // Pagination parameters
    $entriesPerPage = 10;
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($currentPage - 1) * $entriesPerPage;

    // Initialize an HTML variable
    $html = '';

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
                    $html .= "<tr>";
                    $html .= "<td class='line'>" . $row['name'] . "</td>";
                    $html .= "<td class='line'>" . $row['date'] . "</td>";
                    $html .= "</tr>";
                }
            } else {
                // If the package is not available, add 'N/A' entries
                $html .= "<tr>";
                $html .= "<td class='line'>" . $package . "</td>";
                $html .= "<td class='line'>N/A</td>";
                $html .= "</tr>";
            }

            $stmt->close();
        } else {
            // Handle the case where the prepared statement fails
            $html .= "<tr><td colspan='2'>Error: Prepare statement failed</td></tr>";
        }
    }

    // Close the database connection
    $conn->close();

    // Output the HTML
    echo $html;
} else {
    // Handle the case where the session username is not set or empty
    echo "<tr><td colspan='2'>Error: Session username not set or empty</td></tr>";
}
?>
