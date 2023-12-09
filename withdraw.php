<?php
// Start the session at the beginning of the script
session_start();

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the withdrawal amount from the form
    $withdrawalAmount = isset($_POST['withdrawalAmount']) ? intval($_POST['withdrawalAmount']) : 0;

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

        // Fetch bonus amounts for the specified user
        $query = "SELECT bonus_amount FROM ReferralBonus WHERE username = '$loggedInUser'";
        $result = $conn->query($query);

        // Initialize total bonus amount
        $bonusAmount = 0;

        while ($row = $result->fetch_assoc()) {
            $bonusAmount += $row['bonus_amount'];
        }

        // Check if the withdrawal amount is less than 600
        if ($withdrawalAmount < 630) {
            // Display pop-up message
            echo '<script>alert("Withdrawal amount must be at least 630.");</script>';
        } else {
            // Call function to update bonus and handle withdrawal
            updateBonusAndWithdrawal($loggedInUser, $withdrawalAmount, $bonusAmount);
        }

        // Close the mysqli connection
        $conn->close();
    }
}

// Function to update bonus and handle withdrawal
function updateBonusAndWithdrawal($username, $withdrawalAmount, $bonusAmount) {
    try {
        // Database connection details for PDO
        $pdoHost = "localhost";
        $pdoDBName = "telmarka_db";
        $pdoUser = "telmarka_db";
        $pdoPassword = "Benbrian@01";

        // Connect to the database using PDO
        $pdo = new PDO("mysql:host=$pdoHost;dbname=$pdoDBName", $pdoUser, $pdoPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Calculate 5% of the amount entered by the session user
        $deductionAmount = $withdrawalAmount * 0.05;

        // Add the results of the calculations plus the amount entered by the client
        $totalDeduction = $deductionAmount + $withdrawalAmount;

        // Deduct from bonus amounts in ReferralBonus table
        $stmt = $pdo->prepare("SELECT bonus_id, bonus_amount FROM ReferralBonus WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $bonusRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($bonusRows as $bonusRow) {
            $bonusId = $bonusRow['bonus_id'];  // Adjusted to 'bonus_id'
            $bonusRowAmount = $bonusRow['bonus_amount'];

            if ($totalDeduction >= $bonusRowAmount) {
                // Deduct from this row and update it to zero
                $totalDeduction -= $bonusRowAmount;
                $updateStmt = $pdo->prepare("UPDATE ReferralBonus SET bonus_amount = 0 WHERE bonus_id = :bonusId");
                $updateStmt->bindParam(':bonusId', $bonusId);
                $updateStmt->execute();
            } else {
                // Deduct from this row and update the remainder
                $newBonus = $bonusRowAmount - $totalDeduction;
                $updateStmt = $pdo->prepare("UPDATE ReferralBonus SET bonus_amount = :newBonus WHERE bonus_id = :bonusId");
                $updateStmt->bindParam(':newBonus', $newBonus);
                $updateStmt->bindParam(':bonusId', $bonusId);
                $updateStmt->execute();
                break;
            }
        }

        // Insert withdrawal request into WithdrawalRequests table
        $withdrawalStmt = $pdo->prepare("INSERT INTO WithdrawalRequests (points_to_withdraw, status, request_date, username) VALUES (:withdrawalAmount, 'Pending', NOW(), :username)");
        $withdrawalStmt->bindParam(':withdrawalAmount', $withdrawalAmount);
        $withdrawalStmt->bindParam(':username', $username);
        $withdrawalStmt->execute();

        // Provide a success message
        echo '<script>alert("Withdrawal successful! Please wait for admin approval.");</script>';

        // Close the PDO connection
        $pdo = null;

        // Clear the session cache
        session_write_close();
    } catch (PDOException $e) {
        // Provide an error message
        echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
    }
}
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

                        <!-- Transaction charges and account balance at the other end -->
                        <p style="color: #888; margin: 0; float: right;">
                            5% transaction charges apply. Account Balance: KES <?php echo isset($accountBalance) ? $accountBalance : ''; ?>
                        </p>
                        <br style="clear: both;" />

                        <!-- New input field for withdrawal amount -->
                        <div class="form-group">
                            <label for="withdrawalAmount" style="color: black; margin: 0; float: left;">Amount (KES)</label>
                            <input type="number" name="withdrawalAmount" class="form-control" id="withdrawalAmount" required />
                        </div>

                        <!-- Submit button -->
                        <button type="submit" name="submit" class="btn btn btn-block" style="background-color: orange; color: white;">
                            Withdraw
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

    <div class="container">
        <p style="font-weight: bold; line-height: 1.5;" class="text-center">WITHDRAWALS</p>

        <hr class="mx-auto mb-5 w-100%" />
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
                    <th class="line">Widthdrawn Amount</th>
                    <th class="line">Status</th>
                    <th class="line">Date</th>
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
        fetch(`withdrawalstable.php?page=${page}&entriesPerPage=${entriesPerPage}`)
            .then((response) => response.json())
            .then((data) => {
                for (let i = 0; i < data.length; i++) {
                    const entry = data[i];
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td class="line">${entry.withdrawnAmount}</td>
                        <td class="line">${entry.status}</td>
                        <td class="line">${entry.requestDate}</td>
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