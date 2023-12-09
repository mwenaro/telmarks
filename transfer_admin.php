<?php
// Your database connection parameters
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Start the session at the very beginning
session_start();

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all usernames from the "Users" table
$userQuery = "SELECT username FROM Users";
$userResult = $conn->query($userQuery);

// Check if a user is logged in
if (!isset($_SESSION['user_name'])) {
    // Redirect to login page or handle the case when no user is logged in
    header("Location: index.php");
    exit();
}

// Initialize the form token if not set
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

// Define a variable for redirection
$redirectLocation = "transfer_admin.php";

// Fetch data from the "Deposits" table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transfer'])) {
    // Handle form submission

    // Check if the form token is set and valid
    if (isset($_SESSION['form_token']) && isset($_POST['form_token']) && $_SESSION['form_token'] === $_POST['form_token']) {

        $loggedInUser = $_SESSION['user_name'];
        $selectedUser = $_POST['selectedUser'];
        $transferAmount = $_POST['transferAmount'];

        // Log inputs
        error_log("Logged In User: " . var_export($loggedInUser, true));
        error_log("Selected User: " . var_export($selectedUser, true));
        error_log("Transfer Amount: " . var_export($transferAmount, true));

        // Check if the selected user exists in the "Deposits" table
        $checkUserQuery = "SELECT username, amount FROM Deposits WHERE username = '$selectedUser'";
        $userResult = $conn->query($checkUserQuery);

        if ($userResult->num_rows > 0) {
            // User exists, fetch the current amount
            $userData = $userResult->fetch_assoc();
            $currentAmount = $userData['amount'];

            // Update the deposit account for the selected user
            $updateQuery = "UPDATE Deposits SET amount = $currentAmount + $transferAmount WHERE username = '$selectedUser'";
            $updateResult = $conn->query($updateQuery);

            // Update the deposit account for the logged-in user
            $updateLoggedInUserQuery = "UPDATE Deposits SET amount = amount - $transferAmount WHERE username = '$loggedInUser'";
            $updateLoggedInUserResult = $conn->query($updateLoggedInUserQuery);

            // Additional validation and error handling can be added here

            // Regenerate form token to avoid resubmission
            $_SESSION['form_token'] = bin2hex(random_bytes(32));

            // Display success message using JavaScript pop-up
            echo '<script>alert("Transfer successful!"); window.location.href = "' . $redirectLocation . '";</script>';
            exit();
        } else {
            // User does not exist in the "Deposits" table
            // Insert the user into the "Deposits" table
            $insertUserQuery = "INSERT INTO Deposits (username, amount) VALUES ('$selectedUser', $transferAmount)";
            $insertUserResult = $conn->query($insertUserQuery);

            if ($insertUserResult) {
                // User inserted successfully, now update the deposit accounts
                $updateLoggedInUserQuery = "UPDATE Deposits SET amount = amount - $transferAmount WHERE username = '$loggedInUser'";
                $updateLoggedInUserResult = $conn->query($updateLoggedInUserQuery);

                // Additional validation and error handling can be added here

                // Regenerate form token to avoid resubmission
                $_SESSION['form_token'] = bin2hex(random_bytes(32));

                // Display success message using JavaScript pop-up
                echo '<script>alert("Transfer successful!"); window.location.href = "' . $redirectLocation . '";</script>';
                exit();
            } else {
                // Error inserting user into "Deposits" table
                // Display error message using JavaScript pop-up
                echo '<script>alert("Error inserting user. Please try again.");</script>';
            }
        }
    } else {
        // Handle form token mismatch
        echo '<script>alert("Form token mismatch. Please refresh the page and try again.");</script>';
    }
}

// Fetch data from the "Transfers" table
$sql = "SELECT transfer_id, sender_username, recipient_username, amount, transaction_date 
        FROM Transfers 
        ORDER BY transaction_date DESC";

$result = $conn->query($sql);

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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
    <!-- Include the Select2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="side.css" />
</head>

<body>
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
                    <a href="./admin.php" class="nav-link text-dark">
                        <img src="./logos/dashboard icon.png" alt="Package" class="icon-image" />
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./package_admin.php" class="nav-link text-dark">
                        <img src="./logos/package icon.png" alt="Package" class="icon-image" />
                        Package
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./todays_admin.php" class="nav-link text-dark">
                        <img src="./logos/package icon.png" alt="Package" class="icon-image" />
                        Todays Product
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./advertise_admin.php" class="nav-link text-dark">
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
                    <a href="./writing_admin.php" class="nav-link text-dark">
                        <img src="./logos/transfericon.png" alt="Package" class="icon-image" />
                        Writing Package
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./submit_admin.php" class="nav-link text-dark">
                        <img src="./logos/profiticon.png" alt="Package" class="icon-image" />
                        Submit Writing
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./deposit_admin.php" class="nav-link text-dark">
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
                        <a class="dropdown-item" href="./transfer_admin.php">
                            Transfers
                        </a>
                        <a class="dropdown-item" href="./withdraw_admin.php">
                            Withdrawals
                        </a>
                    </div>
                </li>


                <li class="nav-item">
                    <a href="./gold_admin.php" class="nav-link text-dark">
                        <img src="./logos/gold membership icon.png" alt="Package" class="icon-image" />
                        Gold Membership
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./trading_admin.php" class="nav-link text-dark">
                        <img src="./logos/trading bot.png" alt="Package" class="icon-image" />
                        Trading bot
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./screenshot_admin.php" class="nav-link text-dark">
                        <img src="./logos/agentauthorization.png" alt="Package" class="icon-image" />
                        Submit Screenshot
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./whatsapp_admin.php" class="nav-link text-dark">
                        <img src="./logos/whatsapp withdrawals icon.png" alt="Package" class="icon-image" />
                        Whatsapp Withdrawals
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./authorization_admin.php" class="nav-link text-dark">
                        <img src="./logos/agentauthorization.png" alt="Package" class="icon-image" />
                        Agent Authorization
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./verification_admin.php" class="nav-link text-dark">
                        <img src="./logos/agentverificationicon.png" alt="Package" class="icon-image" />
                        Agent Verification
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./customer_admin.php" class="nav-link text-dark">
                        <img src="./logos/customer care icon.png" alt="Package" class="icon-image" />
                        Customer Care
                    </a>
                <li class="nav-item">
                     <a href="./logout.php" class="nav-link text-dark">
                        <img src="./logos/logout.png" alt="Package" class="icon-image" />
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- End vertical navbar -->

    <!-- Page content holder -->
    <div class="page-content p-5" id="content">
<section class="volunteer-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-3">
                <form class="volunteer-form" enctype="multipart/form-data" method="post" action="#" id="transferForm">
                    <!-- Container for styling -->
                    <div style="margin-bottom: 30px; background: #fff; padding: 10px; text-align: center;">

                        <!-- Make a Transfer in bold at one end -->
                        <p style="color: black; font-weight: bold; margin: 0; float: left;">Make a Transfer</p>

                        <!-- Line break -->
                        <br style="clear: both;" />
                        <br style="clear: both;" />
                        

                        <!-- Transaction charges and account balance at the other end -->
                        <p style="color: #888; margin: 0; float: right;">
                            Max Transfer Amount: KES <?php echo isset($accountBalance) ? $accountBalance : ''; ?>
                        </p>
                        
                        <br style="clear: both;" />
                        <br style="clear: both;" />

                        <!-- New input field for recipient username -->
                        <div class="form-group">
                            <label for="selectedUser" style="color: black; margin: 0; float: left;">Select User:</label>
                            <!-- Add a class to identify the select element -->
                            <select name="selectedUser" id="selectedUser" class="form-control user-select" required>
                                <?php
                                // Replace this with your server-side logic to fetch user data and populate options
                                while ($userRow = $userResult->fetch_assoc()) {
                                    echo "<option value='" . $userRow['username'] . "'>" . $userRow['username'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <br>

                        <div class="form-group">
                       <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
                        <!-- New input field for transfer amount -->
                        <div class="form-group">
                            <label for="transferAmount" style="color: black; margin: 0; float: left;">Amount (KES)</label>
                            <input type="number" name="transferAmount" class="form-control" id="transferAmount" required />
                        </div>

                        <!-- Submit button for the transfer -->
                        <button type="submit" name="transfer" value="Transfer" class="btn btn btn-block" style="background-color: orange; color: white;">
                            Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>



            <div class="container">
                <p style="font-weight: bold; line-height: 1.5;" class="text-center">TRANSFER DATA</p>

                <hr class="mx-auto mb-5 w-100%" />
<div id="content-wrapp" style="background: #fff; padding: 40px;">
    <div id="table-container">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search by username or phone number" id="search-input">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="search-btn">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </div>
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
                    <th class="line">Transfer ID</th>
                    <th class="line">Sender Username</th>
                    <th class="line">Recipient Username</th>
                    <th class="line">Amount Sent</th>
                    <th class="line">Date Sent</th>
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
    const searchInput = document.getElementById("search-input");
    const searchButton = document.getElementById("search-btn");

    let currentPage = 1;

    // Function to populate the table
    function populateTable(page, search = '') {
        const start = (page - 1) * showEntries.value;

        fetch(`tran.php?page=${page}&search=${search}&entries=${showEntries.value}`)
            .then((response) => response.text())
            .then((html) => {
                tableBody.innerHTML = html;
            })
            .catch((error) => {
                console.error('Error fetching data:', error);
            });
    }

    // Function to handle next and previous buttons
    function updateTable() {
        populateTable(currentPage, searchInput.value);
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
        currentPage = 1;
        updateTable();
    });

    // Event listener for search button
    searchButton.addEventListener("click", () => {
        currentPage = 1;
        updateTable();
    });
});


        
        document.getElementById('transferButton').addEventListener('click', function () {
    // Get form data
    var selectedUser = document.getElementById('selectedUser').value;
    var transferAmount = document.getElementById('transferAmount').value;
    var formToken = document.querySelector('[name="form_token"]').value;

    // Create a FormData object to send the data
    var formData = new FormData();
    formData.append('selectedUser', selectedUser);
    formData.append('transferAmount', transferAmount);
    formData.append('form_token', formToken);

    // Create and configure an XMLHttpRequest object
    var xhr = new XMLHttpRequest();
    xhr.open('POST', './trans.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Define the callback function to handle the response
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Handle the response, if needed
            console.log(xhr.responseText);
        }
    };

    // Send the request with the form data
    xhr.send(formData);
});

        
    $(document).ready(function() {
        // Initialize Select2 with a single input for searching and selection
        $('.user-select').select2({
            placeholder: 'Type to search for a user',
            allowClear: true,
            minimumInputLength: 1, // Set the minimum length of the input before a search is performed
            ajax: {
                // Specify the URL to fetch user data dynamically
                url: 'fetchUser.php',
                dataType: 'json',
                delay: 250, // Set a delay before the search is performed
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    });


        </script>
        <script src="doc.js"></script>
</body>

</html>