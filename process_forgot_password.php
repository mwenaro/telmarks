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
    if (isset($_POST['email'])) {
        $email = sanitize($conn, $_POST['email']);

        $sql = "SELECT * FROM Users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Generate a unique token
            $token = bin2hex(random_bytes(32));

            // Store the token in the database
            $updateTokenSql = "UPDATE Users SET reset_token = ? WHERE email = ?";
            $updateTokenStmt = mysqli_prepare($conn, $updateTokenSql);
            mysqli_stmt_bind_param($updateTokenStmt, "ss", $token, $email);
            mysqli_stmt_execute($updateTokenStmt);

            // Send an email with a link to reset password
            $resetLink = "http://telmarkagencies.com/Dashboard/reset_password.php?token=$token";
            $to = $email;
            $subject = "Password Reset";
            $message = "Click the following link to reset your password: $resetLink";
            $headers = "From: info@telmarkagencies.com";

            mail($to, $subject, $message, $headers);

            echo "<script>alert('Password reset link sent to your email.'); window.location='index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Not a registered user.'); window.location='register.php';</script>";
            exit();
        }
    }
}
?>
