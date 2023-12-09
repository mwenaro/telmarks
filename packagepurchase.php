<?php
// Start or resume the session
session_start();

// Assuming you have a database connection established
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

// Check if the user is logged in using the correct session variable
if (isset($_SESSION['user_name'])) {
    // Retrieve the session username
    $username = $_SESSION['user_name'];

    // Get the selected amount, package, and cashback from the request
    $selectedAmount = $_POST['selected_amount'];
    $selectedPackage = $_POST['selected_package'];
    $selectedCashback = $_POST['selected_cashback'];

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
                // Fetch the user's current balance from the database
                $currentBalance = $row['amount'];

                // Check if the user has sufficient balance
                if ($currentBalance >= $selectedAmount) {
                    // Calculate the new balance after deducting the selected amount
                    $newBalance = $currentBalance - $selectedAmount;

                    // Update the deposits table with the new balance
                    $sqlUpdateBalance = "UPDATE Deposits SET amount = ? WHERE username = ? ORDER BY deposit_id DESC LIMIT 1";
                    $stmtUpdateBalance = $conn->prepare($sqlUpdateBalance);

                    if ($stmtUpdateBalance) {
                        $stmtUpdateBalance->bind_param("ss", $newBalance, $username);
                        $stmtUpdateBalance->execute();

                        // Check if the update was successful
                        if ($stmtUpdateBalance->affected_rows > 0) {
                            // Insert the selected package into the packages table
                            $sqlInsertPackage = "INSERT INTO Packages (username, name, lifetime) VALUES (?, ?, 0)";
                            $stmtInsertPackage = $conn->prepare($sqlInsertPackage);

                            if ($stmtInsertPackage) {
                                $stmtInsertPackage->bind_param("ss", $username, $selectedPackage);
                                $stmtInsertPackage->execute();

                                // Check if the insert was successful
                                if ($stmtInsertPackage->affected_rows > 0) {
                                    // Insert cashback for referee into the referral_bonus table
                                    $sqlInsertCashback = "INSERT INTO ReferralBonus (username, bonus_amount) VALUES (?, ?)";
                                    $stmtInsertCashback = $conn->prepare($sqlInsertCashback);

                                        if ($stmtInsertCashback) {
                                            $stmtInsertCashback->bind_param("ss", $username, $selectedCashback);
                                            $stmtInsertCashback->execute();
                                        
                                            // Check if the insert was successful
                                            if ($stmtInsertCashback->affected_rows > 0) {
                                                // Close the database connection
                                                $stmtInsertCashback->close();
                                                $stmtInsertPackage->close();
                                                $stmtUpdateBalance->close();
                                                $stmt->close();
                                                $conn->close();
                                        
                                                // Send response to the client
                                                echo "success";
                                                exit();
                                            } else {
                                                echo "Error in inserting cashback record: " . $stmtInsertCashback->error;
                                            }
                                        } else {
                                            echo "Error in preparing cashback SQL statement: " . $conn->error;
                                        }

                                } else {
                                    echo "Error in inserting package record: " . $stmtInsertPackage->error;
                                }
                            } else {
                                echo "Error in preparing package SQL statement: " . $conn->error;
                            }
                        } else {
                            echo "Error in updating balance: " . $stmtUpdateBalance->error;
                        }
                    } else {
                        echo "Error in preparing update balance SQL statement: " . $conn->error;
                    }
                } else {
                    // Display an error message in a div on the same page using JavaScript and reload the page
                    echo "<script>
                            alert('Error: Insufficient balance for the transaction.');
                            window.location.reload();
                          </script>";
                }
            } else {
                echo "Error: User not found in deposits table.";
            }
        } else {
            echo "Error in fetching balance result: " . $stmt->error;
        }

        // Close prepared statements
        $stmtInsertCashback->close();
        $stmtInsertPackage->close();
        $stmtUpdateBalance->close();
        $stmt->close();
    } else {
        echo "Error in preparing SQL statement: " . $conn->error;
    }
} else {
    echo "Error: User not logged in.";
}
?>
