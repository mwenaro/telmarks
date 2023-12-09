<?php
ob_start();
include("config.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

function sanitize($conn, $input)
{
    return mysqli_real_escape_string($conn, $input);
}

function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $username = sanitize($conn, $_POST['login_username']);
        $password = $_POST['login_password'];

        if (empty($username) || empty($password)) {
            echo "<script>alert('Both fields are required for login.'); window.location='index.php';</script>";
        } else {
            $sql = "SELECT * FROM Users WHERE username = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $hashedPassword = $row['password'];
                $differValue = $row['differ'];

                if (verifyPassword($password, $hashedPassword)) {
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_name'] = $row['username'];
                    $_SESSION['timeout'] = time() + 15 * 60;

                    if ($differValue === 'regular') {
                        header('Location: dashboard.php');
                        exit();
                    } elseif ($differValue === 'administrateur') {
                        header('Location: admin.php');
                        exit();
                    }
                } else {
                    echo "<script>alert('Login failed. Invalid credentials.'); window.location='index.php';</script>";
                }
            } else {
                echo "<script>alert('Login failed. Invalid credentials.'); window.location='index.php';</script>";
            }
            
            error_log(print_r($_POST, true)); // Check server logs for the output

        }
    }
}

if (isset($_SESSION['timeout']) && time() > $_SESSION['timeout']) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
