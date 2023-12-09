<?php
// Database connection parameters
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start a session
session_start();

// Check if the session variable 'user_name' is set and not empty
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $sessionUsername = $_SESSION['user_name'];

    // Initialize an associative array to store the data
    $dat = [];

    // Create an associative array to track added usernames
    $addedUsernames = [];

    // Fetch all users invited by the current user who bought either 'Diamond' or 'Diamond Package'
    $invitedUsersSql = "SELECT u.username, u.phone_number, u.registration_date 
                        FROM Users u
                        INNER JOIN Packages p ON u.username = p.username
                        WHERE u.invited_by = ? AND (p.name = 'Diamond' OR p.name = 'Diamond Package')";
    $invitedUsersStmt = $conn->prepare($invitedUsersSql);

    if ($invitedUsersStmt) {
        // Bind the session username as a parameter
        $invitedUsersStmt->bind_param("s", $sessionUsername);

        // Execute the prepared statement
        $invitedUsersStmt->execute();

        // Get the result set
        $invitedUsersResult = $invitedUsersStmt->get_result();

        // Loop through the result set and store data in the $dat array
        while ($invitedUserRow = $invitedUsersResult->fetch_assoc()) {
            $username = $invitedUserRow['username'];

            // Check if the username has already been added
            if (!in_array($username, $addedUsernames)) {
                $phonenumber = $invitedUserRow['phone_number'];
                $registration_date = $invitedUserRow['registration_date'];

                // Store the data in the $dat array
                $dat[] = [
                    'username' => $username,
                    'phone_number' => $phonenumber,
                    'registration_date' => $registration_date,
                    'active' => 'Active' // Since they bought either 'Diamond' or 'Diamond Package'
                ];

                // Add the username to the addedUsernames array
                $addedUsernames[] = $username;
            }
        }

        // Close the prepared statement
        $invitedUsersStmt->close();
    } else {
        // Handle the case where the prepared statement for invited users fails
        $error = 'Prepare statement for invited users failed';
        error_log($error);
        echo json_encode(['error' => $error]);
        $conn->close();
        exit();
    }

    // Close the database connection
    $conn->close();

    // Output the stored user data in JSON format
    echo json_encode($dat);
} else {
    // Handle the case where the session username is not set or empty
    $error = 'Session username not set or empty';
    error_log($error);
    echo json_encode(['error' => $error]);
    exit();
}
?>
