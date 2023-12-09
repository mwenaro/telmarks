<?php
session_start();

// Database configuration
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

function redirectTo($page)
{
    echo "<div>Redirecting...</div>";
    header("refresh:5;url=$page"); // Redirect after 5 seconds
    exit();
}

function handleTransaction($conn, $username, $selectedAmount, $selectedPackage, $selectedCashback)
{
    // Verify the user
    $sql = "SELECT amount FROM Deposits WHERE username = ? ORDER BY deposit_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();

            if (!empty($row)) {
                // The session username is valid, update deposit balance
                $currentBalance = $row['amount'];

                if ($currentBalance >= $selectedAmount) {
                    $newBalance = $currentBalance - $selectedAmount;

                    // Update the deposits table with the new balance
                    $sqlUpdateBalance = "UPDATE Deposits SET amount = ? WHERE username = ? ORDER BY deposit_id DESC LIMIT 1";
                    $stmtUpdateBalance = $conn->prepare($sqlUpdateBalance);

                    if ($stmtUpdateBalance) {
                        $stmtUpdateBalance->bind_param("ss", $newBalance, $username);
                        $stmtUpdateBalance->execute();

                        if ($stmtUpdateBalance->affected_rows > 0) {
                            // Insert the selected package into the packages table
                            $sqlInsertPackage = "INSERT INTO Packages (username, name, lifetime) VALUES (?, ?, 0)";
                            $stmtInsertPackage = $conn->prepare($sqlInsertPackage);

                            if ($stmtInsertPackage) {
                                $stmtInsertPackage->bind_param("ss", $username, $selectedPackage);
                                $stmtInsertPackage->execute();

                                if ($stmtInsertPackage->affected_rows > 0) {
                                    // Insert cashback for referee into the referral_bonus table
                                    $sqlInsertCashback = "INSERT INTO ReferralBonus (username, bonus_amount) VALUES (?, ?)";
                                    $stmtInsertCashback = $conn->prepare($sqlInsertCashback);

                                    if ($stmtInsertCashback) {
                                        $stmtInsertCashback->bind_param("ss", $username, $selectedCashback);
                                        $stmtInsertCashback->execute();

                                        if ($stmtInsertCashback->affected_rows > 0) {
                                            return true; // Transaction successful
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return false; // Transaction failed
}

// Check if the user is logged in using the correct session variable
if (isset($_SESSION['user_name'])) {
    $username = $_SESSION['user_name'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['purchase_request']) && $_POST['purchase_request'] == true) {
            $selectedAmount = isset($_POST['selected_amount']) ? $_POST['selected_amount'] : null;
            $selectedPackage = isset($_POST['selected_package']) ? $_POST['selected_package'] : null;
            $selectedCashback = isset($_POST['selected_cashback']) ? $_POST['selected_cashback'] : null;

            if (handleTransaction($conn, $username, $selectedAmount, $selectedPackage, $selectedCashback)) {
                // Transaction successful, redirect to success page
                redirectTo("success_page.php");
            } else {
                // Transaction failed, redirect to error page
                redirectTo("error_trans.php");
            }
        } else {
            // Handle other cases if needed
        }
    }
} else {
    // User not logged in, redirect to error page
    redirectTo("error_trans.php");
}

// Close the database connection
$conn->close();
?>
