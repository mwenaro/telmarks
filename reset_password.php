<?php
ob_start();
include("config.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

function sanitize($conn, $input)
{
    return mysqli_real_escape_string($conn, $input);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = sanitize($conn, $_GET['token']);

    $sql = "SELECT * FROM Users WHERE reset_token = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        // Valid token, allow the user to reset the password
        $row = mysqli_fetch_assoc($result);

        // Display the reset password form
        echo "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Reset Password</title>
                    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css'>
                    <link rel='stylesheet' href='login.css'>
                    <!-- Add your custom CSS if needed -->
                </head>
                <body>

                <div class='container'>
                    <div class='row justify-content-center mt-5'>
                        <div class='col-md-6'>
                            <h2 class='text-center mb-4'>Reset Password</h2>
                            <form action='process_reset_password.php' method='post'>
                                <input type='hidden' name='email' value='{$row['email']}'>
                                <div class='form-group'>
                                    <label for='password'>New Password:</label>
                                    <input type='password' class='form-control' id='password' name='password' required>
                                </div>
                                <button type='submit' class='bth btn-primary'>Reset Password</button>
                            </form>
                        </div>
                    </div>
                </div>

                <script src='https://code.jquery.com/jquery-3.3.1.slim.min.js'></script>
                <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js'></script>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js'></script>
                <!-- Add your custom JavaScript if needed -->
                </body>
                </html>";
        exit();
    } else {
        echo "<script>alert('Invalid or expired token.'); window.location='login.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid request.'); window.location='login.php';</script>";
    exit();
}
?>
