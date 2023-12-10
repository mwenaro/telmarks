<?php
ob_start();

// Include the config.php file that contains your database connection details
include("config.php");

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirect to the login page if the user is not logged in
    header("Location: index.php");
    exit();
}

// Retrieve the username from the session
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "";

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
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    
    <?php
// Start a session (if not started already)
session_start();

// Database connection details
$servername = "localhost";
$username = "telmarka_db";
$password = "Benbrian@01";
$dbname = "telmarka_db";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$profileImage = "";
$abbreviation = "";

// Check if the session variable is set and not empty
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $sessionUsername = $_SESSION['user_name'];

    // Retrieve profile image and username from Users table
    $sql = "SELECT profile_image FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sessionUsername);

    if ($stmt->execute()) {
        $stmt->bind_result($profileImage);
        $stmt->fetch();
    }

    $stmt->close();
    
    // Generate abbreviation from username
    $abbreviation = strtoupper(substr($sessionUsername, 0, 2));
}

// Default background image if profile image is not available
$bgImage = "img/1.jpeg";
?>
    <header>
        <div class="header-content">
            <div class="notify-icon" id="notificationIcon">
                <span class="las la-bell"></span>
                <span class="notify" id="notificationCount"></span>
            </div>
            
            <!-- Modal to display offer message -->
            <div id="offerModal" class="modal" style="display: none; text-align: center;">
                <?php if (!empty($notifications)): ?>
                    <div class="modal-dialog">
                        <div class="modal-content" style="
                            background-color: #f36c33;
                            color: white;
                            margin: 20px;
                            padding: 10px;
                            position: relative;
                        ">
                            <div class="modal-body">
                                <p><?php echo htmlspecialchars($notifications[0]['message']); ?></p>
                            </div>
                            <!-- Close icon -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeOfferModal()">Close</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="user" id="user">
                
                <!-- Display profile image in a circle -->
                <div class="bg-img" style="background-image: url(<?php echo empty($profileImage) ? $bgImage : 'none'; ?>); position: relative; hover{background-color:orange; cursor:pointer;}">
                    <img src="<?php echo empty($profileImage) ? 'default-image.jpg' : $profileImage; ?>" alt="Profile Image" style="width:40px; height:40px; border-radius: 50%;">
                    <?php if (empty($profileImage)): ?>
                        <div class="abbreviation" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"><?php echo $abbreviation; ?></div>
                    <?php endif; ?>
                </div>
                                
                <!-- Your other HTML content goes here -->
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
    <div class="page-content p-2" id="content" style="margin-top:20px;">
        <!-- Toggle button -->
        <div class="section" id="dashboard">
             <button id="sidebarCollapse" type="button" class="btn btn-light bg-white rounded-pill shadow-sm px-3 mb-3"
                            style="margin-top: 30px;">
                            <i class="fa fa-bars mr-2"></i>
                            <small class="text-uppercase font-weight-bold"></small>
            </button>


<div class="page-header">
    <h1 class="">Dashboard</h1>
    <small>Hello, <?php echo $userName; ?></small>
</div>

<?php
// Assume a connection to your database
$db = new PDO("mysql:host=localhost;dbname=telmarka_db", "telmarka_db", "Benbrian@01");

// Fetch notifications of type 'notification' from the database
$stmt = $db->prepare("SELECT * FROM Notifications WHERE notification_type = 'offer'");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header" id="announcement" style="
    text-align: center;
    background-color: #f36c33;
    color: white;
    margin: 0 20px;
    padding: 5px;
    position: relative;  /* Add position relative for absolute positioning */
">
    <?php foreach ($notifications as $notification): ?>
        <p>
            <?php echo htmlspecialchars($notification['message']); ?>
        </p>
    <?php endforeach; ?>

    <!-- Close icon -->
    <span style="
        position: absolute;
        top: 5px;
        right: 5px;
        cursor: pointer;
    " onclick="closeMessage()">✖</span>
</div>

<!-- tailwind csls -->
<div class="card-container flex flex-col md:flex-row justify-between items-center  mx-auto w-full" style="margin-right:10%; padding:10px">
    <div class="card-links w-full md:w-1/2 lg:w-1/4 h-24 bg-white rounded-lg flex justify-center items-center mb-4" style=" margin-right:5px;">
        <a href="./deposit.php">
            <div class="card-content">
                <h3 style="text-decoration: none; color: black; display: block; text-align: center; font-size: 15px;">Deposits</h3>
            </div>
        </a>
    </div>

    <div class="card-links w-full md:w-1/2 lg:w-1/4 h-24 bg-white rounded-lg flex justify-center items-center mb-4" style=" margin-right:5px;">
        <a href="./transfer.php">
            <div class="card-content">
                <h3 style="text-decoration: none; color: black; display: block; text-align: center; font-size: 15px;">Finances</h3>
            </div>
        </a>
    </div>

    <div class="card-links w-full md:w-1/2 lg:w-1/4 h-24 bg-white rounded-lg flex justify-center items-center mb-4" style=" margin-right:5px;">
        <a href="./package.php">
            <div class="card-content">
                <h3 style="text-decoration: none; color: black; display: block; text-align: center; font-size: 15px;">Packages</h3>
            </div>
        </a>
    </div>
    
    <div class="card-links w-full md:w-1/2 lg:w-1/4 h-24 bg-white rounded-lg flex justify-center items-center mb-4"  style=" margin-right:5px;">
        <a href="./writing.php">
            <div class="card-content">
                <h3 style="text-decoration: none; color: black; display: block; text-align: center; font-size: 15px;">Writing Packages</h3>
            </div>
        </a>
    </div>
</div>

<div id="error-message"></div>


<div style="background-color: #ebeff2; overflow: hidden;"
class="flex flex-col md:flex-row justify-center items-center p-20"
>

<!-- Card 1: Bonus -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; "  class=" h-20 w-full md:w-1/2 lg:w-1/4 justify-between items-center">
    <div style="" class ="flex flex-col justify-center items-center ">
    <h3 style="margin: 0; color: black;">Bonus</h3>
        <!-- App Earnings Section -->          
            
            
<?php
// Start the session
session_start();

// Initialize default values
$bonusAmount = 0;

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    $loggedInUser = $_SESSION['user_name'];

    // Database connection details (replace with your actual connection details)
    $dbHost = "localhost";
    $dbUser = "telmarka_db";
    $dbPassword = "Benbrian@01";
    $dbName = "telmarka_db";

    // Create a database connection using mysqli
    $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch bonus amounts for the specified user and sum them up
    $query = "SELECT bonus_amount FROM ReferralBonus WHERE username = '$loggedInUser'";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $bonusAmount += $row['bonus_amount'];
    }

    // Close the mysqli connection
    $conn->close();
}

// Display the div with the total bonus amount
echo '
<div style="" class ="flex justify-between">
    <p style="margin: 0;">KES ' . $bonusAmount . '</p> 
    <img src="./logos/icon.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
   
</div>
<div style="" class ="flex justify-between">
<p style="margin: 0;">15%</p> 
    <img src="./logos/bonus.png" alt="amount" style="width: 20px; height: 20px;">
    
    <p style="margin: 0;">15%</p> 
</div>

';
?>


            
      

    </div>
</div> 
<!-- //End of card 1  -->

<!-- Card 2: Withdrawn -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px; width:25%;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: center;">

        <!-- Withdrawal Earnings Section -->
        <?php
        // Assuming you have already started the session
        session_start();
        
        // Initialize default values
        $withdrawnAmount = 0;
        
        // Check if the user is logged in
        if (isset($_SESSION['user_name'])) {
            $loggedInUser = $_SESSION['user_name'];
        
            // Database connection details (replace with your actual connection details)
            $dbHost = "localhost";
            $dbUser = "telmarka_db";
            $dbPassword = "Benbrian@01";
            $dbName = "telmarka_db";
        
            // Create a database connection
            $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
        
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
        
            // Fetch the sum of 'points_to_withdraw' for the specified user
            $query = "SELECT SUM(points_to_withdraw) AS totalWithdrawn FROM WithdrawalRequests WHERE username = '$loggedInUser'";
            $result = $conn->query($query);
        
            if ($result->num_rows > 0) {
                $withdrawnData = $result->fetch_assoc();
                $withdrawnAmount = $withdrawnData['totalWithdrawn'];
            }
        
            // Close the database connection
            $conn->close();
        }
        
        // Display the div with the determined withdrawn amount
        echo '
        <div style="flex-grow: 1; text-align: center;">
            <!-- Withdrawn Status -->
            <h3 style="margin: 0; color: black;">Withdrawn</h3>
            <br>
            <div style="display: flex; flex-direction: column; align-items: start;">
                <p style="margin: 0;">KES ' . $withdrawnAmount . '</p> 
                <br>
                <p style="margin: 0;">75%</p> 
            </div>
        </div>';
        ?>



        <!-- Icon Section -->
        <div style="display: flex; align-items: end; flex-direction: column;">
            <br> <br> 
            <img src="./logos/withdrawn.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br>
            <img src="./logos/withdrawnicon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>

<!-- Card 3: Package -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer;" class=" flex md:flex-row justify-between items-center">
    <div style="" class="">

        <!-- App Earnings Section -->
        <?php
        // Assuming you have already started the session
        session_start();
        
        // Initialize default values
        $status = 'Inactive';
        $packageName = 'N/A';
        
        // Check if the user is logged in
        if (isset($_SESSION['user_name'])) {
            $loggedInUser = $_SESSION['user_name'];
        
            // Database connection details (replace with your actual connection details)
            $dbHost = "localhost";
            $dbUser = "telmarka_db";
            $dbPassword = "Benbrian@01";
            $dbName = "telmarka_db";
        
            // Create a database connection
            $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
        
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
        
            // Fetch the last entry in the 'name' column for the specified user
            $query = "SELECT name FROM Packages WHERE username = '$loggedInUser' ORDER BY id DESC LIMIT 1";
            $result = $conn->query($query);
        
            if ($result->num_rows > 0) {
                $lastPackage = $result->fetch_assoc();
                $packageName = $lastPackage['name'];
                $status = 'Active'; // Set to 'Active' since there is a package
            } else {
                // Handle the case where the user is not in the Packages table
                $status = 'Inactive';
            }
        
            // Close the database connection
            $conn->close();
        }
        
        // Display the div with the determined status and last package name
        echo '
        <div style="display: flex; align-items: flex-start; flex-grow: 1;">
            <!-- Package Status -->
            <div style="text-align: left;">
                <h3 class="text-center" style="margin: 0; color: black; ">Package</h3>
                <br>
                <div style="display: flex; flex-direction: column; align-items: start;">
                    <p style="margin: 0; font-size: 14px;">' . $status . ' | ' . $packageName . '</p> 
                    <br>
                    <p style="margin: 0;">25% Purchase</p> 
                </div>
            </div>
        </div>';
        ?>



        <!-- Icon Section -->
        <div style="display: flex; align-items: end; flex-direction: column;">
            <br> <br> 
            <img src="./logos/all.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br>
            <img src="./logos/allpurchase.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>



<!-- Card 4: App Earnings -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px; width:25%;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: center;">
<?php
// Start the session
session_start();

// Initialize default values
$totalEarnings = 0;

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    $loggedInUser = $_SESSION['user_name'];

    // Database connection details (replace with your actual connection details)
    $dbHost = "localhost";
    $dbUser = "telmarka_db";
    $dbPassword = "Benbrian@01";
    $dbName = "telmarka_db";

    // Create a database connection using mysqli
    $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch bonus amounts for the specified user and sum them up
    // Check the actual column name in your 'data' table
    $query = "SELECT result FROM data WHERE username = '$loggedInUser'";

    try {
        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there are rows in the result set
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Assuming the actual column name contains numeric values
                $totalEarnings += $row['result'];
            }
        }

        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close the mysqli connection
    $conn->close();
}
?>

<div style="flex-grow: 1; text-align: center;">
    <h3 style="margin: 0; color: black;">App Earnings</h3>
    <br>
    <div style="display: flex; flex-direction: column; align-items: start;">
        <p style="margin: 0;">KES <?php echo $totalEarnings; ?></p>
        <br>
        <p style="margin: 0;">90%</p>
    </div>
</div>


     <!-- Icon Section -->
        <div style="display: flex; align-items: end; flex-direction: column;">
            <br> <br>
            <img src="./logos/earnings.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br>
            <img src="./logos/affiliateprogramicon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>


</div>



<div style="display: flex;  align-items: center; padding: 20px; background-color: #ebeff2; overflow: hidden;">
    <!-- Card 5: Image Only -->
    <div style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; cursor: pointer; margin-right: 5px; width: 33.33%;">
        <img src="./logos/forexphoto.jpg" alt="Image 1" style="width: 100%; height: 250px; border-bottom: 1px solid #e0e0e0;">
    </div>

    <!-- Card 6: Image Only -->
    <div style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; cursor: pointer; margin-right: 5px; width: 33.33%;">
        <img src="./logos/piechartphoto.jpg" alt="Image 2" style="width: 100%; height: 250px; border-bottom: 1px solid #e0e0e0;">
    </div>

    <!-- Card 7: Image Only -->
    <div style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; cursor: pointer; margin-right: 5px; width: 33.33%;">
        <img src="./logos/stats.jpg" alt="Image 3" style="width: 100%; height: 250px; border-bottom: 1px solid #e0e0e0;">
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background-color: #ebeff2; overflow: hidden;">
<!-- Card 1: Deposit Balance -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: left;">
        <!-- Deposit Section -->
        <div style="display: flex; align-items: flex-start; flex-grow: 1; width:25%;">
            <!-- Deposit Balance -->
            <div style="text-align: left;">
                <h3 style="margin: 0; color: black;">Writting Profit</h3>
                <br>
                <!-- Amount in Kes from Database -->
                <p style="margin: 0;">35%</p>
            </div>
        </div>


        <!-- Amount Section -->
        <!-- Align items vertically inline -->
        <div style="display: flex; align-items: flex-start; flex-direction: column;">
            <!-- Icon for Amount -->
            <img src="./logos/profittransferadvertisewithusdepositbalanceicon.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br> <br>
            <!-- Amount in Kes from Database -->
            <img src="./logos/profiticon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>

<!-- Card 2: Deposit Balance -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px; width:25%;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: left;">
        <!-- Deposit Section -->
        <div style="display: flex; align-items: flex-start; flex-grow: 1;">
            <!-- Deposit Balance -->
<?php
// Start session
session_start();

// Database connection
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

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    echo '<div style="text-align: left;"><h3 style="margin: 0; color: black;">Transfer &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</h3><br><p style="margin: 0;"> N/A</p></div>';
} else {
    // Get the logged-in user's name
    $loggedInUsername = $_SESSION['user_name'];

    // Query to get the total amount from Transfers table for the logged-in user as the sender
    $totalTransfersQuery = "SELECT SUM(amount) AS total_amount FROM Transfers WHERE sender_username = ?";
    $stmtTotalTransfers = $conn->prepare($totalTransfersQuery);
    $stmtTotalTransfers->bind_param("s", $loggedInUsername);
    $stmtTotalTransfers->execute();
    $stmtTotalTransfers->bind_result($totalAmount);
    $stmtTotalTransfers->fetch();
    $stmtTotalTransfers->close();

    // Display the total amount in Kes or N/A if no transfers found
    echo '<div style="text-align: left;"><h3 style="margin: 0; color: black;">Transfer &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</h3><br>';
    echo '<p style="margin: 0;">Amount Kes ' . ($totalAmount !== null ? $totalAmount : 'N/A') . '</p>';
    echo '</div>';
}

// Close the database connection
$conn->close();
?>

        </div>


        <!-- Amount Section -->
        <!-- Align items vertically inline -->
        <div style="display: flex; align-items: flex-start; flex-direction: column;">
            <!-- Icon for Amount -->
            <img src="./logos/profittransferadvertisewithusdepositbalanceicon.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br> <br>
            <!-- Amount in Kes from Database -->
            <img src="./logos/transfericon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>

<!-- Card 3: Deposit Balance -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px; width:25%;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: left;">
        <!-- Deposit Section -->
        <?php
        // Assuming you have already started the session
        session_start();
        
        // Check if the user is logged in
        if (isset($_SESSION['user_name'])) {
            $loggedInUser = $_SESSION['user_name'];
        
            // Database connection details (replace with your actual connection details)
            $dbHost = "localhost";
            $dbUser = "telmarka_db";
            $dbPassword = "Benbrian@01";
            $dbName = "telmarka_db";
        
            // Create a database connection
            $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
        
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
        
            // Fetch 'name' column values from the Packages table
            $query = "SELECT name FROM Packages WHERE username = '$loggedInUser'";
            $result = $conn->query($query);
        
            if ($result->num_rows > 0) {
                // 'name' column values found in the Packages table
                $nameValues = [];
        
                while ($row = $result->fetch_assoc()) {
                    $nameValues[] = $row['name'];
                }
        
                // Check if 'Advertise' is present in any 'name' column value
                $status = in_array('Advertise', $nameValues) ? 'Active' : 'Inactive';
            } else {
                // Handle the case where the user is not in the Packages table
                $status = 'Inactive';
            }
        
            // Close the database connection
            $conn->close();
        } else {
            // Handle the case where the user is not logged in
            $status = 'Log In';
        }
        
        // Display the div with the determined status
        echo '
        <div style="display: flex; align-items: flex-start; flex-grow: 1;">
            <!-- Advertise Status -->
            <div style="text-align: left;">
                <h3 style="margin: 0; color: black;">Advertise with us</h3>
                <br>
                <!-- Display the status -->
                <p style="margin: 0;">' . $status . '</p>
            </div>
        </div>';
        ?>





        <!-- Amount Section -->
        <!-- Align items vertically inline -->
        <div style="display: flex; align-items: flex-start; flex-direction: column;">
            <!-- Icon for Amount -->
            <img src="./logos/profittransferadvertisewithusdepositbalanceicon.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br> <br>
            <!-- Amount in Kes from Database -->
            <img src="./logos/advertisewithusboxicon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>

<!-- Card 4: Deposit Balance -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px; width:25%;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: left;">
        <!-- Deposit Section -->
        <div style="display: flex; align-items: flex-start; flex-grow: 1;">
            <!-- Deposit Balance -->
<?php
// Start session
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

$db_host = "localhost";
$db_user = "telmarka_db";
$db_password = "Benbrian@01";
$db_name = "telmarka_db";

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "";

// Check for database connection errors
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    die("Error: Failed to connect to the database");
}



// Function to update deposit balance for the logged-in user
function updateDepositBalance($conn, $username)
{
    // Use prepared statements to prevent SQL injection
    $sql = "SELECT amount FROM Deposits WHERE username = ? ORDER BY deposit_id DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['deposit_balance'] = $row['amount'];

            } else {
                $_SESSION['deposit_balance'] = 0;
            }
        } else {
            echo "Error in SQL query: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error in preparing SQL statement: " . mysqli_error($conn);
    }
}

// Verify that the session username matches the username in the "Deposits" table
$verifyUserSql = "SELECT * FROM Deposits WHERE username = ? ORDER BY deposit_id DESC LIMIT 1";
$verifyStmt = mysqli_prepare($conn, $verifyUserSql);

if ($verifyStmt) {
    mysqli_stmt_bind_param($verifyStmt, "s", $user_name);
    mysqli_stmt_execute($verifyStmt);
    $verifyResult = mysqli_stmt_get_result($verifyStmt);

    if ($verifyResult) {
        $verifyRow = mysqli_fetch_assoc($verifyResult);


        if (!empty($verifyRow)) {
            // The session username is valid, update deposit balance
            updateDepositBalance($conn, $user_name);

        } else {
            
        }
    } else {
        echo "Error in verification SQL query: " . mysqli_error($conn);
    }

    mysqli_stmt_close($verifyStmt);
} else {
    echo "Error in preparing verification SQL statement: " . mysqli_error($conn);
}
?>

<div style="text-align: left;">
    <h3 style="margin: 0; color: black;">Deposit balance</h3>
    <br>
    <!-- Amount in Kes from Database -->
    <p style="margin: 0;">Amount Kes <?php echo isset($_SESSION['deposit_balance']) ? $_SESSION['deposit_balance'] : 'N/A'; ?></p>
</div>

<?php
ob_end_flush();
?>



        </div>


        <!-- Amount Section -->
        <!-- Align items vertically inline -->
        <div style="display: flex; align-items: flex-start; flex-direction: column;">
            <!-- Icon for Amount -->
            <img src="./logos/profittransferadvertisewithusdepositbalanceicon.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br> <br>
            <!-- Amount in Kes from Database -->
            <img src="./logos/depositbalanceicon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>





</div>


<?php

$db_host = "localhost";
$db_user = "telmarka_db";
$db_password = "Benbrian@01";
$db_name = "telmarka_db";

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "";

$myUsername = "";
$invitedBy = "";
$referralLink = "";

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

function getUserInformation($conn, $username)
{
    $stmt = $conn->prepare("SELECT username, inviter_id FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row;
    }

    return false;
}

function displayUserInformation($conn, $username)
{
    $userInfo = getUserInformation($conn, $username);

    if ($userInfo) {
        $myUsername = $userInfo['username'];
        $inviter_id = $userInfo['inviter_id'];

        $invitedBy = ($inviter_id === null) ? "Not Invited" : getInvitedByUsername($conn, $myUsername);

        $referralLink = generateReferralLink($myUsername);

        echo '<div id="content-wrapp" style="background: #fff; padding: 40px; margin: 0 10%; width: 80%;">
                <h1 class="text-center">User Information</h1>

                <form class="form-inline" id="my-form">
                    <div class="form-group" style="width: 100%;">
                        <label for="my-username" style="font-weight: bold;">My Username:</label>
                        <input type="text" class="form-control" id="my-username" name="my-username" style="width: 100%;" value="' . $myUsername . '" readonly>
                    </div>
                    <div class="form-group" style="width: 100%;">
                        <label for="invited" style="font-weight: bold;">Invited By:</label>
                        <input type="text" class="form-control" id="invited" name="invited" style="width: 100%;" value="' . $invitedBy . '" readonly>
                    </div>
                    <div class="form-group" style="width: 100%;">
                        <label for="my-referral" style="font-weight: bold;">My Referrals:</label>
                        <input type="text" class="form-control" id="my-referral" name="my-referral" style="width: 100%;" value="' . $referralLink . '" readonly>
                    </div>
                </form>
            </div>';

    } else {
        echo "User not found.";
    }
}

function getInvitedByUsername($conn, $username)
{
    $stmt = $conn->prepare("SELECT invited_by FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row['invited_by'];
    }

    return "Not Available";
}

function generateReferralLink($username)
{
    $referralLink = "https://telmarkagencies.com/Dashboard/index.php?ref=$username";
    return $referralLink;
}

displayUserInformation($conn, $user_name);

?>


<div style="display: flex; margin: 0 8%; align-items: center; padding: 20px; background-color: #ebeff2; overflow: hidden;">
<!-- Card 1: Deposit Balance -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px; width:33.3%;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: left;">
        <!-- Deposit Section -->
        <div style="display: flex; align-items: flex-start; flex-grow: 1;">
            <!-- Deposit Balance -->
            <div style="text-align: left;">
                <h3 style="margin: 0; color: black;">Spinning Earnings</h3>
                <!-- Amount in Kes from Database -->
                <p style="margin: 0;">KES 0</p>
            </div>
        </div>


        <!-- Amount Section -->
        <!-- Align items vertically inline -->
        <div style="display: flex; align-items: flex-start; flex-direction: column;">
            <br>
            <!-- Icon for Amount -->
            <img src="./logos/agentverificationicon.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br>
            <!-- Amount in Kes from Database -->
            <img src="./logos/profiticon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>

<!-- Card 2: Deposit Balance -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px; width:33.3%;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: left;">
        <!-- Deposit Section -->
        <div style="display: flex; align-items: flex-start; flex-grow: 1;">
            <!-- Deposit Balance -->
            <div style="text-align: left;">
                <h3 style="margin: 0; color: black;">Game Earnings</h3>
                <br>
                <!-- Amount in Kes from Database -->
                <p style="margin: 0;">KES 0</p>
            </div>
        </div>


        <!-- Amount Section -->
        <!-- Align items vertically inline -->
        <div style="display: flex; align-items: flex-start; flex-direction: column;">
            <br>
            <!-- Icon for Amount -->
            <img src="./logos/profittransferadvertisewithusdepositbalanceicon.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br>
            <!-- Amount in Kes from Database -->
            <img src="./logos/transfericon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>

<!-- Card 3: Deposit Balance -->
<div style="background-color: #ffffff; border: 1px solid #e0e0e0; overflow: hidden; cursor: pointer; margin-right: 5px; width:33.3%;">
    <div style="padding: 15px; display: flex; align-items: center; text-align: left;">
        <!-- Deposit Section -->
        <div style="display: flex; align-items: flex-start; flex-grow: 1;">
            <!-- Deposit Balance -->
            <div style="text-align: left;">
                <h3 style="margin: 0; color: black;">Social Earning</h3>
                <br>
                <!-- Amount in Kes from Database -->
                <p style="margin: 0;">KES 0</p>
            </div>
        </div>


        <!-- Amount Section -->
        <!-- Align items vertically inline -->
        <div style="display: flex; align-items: flex-start; flex-direction: column;">
            <br>
            <!-- Icon for Amount -->
            <img src="./logos/profittransferadvertisewithusdepositbalanceicon.png" alt="amount" style="width: 20px; height: 20px; margin-bottom: 5px;">
            <br> 
            <!-- Amount in Kes from Database -->
            <img src="./logos/advertisewithusboxicon.png" alt="amount" style="width: 20px; height: 20px;">
        </div>

    </div>
</div>



        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
        </script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
        </script>
            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
        
$(document).ready(function () {
    // Function to check notifications on page load
    checkNotifications();

    // Function to check notifications on icon click
    $('#notificationIcon').on('click', function () {
        checkNotifications();
    });

    // Function to close the offer modal
    function closeOfferModal() {
        $('#offerModal').hide();
    }

    // Function to check notifications and update the count
    function checkNotifications() {
        // You can use AJAX to fetch data from the server
        // Replace this with your actual endpoint
        $.ajax({
            url: 'notifications.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {

                // Display the message directly
                var offerMessage = data.message;

                // Update the notification count based on whether there is a message
                var notificationCount = offerMessage.trim() !== '' ? 1 : 0;

                // Update the notification count in the icon
                $('#notificationCount').text(notificationCount);

                // Display the offer message in the modal
                $('#offerMessage').text(offerMessage);

                // Open the modal if there is a message
                if (notificationCount > 0) {
                    $('#offerModal').show();
                }
            },
            error: function (error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }
});

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


    function closeMessage() {
        // Hide the entire announcement div by setting its display property to none
        document.getElementById('announcement').style.display = 'none';
    }
        </script>
        <script src="doc.js"></script>
</body>

</html>