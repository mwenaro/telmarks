<?php
include 'config.php';

header("Content-Type: application/json");

// Get the raw callback data
$callbackData = file_get_contents('php://input');

// Log the callback response
$logFile = "Callback.log";
$log = fopen($logFile, "a");
fwrite($log, $callbackData);
fclose($log);

// Decode the callback data
$data = json_decode($callbackData);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    $errorMessage = "Error decoding JSON: " . json_last_error_msg();
    error_log($errorMessage);
    die($errorMessage);
}

// Extract common relevant information from the callback data
$ResultCode = isset($data->ResultCode) ? $data->ResultCode : $data->Body->stkCallback->ResultCode;
$Amount = isset($data->TransAmount) ? $data->TransAmount : ($data->Body->stkCallback->CallbackMetadata->Item[0]->Value ?? null);
$TransactionId = isset($data->TransID) ? $data->TransID : ($data->Body->stkCallback->CallbackMetadata->Item[1]->Value ?? null);
$UserPhoneNumber = isset($data->MSISDN) ? $data->MSISDN : ($data->Body->stkCallback->CallbackMetadata->Item[4]->Value ?? null);

// Log extracted information for debugging
error_log("ResultCode: $ResultCode");
error_log("Amount: $Amount");
error_log("TransactionId: $TransactionId");
error_log("UserPhoneNumber: $UserPhoneNumber");

// Only proceed if the transaction was successful and required values are present
if ($ResultCode == 0 && $Amount !== null && $TransactionId !== null && $UserPhoneNumber !== null) {
    // Remove '+254' or '254' from both phone numbers
    $cleanedPhoneNumberCallback = normalizePhoneNumber($UserPhoneNumber);

    // Log cleaned phone number for debugging
    error_log("Cleaned Callback Phone Number: $cleanedPhoneNumberCallback");

    // Initialize a MySQLi connection
    $db = new mysqli($host, $username, $password, $database);

    // Check if the connection was successful
    if ($db->connect_error) {
        $errorMessage = "Connection failed: " . $db->connect_error;
        error_log($errorMessage);
        die($errorMessage);
    }

    // Check if the cleaned phone number exists in the Users table
    $checkUserQuery = "SELECT username, phone_number FROM Users WHERE phone_number = ?";
    $stmtCheckUser = $db->prepare($checkUserQuery);
    
    // Check if the statement was prepared successfully
    if (!$stmtCheckUser) {
        $errorMessage = "Check user preparation error: " . $db->error;
        error_log($errorMessage);
        die($errorMessage);
    }

    // Bind parameter for phone number
    $stmtCheckUser->bind_param("s", $cleanedPhoneNumberCallback);

    $stmtCheckUser->execute();
    $stmtCheckUser->store_result();

    // Bind the result variable
    $stmtCheckUser->bind_result($username, $userPhoneNumber);

    // Variable to check if a match is found
    $matchFound = false;

    // Iterate over the results
    while ($stmtCheckUser->fetch()) {
        // Normalize phone numbers to ensure consistent comparison
        $cleanedPhoneNumberUser = normalizePhoneNumber($userPhoneNumber);

        // Debugging output
        error_log("Cleaned Callback Phone Number: $cleanedPhoneNumberCallback");
        error_log("Cleaned User Phone Number: $cleanedPhoneNumberUser");

        // Check if the cleaned phone numbers match
        if ($cleanedPhoneNumberCallback == $cleanedPhoneNumberUser) {
            // Determine the transaction type (STK or Paybill)
            $transactionType = isset($data->TransAmount) ? 'PAYBILL' : 'STK';

            // Insert into the appropriate Callbacks table
            $insertCallbacksQuery = "INSERT INTO ";
            $insertCallbacksQuery .= $transactionType === 'PAYBILL' ? "paybill_callbacks" : "mpesa_callbacks";
            $insertCallbacksQuery .= " (transaction_id, transaction_type, trans_time, trans_amount, phone_number) VALUES (?, ?, NOW(), ?, ?)";
            
            $stmtInsertCallbacks = $db->prepare($insertCallbacksQuery);

            // Check if the statement was prepared successfully
            if (!$stmtInsertCallbacks) {
                $errorMessage = "Callbacks insertion preparation error: " . $db->error;
                error_log($errorMessage);
                die($errorMessage);
            }

            // Create separate variables for parameters being passed by reference
            $stmtInsertCallbacksTransactionId = $TransactionId;
            $stmtInsertCallbacksTransactionType = $transactionType;
            $stmtInsertCallbacksAmount = $Amount;
            $stmtInsertCallbacksPhoneNumber = $cleanedPhoneNumberCallback;

            // Bind parameters
            $stmtInsertCallbacks->bind_param("ssds", $stmtInsertCallbacksTransactionId, $stmtInsertCallbacksTransactionType, $stmtInsertCallbacksAmount, $stmtInsertCallbacksPhoneNumber);

            // Execute the prepared statement
            if (!$stmtInsertCallbacks->execute()) {
                $errorMessage = "Callbacks insertion error: " . $stmtInsertCallbacks->error;
                error_log($errorMessage);
                die($errorMessage);
            }

            // Set match found flag to true
            $matchFound = true;

            // Close the statement
            $stmtInsertCallbacks->close();

            // Exit the loop since a match is found
            break;
        }
    }

    // Close the statement
    $stmtCheckUser->close();

    // If no match is found, log the error
    if (!$matchFound) {
        // Log the error when the phone number doesn't match anyone in the Users table
        $errorMessage = "Phone number $cleanedPhoneNumberCallback not found in Users table.";
        error_log($errorMessage);
    }

    // Close the database connection
    $db->close();
}

// Check if the transaction was successful and send the appropriate response
if ($ResultCode == 0) {
    // Transaction was successful
    echo json_encode(["message" => "Transaction successful"]);
} else {
    // Transaction failed
    echo json_encode(["message" => "Transaction failed"]);
}

/**
 * Normalize a phone number to a consistent format.
 *
 * @param string $phoneNumber
 * @return string
 */
function normalizePhoneNumber($phoneNumber) {
    // Add any additional normalization logic here
    return preg_replace("/^\+?254/", "", $phoneNumber);
}
?>
