<?php
// Start the session at the beginning of the script
session_start();

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the withdrawal amount from the form
    $withdrawalAmount = isset($_POST['withdrawalAmount']) ? intval($_POST['withdrawalAmount']) : 0;

    // Constant WhatsApp value for every transaction
    $constantWhatsApp = "whatsapp"; // Replace with your actual constant WhatsApp value

    // Check if the user is logged in
    if (isset($_SESSION['user_name'])) {
        $loggedInUser = $_SESSION['user_name'];

        // Database connection details for mysqli
        $dbHost = "localhost";
        $dbUser = "telmarka_db";
        $dbPassword = "Benbrian@01";
        $dbName = "telmarka_db";

        // Create a database connection using mysqli
        $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the user has a 'Diamond' package in the 'name' column
        $packageQuery = "SELECT COUNT(*) as packageCount FROM Packages WHERE username = '$loggedInUser' AND name = 'Diamond'";
        $packageResult = $conn->query($packageQuery);
        $packageCount = $packageResult->fetch_assoc()['packageCount'];

        if ($packageCount > 0) {
            // If the user has a 'Diamond' package, proceed with the withdrawal
            handleWithdrawal($loggedInUser, $withdrawalAmount, $constantWhatsApp, $conn);

        } else {
            // If the user doesn't have a 'Diamond' package, display an error message
            echo "<script>";
            echo "var errorMessage = 'Error: You don\\'t have a <strong style=\"text-transform: uppercase; font-weight: bold;\">DIAMOND PACKAGE</strong> and can only withdraw if you have bought a Diamond package.'; ";
            echo "alert(errorMessage);";
            echo "</script>";
        }

        // Close the mysqli connection
        $conn->close();
    }
}

// Function to handle withdrawal
function handleWithdrawal($username, $withdrawalAmount, $whatsapp, $conn) {
    try {
        // Insert withdrawal request into WithdrawalRequests table
        $withdrawalStmt = $conn->prepare("INSERT INTO WithdrawalRequests (points_to_withdraw, status, request_date, username, whatsapp) VALUES (?, 'Pending', NOW(), ?, ?)");
        $withdrawalStmt->bind_param('iss', $withdrawalAmount, $username, $whatsapp);
        $withdrawalStmt->execute();

        // Deduct from 'result' column in 'data' table for the specific user
        $deductionQuery = "UPDATE data SET result = CASE WHEN result - ? < 0 THEN 0 ELSE result - ? END WHERE username = ?";
        $deductionStmt = $conn->prepare($deductionQuery);
        $deductionStmt->bind_param('iss', $withdrawalAmount, $withdrawalAmount, $username);
        $deductionStmt->execute();

        // Check if there's a negative balance after deduction
        $checkNegativeQuery = "SELECT result FROM data WHERE username = ? LIMIT 1";
        $checkNegativeStmt = $conn->prepare($checkNegativeQuery);
        $checkNegativeStmt->bind_param('s', $username);
        $checkNegativeStmt->execute();
        $resultAfterDeduction = $checkNegativeStmt->get_result()->fetch_assoc()['result'];

        if ($resultAfterDeduction < 0) {
            // If the balance is negative, deduct the remaining amount from another table
            $remainingAmountToDeduct = abs($resultAfterDeduction);

            // Deduct from 'other_table' column for the specific user
            $otherTableDeductionQuery = "UPDATE other_table SET other_column = other_column - ? WHERE username = ?";
            $otherTableDeductionStmt = $conn->prepare($otherTableDeductionQuery);
            $otherTableDeductionStmt->bind_param('is', $remainingAmountToDeduct, $username);
            $otherTableDeductionStmt->execute();

            // Set the 'result' column in 'data' table to 0
            $resetResultQuery = "UPDATE data SET result = 0 WHERE username = ?";
            $resetResultStmt = $conn->prepare($resetResultQuery);
            $resetResultStmt->bind_param('s', $username);
            $resetResultStmt->execute();
        }

        // Send email to the user with the withdrawn amount
        sendEmail($username, $withdrawalAmount, $conn);

        // Provide a success message
        echo '<script>alert("Withdrawal successful! Please wait for admin approval.");</script>';

        // Clear the session cache
        session_write_close();
    } catch (PDOException $e) {
        // Provide an error message
        echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
    }
}

// Function to send an email to the user with the withdrawn amount
function sendEmail($username, $withdrawalAmount, $conn) {
    // Fetch user email from Users table
    $emailQuery = "SELECT email FROM Users WHERE username = ?";
    $emailStmt = $conn->prepare($emailQuery);
    $emailStmt->bind_param('s', $username);
    $emailStmt->execute();
    $userEmail = $emailStmt->get_result()->fetch_assoc()['email'];

    if ($userEmail) {
        // Send email with the withdrawn amount
        $to = $userEmail;
        $subject = "Withdrawal Authorization";
        $message = "Dear $username,\n\nYour withdrawal request of Kes$withdrawalAmount has been received and is pending admin approval. Please wait for further instructions.\n\nThank you for using our service.\n\nThis message is autogenerated by the system.";
        $headers = "From: info@telmarkagencies.com"; // Replace with your actual email

        // Use mail() function to send the email
        mail($to, $subject, $message, $headers);
    }
}
// ... (remaining code)
?>




<!DOCTYPE html>
<html>

<head>
    <title>TELMARK DASHBOARD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="side.css" />
</head>

<body>
    <header>
        <div class="header-content">
            <div class="notify-icon">
                <span class="las la-bell"></span>
                <span class="notify">1</span>
            </div>
            <div class="user" id="user">
                <div class="bg-img" style="background-image: url(img/1.jpeg)"></div>
                <span class="las la-user" id="profile-icon"></span>
                <span>Account</span>
            </div>

            <div class="profile-card" id="profile-card">
                <span class="close-icon" id="close-icon">&times;</span>
                <a href="updateprofile.php">
                    <button class="update-button">Update Account</button>
                </a>

                <a href="logout.php">
                    <button class="logout-button">Logout</button>
                </a>

            </div>

        </div>
    </header>

    <!-- Vertical navbar -->
        <div class="vertical-nav bg-white" id="sidebar">
        <div class="py-4 px-3 mb-4 bg-light">
            <a href="https://telmarkagencies.com/">
                <img src="./logos/telmarklogo.png" alt="" style="max-width: 100%" />
            </a>
        </div>

        <p class="text-gray font-weight-bold text-uppercase px-3 small pb-4 mb-0">
            NAVIGATION
        </p>
        <nav>
            <ul class="nav flex-column bg-white mb-0">
                <li class="nav-item">
                    <a href="./dashboard.php" class="nav-link text-dark">
                        <img src="./logos/dashboard icon.png" alt="Package" class="icon-image" />
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./package.php" class="nav-link text-dark">
                        <img src="./logos/package icon.png" alt="Package" class="icon-image" />
                        Package
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./todaysproduct.php" class="nav-link text-dark">
                        <img src="./logos/package icon.png" alt="Package" class="icon-image" />
                        Todays Product
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./advertise.php" class="nav-link text-dark">
                        <img src="./logos/advertise.png" alt="Advertise with us" class="icon-image" />
                        Advertise with us
                    </a>
                </li>
            </ul>
        </nav>

        <p class="text-gray font-weight-bold text-uppercase px-3 small py-4 mb-0">
            MY FINANCIALS
        </p>

        <nav>
            <ul class="nav flex-column bg-white mb-0">
                <li class="nav-item">
                    <a href="./writing.php" class="nav-link text-dark">
                        <img src="./logos/transfericon.png" alt="Package" class="icon-image" />
                        Writing Package
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./submit.php" class="nav-link text-dark">
                        <img src="./logos/profiticon.png" alt="Package" class="icon-image" />
                        Submit Writing
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./deposit.php" class="nav-link text-dark">
                        <img src="./logos/deposits icon.png" alt="Package" class="icon-image" />
                        Deposits
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link text-dark dropdown-btn" href="#" id="financesDropdown" role="button">
                        <!-- Use an image for the "Finances" icon -->
                        <img src="./logos/finances icon.png" alt="Finances Icon" class="icon-image mr-3" />
                        Finances
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <div class="dropdown-container" aria-labelledby="financesDropdown">
                        <!-- Use images for the "Transfers" and "Withdrawals" icons -->
                        <a class="dropdown-item" href="./transfer.php">
                            Transfers
                        </a>
                        <a class="dropdown-item" href="./withdraw.php">
                            Withdrawals
                        </a>
                    </div>
                </li>


                <li class="nav-item">
                    <a href="./gold.php" class="nav-link text-dark">
                        <img src="./logos/gold membership icon.png" alt="Package" class="icon-image" />
                        Gold Membership
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./trading.php" class="nav-link text-dark">
                        <img src="./logos/trading bot.png" alt="Package" class="icon-image" />
                        Trading bot
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./screenshot.php" class="nav-link text-dark">
                        <img src="./logos/agentauthorization.png" alt="Package" class="icon-image" />
                        Submit Screenshot
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./whatsappwithdrawal.php" class="nav-link text-dark">
                        <img src="./logos/whatsapp withdrawals icon.png" alt="Package" class="icon-image" />
                        Whatsapp Withdrawals
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./authorization.php" class="nav-link text-dark">
                        <img src="./logos/agentauthorization.png" alt="Package" class="icon-image" />
                        Agent Authorization
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./verification.php" class="nav-link text-dark">
                        <img src="./logos/agentverificationicon.png" alt="Package" class="icon-image" />
                        Agent Verification
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./customer.php" class="nav-link text-dark">
                        <img src="./logos/customer care icon.png" alt="Package" class="icon-image" />
                        Customer Care
                    </a>
                <li class="nav-item dropdown">
                    <a class="nav-link text-dark dropdown-btn" href="#" id="affiliateprogramsDropdown" role="button">
                        <!-- Use an image for the "Affiliate Programs" icon -->
                        <img src="./logos/affiliateprogramicon.png" alt="Affiliate Programs Icon"
                            class="icon-image mr-3" />
                        Affiliate Programs
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <div class="dropdown-container" aria-labelledby="affiliateprogramsDropdown">
                        <!-- Use images for the "All Affiliates," "Star Affiliates," "Platinum Affiliates," and "Diamond Affiliates" icons -->
                        <a class="dropdown-item" href="./allaffiliates.php">
                            All Affiliates
                        </a>
                        <a class="dropdown-item" href="staraffiliates.php">
                            Star Affiliates
                        </a>
                        <a class="dropdown-item" href="./platinum.php">
                            Platinum Affiliates
                        </a>
                        <a class="dropdown-item" href="./diamond.php">
                            Diamond Affiliates
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
    <!-- End vertical navbar -->

    <!-- Page content holder -->
    <div class="page-content p-5" id="content">


        <!-- Packages content -->

        <div class="section" id="submit" style="margin-top:30px; padding: 20px; margin-bottom: 30px; background:#fff;">

<!-- HTML code for the withdrawal form -->
<section class="volunteer-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-3">
                <form class="volunteer-form" enctype="multipart/form-data" method="post" action="#">
                    <!-- Container for styling -->
                    <div style="margin-bottom: 30px; background: #fff; padding: 10px; text-align: center;">

                        <!-- Make a Withdrawal in bold at one end -->
                        <p style="color: black; font-weight: bold; margin: 0; float: left;">Make a Withdrawal</p>

                        <!-- Line break -->
                        <br style="clear: both;" />
                        <br style="clear: both;" />


                        <!-- New input field for withdrawal amount -->
                        <div class="form-group">
                            <label for="withdrawalAmount" style="color: black; margin: 0; float: left;">Amount (KES)</label>
                            <input type="number" name="withdrawalAmount" class="form-control" id="withdrawalAmount" required />
                        </div>

                        <!-- Submit button -->
                        <button type="submit" name="submit" class="btn btn btn-block" style="background-color: orange; color: white;">
                            Withdraw WhatsApp Earning
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


        <div id="content-wrapp" style="background: #fff; padding: 40px;">
            <div class="show-entries">
                <span>Show Entries</span>
                <select id="show-entries">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <table class="table" id="my-table">
                <thead>
                    <tr>
                        <th class="line">Amount Withdrawn</th>
                        <th class="line">Date</th>
                        <th class="line">Status</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <!-- Data from the database will be inserted here -->
                </tbody>
            </table>
            <table style="float: right; padding: 0;">
                <tr>
                    <td style="padding: 0;">
                        <button id="previous-btn" class="btn btn"><i class="bi bi-chevron-left"></i> Previous</button>
                    </td>
                    <td style="padding: 0;">
                        <span id="page-number">1</span>
                    </td>
                    <td style="padding: 0;">
                        <button id="next-btn" class="btn btn">Next <i class="bi bi-chevron-right"></i></button>
                    </td>
                </tr>
            </table>

        </div>


        </div>
        </div>

        <!-- End demo content -->

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
            </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
            </script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
            </script>
        <script>
            var dropdown = document.getElementsByClassName("dropdown-btn");
            var i;

            for (i = 0; i < dropdown.length; i++) {
                dropdown[i].addEventListener("click", function () {
                    this.classList.toggle("active");
                    var dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                    } else {
                        dropdownContent.style.display = "block";
                    }
                });
            }
            
document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("table-body");
    const showEntries = document.getElementById("show-entries");
    const pageNumber = document.getElementById("page-number");
    const previousButton = document.getElementById("previous-btn");
    const nextButton = document.getElementById("next-btn");

    let currentPage = 1;
    let entriesPerPage = 10;

    // Function to populate the table
    function populateTable(page) {
        tableBody.innerHTML = "";
        const start = (page - 1) * entriesPerPage;
        const end = start + entriesPerPage;

        // Make an AJAX request to fetch data from the PHP script
        fetch(`whatsapptable.php?page=${page}&entries=${entriesPerPage}`)
            .then((response) => response.json())
            .then((data) => {
                for (let i = 0; i < data.length; i++) {
                    const entry = data[i];
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td class="line">${entry.points_to_withdraw}</td>
                        <td class="line">${entry.request_date}</td>
                        <td class="line">${entry.status}</td>
                    `;
                    tableBody.appendChild(row);
                }
            });
    }

    // Function to handle next and previous buttons
    function updateTable() {
        populateTable(currentPage);
        pageNumber.innerText = currentPage;
    }

    // Fetch initial data when the page loads
    updateTable();

    // Event listeners for next and previous buttons
    nextButton.addEventListener("click", () => {
        currentPage++;
        updateTable();
        previousButton.style.display = "block";
    });

    previousButton.addEventListener("click", () => {
        currentPage--;
        updateTable();
        if (currentPage === 1) {
            previousButton.style.display = "none";
        }
    });

    // Event listener for changing entries per page
    showEntries.addEventListener("change", () => {
        entriesPerPage = parseInt(showEntries.value, 10);
        currentPage = 1;
        updateTable();
    });
});


        </script>
        <script src="doc.js"></script>
</body>

</html>