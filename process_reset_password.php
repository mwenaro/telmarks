<?php
ob_start();
include("config.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

function sanitize($conn, $input)
{
    return mysqli_real_escape_string($conn, $input);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = sanitize($conn, $_POST['email']);
        $password = $_POST['password'];

        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Update the password in the database
        $updatePasswordSql = "UPDATE Users SET password = ?, reset_token = NULL WHERE email = ?";
        $updatePasswordStmt = mysqli_prepare($conn, $updatePasswordSql);
        mysqli_stmt_bind_param($updatePasswordStmt, "ss", $hashedPassword, $email);
        mysqli_stmt_execute($updatePasswordStmt);

        echo "<script>alert('Password reset successful.'); window.location='index.php';</script>";
        exit();
    }
}
?>
