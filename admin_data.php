<?php
// Your database connection parameters
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the delete button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $userId = $_POST['user_id'];

    try {
        // Disable foreign key checks
        $conn->query('SET foreign_key_checks = 0');

        // Perform the cascading delete operation
        // Delete from Transfers table
        $deleteTransfersQuery = "DELETE FROM Transfers WHERE sender_username IN (SELECT username FROM Users WHERE id = $userId) OR recipient_username IN (SELECT username FROM Users WHERE id = $userId)";
        if ($conn->query($deleteTransfersQuery) === TRUE) {
            // Delete from ReferralBonus table
            $deleteReferralBonusQuery = "DELETE FROM ReferralBonus WHERE username IN (SELECT username FROM Users WHERE id = $userId)";
            if ($conn->query($deleteReferralBonusQuery) === TRUE) {
                // Delete from Packages table
                $deletePackagesQuery = "DELETE FROM Packages WHERE username IN (SELECT username FROM Users WHERE id = $userId)";
                if ($conn->query($deletePackagesQuery) === TRUE) {
                    // Now you can safely delete the user
                    $deleteUserQuery = "DELETE FROM Users WHERE id = $userId";
                    if ($conn->query($deleteUserQuery) === TRUE) {
                        echo "User deleted successfully";
                    } else {
                        echo "Error deleting user: " . $conn->error;
                    }
                } else {
                    echo "Error deleting packages: " . $conn->error;
                }
            } else {
                echo "Error deleting referral bonuses: " . $conn->error;
            }
        } else {
            echo "Error deleting transfers: " . $conn->error;
        }
    } finally {
        // Enable foreign key checks
        $conn->query('SET foreign_key_checks = 1');
    }
}

// Check if the edit button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $userId = $_POST['user_id'];
    $jsonData = json_decode($_POST['data'], true);

    // Construct the SQL query based on the updated data
    $updateQuery = "UPDATE Users SET ";
    foreach ($jsonData as $field => $value) {
        $field = $conn->real_escape_string($field);
        $value = $conn->real_escape_string($value);
        $updateQuery .= "$field = '$value', ";
    }
    $updateQuery = rtrim($updateQuery, ', '); // Remove the trailing comma and space
    $updateQuery .= " WHERE id = $userId";

    // Perform the update operation
    if ($conn->query($updateQuery) === TRUE) {
        echo "User updated successfully";
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

// Fetch data from the Users table
$search = $_GET['search'] ?? '';
$query = "SELECT id, name, email, phone_number, username, registration_date, invited_by FROM Users";

// Apply search filter if provided
if ($search !== '') {
    $query .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone_number LIKE '%$search%' OR username LIKE '%$search%' OR registration_date LIKE '%$search%' OR invited_by LIKE '%$search%'";
}

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch the data into an associative array
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Close the database connection
$conn->close();

// Return the data in JSON format
echo json_encode($data);
?>
