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

// Check if the session variable is set and not empty
session_start(); // Keep only one session_start() at the necessary place

if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $sessionUsername = $_SESSION['user_name'];

    // Initialize an associative array to store the data
    $dat = [];

    // Fetch all users invited by the current user
    $invitedUsersSql = "SELECT username, phone_number, registration_date FROM Users WHERE invited_by = ?";
    $invitedUsersStmt = $conn->prepare($invitedUsersSql);

    if ($invitedUsersStmt) {
        $invitedUsersStmt->bind_param("s", $sessionUsername);
        $invitedUsersStmt->execute();
        $invitedUsersResult = $invitedUsersStmt->get_result();

        while ($invitedUserRow = $invitedUsersResult->fetch_assoc()) {
            $username = $invitedUserRow['username'];
            $phonenumber = $invitedUserRow['phone_number'];
            $registration_date = $invitedUserRow['registration_date'];

            // Check if the invited user has bought a package
            $packageSql = "SELECT name FROM Packages WHERE username = ?";
            $packageStmt = $conn->prepare($packageSql);

            if ($packageStmt) {
                $packageStmt->bind_param("s", $username);
                $packageStmt->execute();
                $packageResult = $packageStmt->get_result();

                // Check if any package is found
                $activeStatus = ($packageResult->num_rows > 0) ? 'Active' : 'Inactive';

                // Store the data in the $dat array
                $dat[] = [
                    'username' => $username,
                    'phone_number' => $phonenumber,
                    'registration_date' => $registration_date,
                    'active' => $activeStatus
                ];

                $packageStmt->close();
            } else {
                // Handle the case where the prepared statement for package fails
                $error = 'Prepare statement for package failed';
                error_log($error);
                echo json_encode(['error' => $error]);
                $conn->close();
                exit();
            }
        }

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
    echo json_encode($dat);
} else {
    // Handle the case where the session username is not set or empty
    $error = 'Session username not set or empty';
    error_log($error);
    echo json_encode(['error' => $error]);
    exit();
}
?>
