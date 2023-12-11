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
        <!-- Toggle button -->

        <!-- Packages content -->

        <div class="section" id="submit" style="margin-top:30px; padding: 20px; margin-bottom: 30px; background:#fff;">
            <!-- Contact Section -->
            <section class="contact-section">
                <div class="container">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <form class="contact-form" action="" method="POST" id="notificationForm">
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea class="form-control" name="Message" id="message" rows="5" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="notificationType">Notification Type</label>
                                <select class="form-control" name="NotificationType" id="notificationType" required>
                                    <option value="notification">Notification</option>
                                    <option value="offer">Offer</option>
                                </select>
                            </div>
                            <button type="submit" name="send" class="btn btn btn-block" style="background-color: orange; color: white;">
                                Submit
                            </button>
                        </form>
                                        
                        <?php
                        
                        $servername = "localhost";
                        $username = "telmarka_db";
                        $password = "Benbrian@01";
                        $dbname = "telmarka_db";
                        
                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        
                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        
                        if (isset($_POST['send'])) {
                            $Message = $_POST['Message'];
                            $NotificationType = $_POST['NotificationType'];
                        
                            // Delete all previous entries of the specified notification_type
                            $deleteSql = "DELETE FROM Notifications WHERE notification_type = ?";
                            $deleteStmt = mysqli_prepare($conn, $deleteSql);
                            mysqli_stmt_bind_param($deleteStmt, "s", $NotificationType);
                            mysqli_stmt_execute($deleteStmt);
                        
                            // Insert the new record
                            $insertSql = "INSERT INTO Notifications (message, notification_type, timestamp) VALUES (?, ?, CURRENT_TIMESTAMP)";
                            $insertStmt = mysqli_prepare($conn, $insertSql);
                            mysqli_stmt_bind_param($insertStmt, "ss", $Message, $NotificationType);
                        
                            if (mysqli_stmt_execute($insertStmt)) {
                                echo '<script>
                                        alert("Notification inserted successfully!");
                                        window.location.href = "customer_admin.php";
                                      </script>';
                            } else {
                                echo '<script>
                                        alert("Error: Failed inserting notification. Try again later.");
                                        window.location.href = "customer_admin.php";
                                      </script>';
                            }
                        }
                        
                        ?>

                    </div>
                </div>

                    
                </div>
            </section>
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

        // Check if the form was submitted previously
        if (localStorage.getItem("volunteerSubmitted")) {
            // Display the thank you message
            document.addEventListener("DOMContentLoaded", function() {
                var messageContainer = document.querySelector(".thank-you-message");
                messageContainer.style.display = "block";

                // Clear the localStorage flag to prevent showing the message on subsequent reloads
                localStorage.removeItem("volunteerSubmitted");
            });
        }

        // Attach an event listener to the form submit button
        document.addEventListener("DOMContentLoaded", function() {
            var submitButton = document.querySelector('button[name="submit"]');
            submitButton.addEventListener("click", function() {
                // Set the localStorage flag when the form is submitted
                localStorage.setItem("volunteerSubmitted", "true");
            });
        });
        </script>
        <script src="doc.js"></script>
</body>

</html>