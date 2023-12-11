<?php
session_start();

ob_start();

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

function handleTransaction($conn, $username, $selectedAmount, $selectedPackage, $selectedCashback)
{
    // Check if the user has an invited_by in the Users table
    $sqlInvitedBy = "SELECT invited_by FROM Users WHERE username = ?";
    $stmtInvitedBy = $conn->prepare($sqlInvitedBy);

    error_log('Handling transaction for user: ' . $username);
    
    $stmtUpdateCashback = null;

    if ($stmtInvitedBy) {
        $stmtInvitedBy->bind_param("s", $username);
        $stmtInvitedBy->execute();
        $resultInvitedBy = $stmtInvitedBy->get_result();
        $rowInvitedBy = $resultInvitedBy->fetch_assoc();

        if (!empty($rowInvitedBy['invited_by'])) {
            $invitedByUsername = $rowInvitedBy['invited_by'];

            // Check if $invitedByUsername is a valid username
            $sqlCheckUser = "SELECT username FROM Users WHERE username = ?";
            $stmtCheckUser = $conn->prepare($sqlCheckUser);

            if ($stmtCheckUser) {
                $stmtCheckUser->bind_param("s", $invitedByUsername);
                $stmtCheckUser->execute();
                $resultCheckUser = $stmtCheckUser->get_result();
                $rowCheckUser = $resultCheckUser->fetch_assoc();

                if (!empty($rowCheckUser['username'])) {
                    // Update the cashback for the invited user in the ReferralBonus table
                    if ($invitedByUsername !== $username) {
                        $sqlUpdateCashback = "INSERT INTO ReferralBonus (username, bonus_amount) VALUES (?, ?) ON DUPLICATE KEY UPDATE bonus_amount = bonus_amount + ?";
                        $stmtUpdateCashback = $conn->prepare($sqlUpdateCashback);

                        if ($stmtUpdateCashback) {
                            $stmtUpdateCashback->bind_param("sss", $invitedByUsername, $selectedCashback, $selectedCashback);
                            $stmtUpdateCashback->execute();

                            if ($stmtUpdateCashback->affected_rows <= 0) {
                                error_log('Update cashback failed.');
                                return json_encode(['status' => 'error', 'message' => 'Update cashback failed.']);
                            }
                        } else {
                            error_log('Prepare statement for update cashback failed.');
                            return json_encode(['status' => 'error', 'message' => 'Prepare statement for update cashback failed.']);
                        }
                    }
                }
            }

            $stmtCheckUser->close();
        }

        $stmtUpdateCashback->close();
        $stmtInvitedBy->close();
    } else {
        error_log('Prepare statement for invited_by failed.');
        return json_encode(['status' => 'error', 'message' => 'Prepare statement for invited_by failed.']);
    }

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
                            $sqlInsertPackage = "INSERT INTO Packages (username, name, lifetime, date) VALUES (?, ?, 0, CURDATE())";
                            $stmtInsertPackage = $conn->prepare($sqlInsertPackage);


                            if ($stmtInsertPackage) {
                                $stmtInsertPackage->bind_param("ss", $username, $selectedPackage);
                                $stmtInsertPackage->execute();

                                if ($stmtInsertPackage->affected_rows > 0) {
                                    // Return a JSON response for success
                                    return json_encode(['status' => 'success']);
                                } else {
                                    return json_encode(['status' => 'error', 'message' => 'Insert package failed.']);
                                }
                            } else {
                                return json_encode(['status' => 'error', 'message' => 'Prepare statement for insert package failed.']);
                            }
                        } else {
                            return json_encode(['status' => 'error', 'message' => 'Update balance failed.']);
                        }
                    } else {
                        return json_encode(['status' => 'error', 'message' => 'Prepare statement for update balance failed.']);
                    }
                } else {
                    return json_encode(['status' => 'error', 'message' => 'Insufficient balance.']);
                }
            }
        } else {
            return json_encode(['status' => 'error', 'message' => 'Query execution failed.']);
        }
    } else {
        return json_encode(['status' => 'error', 'message' => 'Prepare statement for user verification failed.']);
    }

    return json_encode(['status' => 'error', 'message' => 'Transaction failed']);
}

// Check if the user is logged in using the correct session variable
if (isset($_SESSION['user_name'])) {
    $username = $_SESSION['user_name'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['purchase_request']) && $_POST['purchase_request'] == true) {
            $selectedAmount = isset($_POST['selected_amount']) ? $_POST['selected_amount'] : null;
            $selectedPackage = isset($_POST['selected_package']) ? $_POST['selected_package'] : null;
            $selectedCashback = isset($_POST['selected_cashback']) ? $_POST['selected_cashback'] : null;

            $response = handleTransaction($conn, $username, $selectedAmount, $selectedPackage, $selectedCashback);

            // Return the JSON response
            header('Content-Type: application/json');
            echo $response;
            exit();
        }
    }
} else {
    $_SESSION['error_message'] = 'User not logged in.';
    header("Location: error_trans.php");
    exit();
}

ob_end_flush();

// Close the database connection
$conn->close();
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
  <!--Add TAILWING CDN  -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    <div class="page-content " id="content">

        <!-- Packages content -->
        <div class="section" id="package" style="margin-top: 30px;">
            <div class="container">
                <p style="font-weight: bold; line-height: 1.5;">Packages Available <br> Pricing</p>

                <hr class="mx-auto mb-5 w-100%" />
                <!-- row mb-5 -->
                <!-- Tailwind starts here -->
                <div class="w-full  grid grid-cols-1 md:grid-cols-3 gap-3 p-6 mx-auto mb-6">

                    <!-- Card starts here -->
                    <div class="h-[450px] px-4 py-2 min-w-[200px] mx-2">
                        <!-- Card starts here -->
                        <div class="card shadow w-full" id="gold-package">
                            <div class="w-full card-body" style="text-align: center;">
                                <form id="purchaseForm">
                                    <p class="card-img-top" style="font-size: 24px;">
                                        <br>
                                        <span style="font-size: 18px; color: #000;">STAR</span><br>
                                        <span style="font-size: 18px; color: #000;">KES</span><br>
                                        <span style="font-size: 45px; color: #000;">1,000</span><br>
                                        Lifetime
                                    </p>
                                    <hr class="mx-auto w-75" />
                                    <input type="hidden" name="selected_amount" value="1000">
                                    <input type="hidden" name="selected_package" value="Star">
                                    <input type="hidden" name="selected_cashback" value="700">
                                    <p>
                                        Cashback 700 <br> Refferal <br> Games <br> No Whatsapp Earning <br> Games <br> <br>
                                    </p>
                                    <button type="button" class="btn" style="background-color: #f36c33; color: #fff;"
                                        onmouseover="this.style.backgroundColor='white'; this.style.color='#f36c33';"
                                        onmouseout="this.style.backgroundColor='#f36c33'; this.style.color='#fff';" onclick="purchasePackage(this)">Purchase</button>
                                </form>
                            </div>
                        </div>
                        <!-- Card ends here -->
                    </div>
                    <!-- Card starts here -->
                    <div class="h-[450px] px-4 py-2 min-w-[200px] mx-2">
                        <!-- Card starts here -->
                        <div class="card shadow w-full" id="gold-package">
                            <div class="w-full card-body" style="text-align: center;">
                                <form id="purchaseForm">
                                        <p class="card-img-top" style="font-size: 24px;">
                                            
                                            <br>
                                            <span style="font-size: 18px; color: #000;">PLATINUM</span><br>
                                            <span style="font-size: 18px; color: #000;">KES</span><br>
                                            <span style="font-size: 45px; color: #000;">2,500</span><br>
                                            Lifetime
                                        </p>
                                        <hr class="mx-auto w-75" />
                                         <input type="hidden" name="selected_amount" value="2500">
                                        <input type="hidden" name="selected_package" value="Platinum">
                                        <input type="hidden" name="selected_cashback" value="2000">
                                        
                                        <p>
                                            Cashback 2,000 <br> Earning Method <br> Games <br> Refferal <br> 90 Per View <br>
                                            Games
                                        </p>
                                        <button class="btn" style="background-color: #f36c33; color: #fff;"
                                            onmouseover="this.style.backgroundColor='white'; this.style.color='#f36c33';"
                                            onmouseout="this.style.backgroundColor='#f36c33'; this.style.color='#fff';"onclick="purchasePackage(this)">Purchase</button>
                                </form>
                            </div>

                        </div>
                        <!-- Card ends here -->
                    </div>
                    <!-- Card ends here -->

                    <!-- Col ends here -->
                    <div class="h-[450px] px-4 py-2 min-w-[200px] mx-2">
                        <!-- Card starts here -->
                        <div class="card shadow w-full" id="gold-package">
                            <div class="w-full card-body" style="text-align: center;">
                                
                            <form id="purchaseForm">
                                <p class="card-img-top" style="font-size: 24px;">
                                    <br>
                                    <span style="font-size: 18px; color: #000;">DIAMOND</span><br>
                                    <span style="font-size: 18px; color: #000;">KES</span><br>
                                    <span style="font-size: 45px; color: #000;">4,800</span><br>
                                    Lifetime
                                </p>
                                <hr class="mx-auto w-75" />
                                 <input type="hidden" name="selected_amount" value="4800">
                                <input type="hidden" name="selected_package" value="Diamond">
                                <input type="hidden" name="selected_cashback" value="3600">
                                <p>
                                    Cashback 3,600 <br> Way of Earning <br> Games <br> Trading <br> Forex Classes <br>
                                    100 Per View
                                </p>
                                <button class="btn" style="background-color: #f36c33; color: #fff;"
                                    onmouseover="this.style.backgroundColor='white'; this.style.color='#f36c33';"
                                    onmouseout="this.style.backgroundColor='#f36c33'; this.style.color='#fff';"onclick="purchasePackage(this)">Purchase</button>
                            </form>
                            </div>

                        </div>
                        <!-- Card ends here -->
                    </div>
                    <!-- Col ends here -->
                </div>
            </div>

<div id="content-wrapp" style="background: #fff; padding: 10px;">
    <div class="show-entries">
        <span>Show Entries</span>
        <select id="show-entries">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>

    <div id="table-container">
        <table class="table" id="my-table">
            <thead>
                <tr>
                    <th class="line">Package</th>
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
        
        function purchasePackage(button) {
            // Get the selected amount, package, and cashback
            var selectedAmount = button.parentNode.querySelector('input[name="selected_amount"]').value;
            var selectedPackage = button.parentNode.querySelector('input[name="selected_package"]').value;
            var selectedCashback = button.parentNode.querySelector('input[name="selected_cashback"]').value;
        
            // Create an XMLHttpRequest object
            var xhr = new XMLHttpRequest();
        
            // Configure it to make a POST request to the same file
            xhr.open('POST', window.location.href, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
            // Define the data to be sent
            var data = 'selected_amount=' + selectedAmount +
                '&selected_package=' + selectedPackage +
                '&selected_cashback=' + selectedCashback +
                '&purchase_request=true'; // Add a flag to identify the purchase request
        
            // Define the callback function to handle the response
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Parse the JSON response
                    var response = JSON.parse(xhr.responseText);
        
                    // Check the status in the response
                    if (response.status === 'success') {
                        // Redirect or perform other actions based on the successful response
                        window.location.href = 'success_page.php';
                    } else {
                        // Handle the error case, display an alert for now
                        alert('Error: ' + response.message);
                    }
                } else {
                    console.error('Error:', xhr.statusText);
                }
            };
        
            // Send the request with the data
            xhr.send(data);
        }
        var dropdown = document.getElementsByClassName("dropdown-btn");
        var i;

        for (i = 0; i < dropdown.length; i++) {
            dropdown[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var dropdownContent = this.nextElementSibling;
                if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }
            });
        }
        
document.addEventListener("DOMContentLoaded", function() {
    const tableBody = document.getElementById("table-body");
    const showEntries = document.getElementById("show-entries");
    const pageNumber = document.getElementById("page-number");
    const previousButton = document.getElementById("previous-btn");
    const nextButton = document.getElementById("next-btn");

    let currentPage = 1;
    let entriesPerPage = 10;

    // Function to populate the table
    function populateTable(page) {
        const start = (page - 1) * entriesPerPage;
        const end = start + entriesPerPage;

        fetch("packagefetch.php") // Adjust the path if needed
            .then((response) => response.text())
            .then((html) => {
                tableBody.innerHTML = html;
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