<?php
// Step 1: Obtain OAuth2 Token
$oauthUrl = "https://api.safaricom.co.ke/oauth/v1/generate";
$consumerKey = "CnzHJORi1ctJWJ4ESfDGuWrgLAo6bSbD";
$consumerSecret = "73UrBj3Ibu2rUbhM";

$credentials = base64_encode($consumerKey . ':' . $consumerSecret);

$ch = curl_init($oauthUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$oauthResponse = curl_exec($ch);

if (curl_errno($ch)) {
    die("Error obtaining OAuth2 Token: " . curl_error($ch));
}

curl_close($ch);

$oauthData = json_decode($oauthResponse, true);
$accessToken = isset($oauthData['access_token']) ? $oauthData['access_token'] : null;

if (!$accessToken) {
    die("Error obtaining OAuth2 Token. Check your credentials.");
}

echo "OAuth2 Token Obtained: $accessToken\n";

// Step 2: Register Callback URL
$registerUrl = "https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl";
$callbackUrl = "https://telmarkagencies.com/Dashboard/callback.php";
$shortCode = "4122749";

$ch = curl_init($registerUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json",
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "ShortCode" => $shortCode,
    "ResponseType" => "Complete",
    "ConfirmationURL" => $callbackUrl,
    "ValidationURL" => $callbackUrl,
]));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$registerResponse = curl_exec($ch);

if (curl_errno($ch)) {
    die("Error registering Callback URL: " . curl_error($ch));
}

curl_close($ch);

echo "Callback URL Registration Response: $registerResponse\n";

// Step 3: Implement Callback Logic (Handle PayBill Transactions)
// Assume the PayBill callback data is received as a POST request
$payBillCallbackData = file_get_contents('php://input');
$payBillCallbackJson = json_decode($payBillCallbackData, true);

// Log the PayBill callback URL in JSON format
file_put_contents("paybill_callback.log", json_encode($payBillCallbackJson) . PHP_EOL, FILE_APPEND);

// Step 4: Insert into Database
$host = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$database = "telmarka_db";

$db = new mysqli($host, $username, $password, $database);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Insert data into the 'paybill_callbacks' table (adjust table and column names as needed)
$insertQuery = "INSERT INTO paybill_callbacks (transaction_id, transaction_type, trans_time, trans_amount, phone_number) VALUES (?, ?, NOW(), ?, ?)";
$stmt = $db->prepare($insertQuery);

if (!$stmt) {
    die("Insertion preparation error: " . $db->error);
}

$transactionId = $payBillCallbackJson['TransID'];
$transactionType = "PAYBILL";
$transAmount = $payBillCallbackJson['TransAmount'];
$phoneNumber = $payBillCallbackJson['MSISDN'];

$stmt->bind_param("ssds", $transactionId, $transactionType, $transAmount, $phoneNumber);

if (!$stmt->execute()) {
    die("Insertion error: " . $stmt->error);
}

$stmt->close();
$db->close();

echo "PayBill Callback data logged and inserted into the database.\n";
?>
