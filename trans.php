<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$database = "telmarka_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to redirect with a message
function redirectWithMessage($message, $redirectLocation) {
    $_SESSION['message'] = $message;
    header("Location: $redirectLocation");
    exit();
}

// Generate and store form token in the session
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

// Check if the form is submitted
if (isset($_POST['transfer'])) {
    // Verify form token
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        // Invalid form submission
        redirectWithMessage("Error: Invalid form submission.", "error_trans.php");
    }

    // Clear the used form token
    unset($_SESSION['form_token']);

    // Check if the session is active
    if (!isset($_SESSION['user_name'])) {
        redirectWithMessage("Error: Session not active or username not set.", "transfer.php");
    }

    // Get and sanitize form data
    $recipientUsername = mysqli_real_escape_string($conn, $_POST['recipientUsername']);
    $transferAmount = floatval($_POST['transferAmount']);

    // Assign the value to $loggedInUsername
    $loggedInUsername = $_SESSION['user_name'];

    // Check if the recipient exists in the Users table
    $checkRecipientQuery = "SELECT id FROM Users WHERE LOWER(username) = LOWER(?)";
    $stmtCheckRecipient = $conn->prepare($checkRecipientQuery);
    $stmtCheckRecipient->bind_param("s", $recipientUsername);
    $stmtCheckRecipient->execute();
    $stmtCheckRecipient->store_result();

    if ($stmtCheckRecipient->num_rows !== 1) {
        // Redirect with an error message if the recipient is not found
        echo "<script>alert('Error: The recipient is not a registered user.'); window.location.href = 'transfer.php';</script>";
        exit();
    }

    // Check if the recipient has a deposit entry
    $checkRecipientDepositQuery = "SELECT COUNT(*) FROM Deposits WHERE username = ?";
    $stmtCheckRecipientDeposit = $conn->prepare($checkRecipientDepositQuery);
    $stmtCheckRecipientDeposit->bind_param("s", $recipientUsername);
    $stmtCheckRecipientDeposit->execute();
    $stmtCheckRecipientDeposit->bind_result($depositCount);
    $stmtCheckRecipientDeposit->fetch();
    $stmtCheckRecipientDeposit->close(); // Close this statement to resolve the issue

    if ($depositCount === 0) {
        // If the recipient has no deposit entry, insert a new entry
        $insertDepositQuery = "INSERT INTO Deposits (amount, transaction_date, username) VALUES (0, CURRENT_TIMESTAMP, ?)";
        $stmtInsertDeposit = $conn->prepare($insertDepositQuery);
        $stmtInsertDeposit->bind_param("s", $recipientUsername);
        $stmtInsertDeposit->execute();
        $stmtInsertDeposit->close(); // Close this statement to resolve the issue
    }

    // Perform the transfer
    $conn->begin_transaction();

    // Deduct amount from sender's ReferralBonus entries
    $deductReferralBonusQuery = "UPDATE ReferralBonus SET bonus_amount = CASE WHEN bonus_amount >= ? THEN bonus_amount - ? ELSE 0 END WHERE username = ?";
    $stmtDeductReferralBonus = $conn->prepare($deductReferralBonusQuery);
    $stmtDeductReferralBonus->bind_param("dds", $transferAmount, $transferAmount, $loggedInUsername);
    $stmtDeductReferralBonus->execute();
    $stmtDeductReferralBonus->close(); // Close this statement to resolve the issue

    // Add amount to recipient's Deposits
    $addQuery = "UPDATE Deposits SET amount = amount + ? WHERE username = ?";
    $stmtAdd = $conn->prepare($addQuery);
    $stmtAdd->bind_param("ds", $transferAmount, $recipientUsername);
    $stmtAdd->execute();
    $stmtAdd->close(); // Close this statement to resolve the issue

    // Record the transaction in the Transfers table
    $recordTransferQuery = "INSERT INTO Transfers (sender_username, recipient_username, amount, transaction_date) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmtRecordTransfer = $conn->prepare($recordTransferQuery);
    $stmtRecordTransfer->bind_param("ssd", $loggedInUsername, $recipientUsername, $transferAmount);
    $stmtRecordTransfer->execute();
    $stmtRecordTransfer->close(); // Close this statement to resolve the issue

    // Commit the transaction
    $conn->commit();

    // Redirect to success_trans.php
    redirectWithMessage("Transfer successful!", "success_trans.php");
} else {
    // Redirect to error_trans.php with form not submitted message
    redirectWithMessage("Error: Form not submitted.", "error_trans.php");
}

// Close the prepared statements

// Close the database connection
$conn->close();
?>
