<?php
// Database connection settings
$hostname = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$database = "telmarka_db";

// Create a database connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Extract data sent from the STK push script
if (isset($_POST['transactionID'])) {
    $transactionID = $_POST['transactionID'];
    // You can extract and handle other data here as well

    // Prepare an SQL statement to insert data into the 'mpesa_transactions' table
    $sql = "INSERT INTO mpesa_transactions (merchant_request_id, checkout_request_id, response_code, response_description, phone_number, amount, transaction_date) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";

    // Create a prepared statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param("ssssss", $transactionID, $checkoutRequestID, $responseCode, $responseDescription, $phoneNumber, $amount);

        // You can bind additional parameters here as needed, just make sure to adjust the bind_param and values accordingly

        // Set values for the bound parameters
        $checkoutRequestID = $_POST['checkoutRequestID'];
        $responseCode = $_POST['responseCode'];
        $responseDescription = $_POST['responseDescription'];
        $phoneNumber = $_POST['phoneNumber'];
        $amount = $_POST['amount'];

        // Execute the statement
        if ($stmt->execute()) {
            // Data successfully inserted into the database
            $response = array(
                "success" => true,
                "message" => "Data saved to the database."
            );
        } else {
            // Database insertion error
            $response = array(
                "success" => false,
                "error" => "Database error: " . $stmt->error
            );
        }

        // Close the statement
        $stmt->close();
    } else {
        // Statement preparation error
        $response = array(
            "success" => false,
            "error" => "Database statement error: " . $conn->error
        );
    }

    // Close the database connection
    $conn->close();
} else {
    // Invalid request
    $response = array(
        "success" => false,
        "error" => "Invalid request."
    );
}

// Send the response back to the STK push script
header('Content-Type: application/json');
echo json_encode($response);
?>
