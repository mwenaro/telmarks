<?php
// Start a session (if not started already)
session_start();

// Database connection details
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables to store user data
$sessionUsername = $username = $full_name = $phone_number = $email = "";

// Check if the session variable is set and not empty
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $sessionUsername = $_SESSION['user_name'];

    // Pre-fill the form with user data
    $sql = "SELECT * FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sessionUsername);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $username = $row['username'];
            $email = $row['email'];
            $full_name = $row['full_name'];
            $phone_number = $row['phone_number'];
        }
    }
}

// Close the statement
$stmt->close();

// Handle form submission
if (isset($_POST['update'])) {
    // Get user data from the form
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            // SQL query to update user data (including the profile image)
            $sql = "UPDATE Users SET full_name = ?, phone_number = ?, email = ?, profile_image = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssss", $full_name, $phone_number, $email, $target_file, $sessionUsername);

                if ($stmt->execute()) {
                    // Data updated successfully
                    echo json_encode(['message' => 'Account profile updated successfully']);
                } else {
                    // Error occurred during execution
                    echo json_encode(['message' => 'Error updating user data: ' . $stmt->error]);
                }

                // Close the statement
                $stmt->close();
            } else {
                // Error preparing the statement
                echo json_encode(['message' => 'Error preparing statement: ' . $conn->error]);
            }
        } else {
            // Error moving the uploaded file
            echo json_encode(['message' => 'Error moving the uploaded file']);
        }
    } else {
        // Profile picture not uploaded
        echo json_encode(['message' => 'Error: Profile picture not uploaded']);
    }
}

// Close the database connection
$conn->close();
?>
