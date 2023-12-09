<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("config.php");
session_start();

// Function to sanitize input
function sanitize($conn, $input)
{
    if (isset($conn)) {
        return mysqli_real_escape_string($conn, $input);
    }
    return $input;
}

// Function to check if a user exists by a given field (username or email)
function userExists($field, $value, $conn)
{
    $query = "SELECT id FROM Users WHERE $field = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $value);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) > 0;
}

// Function to generate a unique referral link
function generateReferralLink($username)
{
    return "https://telmarkagencies.com/Dashboard/register.php?ref=" . $username;
}

// Function to insert user data into the database
function insertUserData($conn, $your_name, $reg_email, $phone_number, $username, $password, $invited_by)
{
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the user being invited already exists
    if (userExists('username', $username, $conn)) {
        // Display error message as a JavaScript alert
        echo "<script>alert('Username already exists. Please choose a different username.'); window.location.href='register.php';</script>";
        exit();
    }

    // Generate and save referral link
    $referral_link = generateReferralLink($username);

    // Add the referral link and invited_by to the INSERT query
    $query = "INSERT INTO Users (name, email, phone_number, username, password, inviter_id, referral_link, invited_by, differ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'regular')";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        // Display error message as a JavaScript alert
        echo "<script>alert('Prepare failed: " . mysqli_error($conn) . "'); window.location.href='register.php';</script>";
        exit();
    }

    // Assuming 'inviter_id' and 'invited_by' are separate columns
    mysqli_stmt_bind_param($stmt, "ssssssss", $your_name, $reg_email, $phone_number, $username, $hashed_password, $invited_by, $referral_link, $invited_by);

    if (!mysqli_stmt_execute($stmt)) {
        // Display error message as a JavaScript alert
        echo "<script>alert('Execution failed: " . mysqli_stmt_error($stmt) . "'); window.location.href='register.php';</script>";
        exit();
    }

    mysqli_stmt_close($stmt);

    // Notify the registered user by email (you need to implement the email sending functionality)
    $subject = "Registration Successful";
    $message = "Dear $your_name,\n\nThank you for registering with us! Your registration was successful.";
    $headers = "From: info@telmarkagencies.com"; // Replace with your email address

    // Uncomment the following line to send the email
    // mail($reg_email, $subject, $message, $headers);

    // Display success message as a JavaScript alert
    echo "<script>alert('Registration successful! Please login.'); window.location.href='index.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $your_name = sanitize($conn, $_POST['your_name']);
    $reg_email = sanitize($conn, $_POST['reg_email']);
    $phone_number = sanitize($conn, $_POST['phone_number']);
    $username = sanitize($conn, $_POST['reg_username']);
    $password = sanitize($conn, $_POST['reg_password']); // No need to hash here
    $invited_by = sanitize($conn, $_POST['invited_by']);

    // Insert user data into the database
    insertUserData($conn, $your_name, $reg_email, $phone_number, $username, $password, $invited_by);
}
?>
