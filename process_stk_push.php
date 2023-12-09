<?php
// Start session if needed
// session_start();

// Configuration settings
$config = array(
    "env" => "live",
    "BusinessShortCode" => "4122749",
    "key" => "CnzHJORi1ctJWJ4ESfDGuWrgLAo6bSbD",
    "secret" => "73UrBj3Ibu2rUbhM",
    "username" => "telmarkagencies",
    "TransactionType" => "CustomerPayBillOnline",
    "passkey" => "d2d29fb04c697aae09b464d73e933873646a3ef551823209d4d951705eb73df9",
    "CallBackURL" => "https://telmarkagencies.com/Dashboard/callback.php",
    "AccountReference" => "Telmark Agencies",
    "TransactionDesc" => "Deposit",
);

if (isset($_POST['phone_number'], $_POST['amount'])) {
    // Extract data from the form
    $phone = $_POST['phone_number'];
    $amount = $_POST['amount'];

    // Normalize the phone number
    $phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
    $phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
    $phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;

    // Generate an access token
    $access_token_url = ($config['env'] == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://live.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
    $credentials = base64_encode($config['key'] . ':' . $config['secret']);

    $ch = curl_init($access_token_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    // Handle cURL error
    if (curl_errno($ch)) {
        handleCurlError($ch);
    }

    curl_close($ch);
    $result = json_decode($response);
    $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

    // Generate timestamp and password
    $timestamp = date("YmdHis");
    $password = base64_encode($config['BusinessShortCode'] . "" . $config['passkey'] . "" . $timestamp);

    // Construct data for the STK Push
    $curl_post_data = array(
        "BusinessShortCode" => $config['BusinessShortCode'],
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => $config['TransactionType'],
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => $config['BusinessShortCode'],
        "PhoneNumber" => $phone,
        "CallBackURL" => $config['CallBackURL'],
        "AccountReference" => $config['AccountReference'],
        "TransactionDesc" => $config['TransactionDesc'],
    );

    $data_string = json_encode($curl_post_data);

    // Determine the API endpoint based on the environment
    $endpoint = ($config['env'] == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" : "https://live.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

    // Send the STK Push request
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    // Handle cURL error
    if (curl_errno($ch)) {
        handleCurlError($ch);
    }

    curl_close($ch);

    $result = json_decode($response, true);

    // Debugging line
    if ($result['ResponseCode'] === "0") {
        handleSuccess($result, $phone, $amount);
    } else {
        handleFailure($result);
    }
}

// Function to handle cURL errors
function handleCurlError($ch) {
    echo "cURL Error: " . curl_error($ch);
    exit;
}

// Function to handle successful STK Push
function handleSuccess($result, $phone, $amount) {
    $MerchantRequestID = $result['MerchantRequestID'];
    $CheckoutRequestID = $result['CheckoutRequestID'];

    // Add this line for debugging
    error_log("STK Push successful: MerchantRequestID=$MerchantRequestID, CheckoutRequestID=$CheckoutRequestID");

    // Database Connection
    $conn = mysqli_connect("localhost", "telmarka_db", "Benbrian@01", "telmarka_db");

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Insert data into the database using prepared statement
    $sql = "INSERT INTO `mpesa_transactions` (`id`, `merchant_request_id`, `checkout_request_id`, `response_code`, `response_description`, `phone_number`, `amount`, `transaction_date`) 
    VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $MerchantRequestID, $CheckoutRequestID, $result['ResponseCode'], $result['ResponseDescription'], $phone, $amount);

    if ($stmt->execute()) {
        // Data inserted successfully
        // Redirect to success.php
        header("Location: success.php");
        exit();
    } else {
        // Handle database insertion error
        handleDatabaseError($stmt, $conn);
    }
}

// Function to handle database errors
function handleDatabaseError($stmt, $conn) {
    $response = array(
        "success" => false,
        "message" => "Database insertion failed",
        "error" => $stmt->error
    );

    echo json_encode($response);
    exit;
}

// Function to handle STK Push failure
function handleFailure($result) {
    $response = array(
        "success" => false,
        "message" => "STK Push failed",
        "error" => $result['errorMessage']
    );

    echo json_encode($response);
    exit;
}
?>
