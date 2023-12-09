<!-- user_details_modal.php -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_username'])) {
    // Include the database connection
    include('Config.php');

    // Assuming $selectedUsername is sanitized
    $selectedUsername = $_POST['selected_username'];

    // Fetch Packages data for the selected user (replace with your actual function)
    $packagesData = fetchPackagesData($selectedUsername, $conn);

    // Fetch Deposits data for the selected user (replace with your actual function)
    $depositsData = fetchDepositsData($selectedUsername, $conn);

    // Fetch Referral Bonus data for the selected user (replace with your actual function)
    $referralBonusData = fetchReferralBonusData($selectedUsername, $conn);

    // Combine all data into an associative array
    $userData = [
        'packages' => $packagesData,
        'deposits' => $depositsData,
        'referral_bonus' => $referralBonusData,
    ];

    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode($userData);
} else {
    echo 'Invalid request';
}
?>
