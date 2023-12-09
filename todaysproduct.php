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

<div class="section" id="submit" style="margin-top: 30px; padding: 20px; margin-bottom: 30px; background: #f0f0f0; text-align: center;">

    <div class="container">
        <div class="row">

<?php
// Assume a connection to your database
$db = new PDO("mysql:host=localhost;dbname=telmarka_db", "telmarka_db", "Benbrian@01");

// Fetch the filename from the uploaded_files table
$stmt = $db->prepare("SELECT filename FROM uploaded_files LIMIT 1");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Set the default image path in case filename is not found
$imagePath = 'https://telmarkagencies.com/Dashboard/today/product1.jpg';

// Check if filename is available in the database result
if (!empty($result['filename'])) {
    // Construct the full path to the image in the "Dashboard/today" folder
    $imagePath = 'https://telmarkagencies.com/Dashboard/today/' . $result['filename'];
}
?>

<!-- HTML Document -->
<div class="col-lg-6">
    <section class="download-container">
        <div class="download-section">
            <!-- Image container with download button -->
            <div id="imageContainer" style="margin-bottom: 30px; background: #fff; text-align: center;">
                <img src="<?php echo $imagePath; ?>" alt="Download Image" style="max-width: 100%; height: 500px; margin: 0 auto;">
                <br style="clear: both;" />
                <!-- Download button below the image -->
                <a id="downloadButton" href="<?php echo $imagePath; ?>" download="<?php echo $result['filename']; ?>" class="btn btn-block" style="background-color: orange; color: white; text-decoration: none; display: block; margin-top: 10px;">
                    Download
                </a>
            </div>
        </div>
    </section>
</div>




            <!-- Second section with the same structure -->
<?php
// Assume a connection to your database
$db = new PDO("mysql:host=localhost;dbname=telmarka_db", "telmarka_db", "Benbrian@01");

// Fetch the second filename from the uploaded_files table
$stmtSecond = $db->prepare("SELECT filename FROM uploaded_files LIMIT 1 OFFSET 1");
$stmtSecond->execute();
$resultSecond = $stmtSecond->fetch(PDO::FETCH_ASSOC);

// Set the default second image path in case filename is not found
$secondImagePath = 'https://telmarkagencies.com/Dashboard/today/product2.jpg';

// Check if the second filename is available in the database result
if (!empty($resultSecond['filename'])) {
    // Construct the full path to the second image
    $secondImagePath = 'https://telmarkagencies.com/Dashboard/today/' . $resultSecond['filename'];
}
?>

<!-- HTML Document -->
<div class="col-lg-6">
    <section class="download-container">
        <div class="download-section">
            <!-- Image container with download button for the second image -->
            <div style="margin-bottom: 30px; background: #fff; text-align: center;">
                <img src="<?php echo $secondImagePath; ?>" alt="Download Second Image" style="max-width: 100%; height: 500px; margin: 0 auto;">
                <br style="clear: both;" />
                <!-- Download button below the second image -->
                <a href="<?php echo $secondImagePath; ?>" download="<?php echo $resultSecond['filename']; ?>" class="btn btn-block" style="background-color: orange; color: white; text-decoration: none; display: block; margin-top: 10px;">
                    Download
                </a>
            </div>
        </div>
    </section>
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


        // Function to update the image and download link
    function updateImageAndDownloadLink() {
        $.ajax({
            url: 'fetchLatestImage.php', // Create a separate PHP file for fetching the latest image
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.filename) {
                    // Update image source
                    $('#imageContainer img').attr('src', 'https://telmarkagencies.com/Dashboard/today/' + data.filename);

                    // Update download link
                    $('#downloadButton').attr('href', 'https://telmarkagencies.com/Dashboard/today/' + data.filename);
                    $('#downloadButton').attr('download', data.filename);
                }
            }
        });
    }

    // Set the interval for updating the image and download link (e.g., every 5 seconds)
    setInterval(updateImageAndDownloadLink, 5000); // 5000 milliseconds = 5 seconds

        </script>
        <script src="doc.js"></script>
</body>

</html>