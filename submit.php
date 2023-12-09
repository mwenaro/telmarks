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
        <div class="section" id="submit" style="margin-top:70px; padding: 20px; margin-bottom: 30px; background:#fff;">
            <!-- Contact Section -->
            <section class="contact-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <form class="contact-form" action="screenshotsubmit.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="name">Phone Number</label>
                                    <input type="number" name="Phone_Number" class="form-control" id="Phone_Number"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Number of Pages</label>
                                    <input type="number" name="views" class="form-control" id="views" required max="200">
                                </div>
                                <div class="form-group">
                                    <label for="cover-letter">Upload Document</label>
                                    <div class="custom-file">
                                        <input type="file" name="cover" class="custom-file-input" id="cover-letter"
                                            required />
                                        <label class="custom-file-label" for="cover-letter">Choose file</label>
                                    </div>
                                </div>
                                <button type="submit" name="send" class="btn btn btn-block"
                                    style="background-color: orange; color: white;">
                                    Submit
                                </button>
                            </form>
                            <div class="success">
                            </div>
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
                tableBody.innerHTML = "";
                const start = (page - 1) * entriesPerPage;
                const end = start + entriesPerPage;

                // Make an AJAX request to fetch data from the PHP script
                fetch("table.php")
                    .then((response) => response.json())
                    .then((data) => {
                        for (let i = start; i < end && i < data.length; i++) {
                            const entry = data[i];
                            const row = document.createElement("tr");
                            row.innerHTML = `
                        <td class="line">${entry.phone_number}</td>
                        <td class="line">${entry.views}</td>
                        <td class="line">${entry.result}</td>
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