<?php
session_start(); // Start or resume the session

if (isset($_SESSION['user_logged_in'])) {
    // Unset and destroy the session data
    session_unset();
    session_destroy();
}

// Redirect the user to index.html
header('Location: index.php');
exit;
?>
