<?php
ob_start();

// Include the config.php file that contains your database connection details
include("config.php");
include('user_details_modal.php');

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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-50oBUHEmvpQ+1lH3sCq3nfUz7wJ5bF/8Q+i6L0Zl27ZvruRvCBt9uHPa0I3LIuN/Dmi5lFqjnZ7lFxiSgXZBf8g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="side.css" />
    <style>
        .card-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #ebeff2; /* Grey background */
            overflow: hidden;
        }

        .card-links {
            background-color: #ffffff; /* White background */
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
            cursor: pointer;
            padding:20px;
            color:#000;

        .card-links:hover {
            transform: scale(1.05);
        }

        .card-links-content {
            padding: 15px;
            text-align: center;
        }
        
        .card-links-content h3 {
            margin: 0; /* Remove default margin for h3 */
            color: black; /* Change text color to black */
        }

        .card-links a {
            text-decoration: none;
            color: #000;
        }
        
        .bg-img {
            width: 150px; /* Set your desired width */
            height: 150px; /* Set your desired height */
            border-radius: 50%; /* Make it round */
            overflow: hidden;
            margin: 0 auto; /* Center the container */
        }

        /* Style for the profile image */
        .bg-img img {
            width:20px;
            height:20px;
            width: 100%; /* Make the image fill the container */
            border-radius: 50%; /* Make it round */
            border: 2px solid #fff; /* Optional: Add a white border */
        }
        
        /* Style for the abbreviation */
        .abbreviation {
            font-size: 24px; /* Set your desired font size */
            font-weight: bold;
            color: #fff; /* Set your desired text color */
        }
        
        
        /* Default styles for larger screens */
.vertical-nav .nav-link {
    display: flex;
    align-items: center;
}

/* Styles for small screens (mobile) */
@media (max-width: 768px) {
    .vertical-nav .nav-link {
        display: flex;
        align-items: center;
        text-align: center;
    }

    .vertical-nav .nav-link img {
        margin-right: 0; /* Remove margin for the image */
        margin-bottom: 5px; /* Add some spacing between images */
    }

    .vertical-nav p {
        display: none; /* Hide the paragraphs in mobile view */
    }
}

/* Custom CSS for Card-Links */
.card-links {
    margin-bottom: 20px; /* Adjust the margin as needed */
}

/* Mobile responsiveness */
@media (max-width: 767px) {
    .card-links {
        width: 100%;
        margin-right: 0;
    }
}

/* Clear Bootstrap card deck margin on mobile */
@media (max-width: 576px) {
    .card-deck {
        margin-right: 0;
    }
}


    </style>
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
    <div class="page-content p-2" id="content" style="margin-top:20px;">
        <!-- Toggle button -->
        <div class="section" id="dashboard">
             <button id="sidebarCollapse" type="button" class="btn btn-light bg-white rounded-pill shadow-sm px-3 mb-3"
                            style="margin-top: 30px;">
                            <i class="fa fa-bars mr-2"></i>
                            <small class="text-uppercase font-weight-bold"></small>
            </button>
            
        </div>
        


<div class="page-header">
    <h1>Admin Dashboard</h1>
    <small>Hello, <?php echo $userName; ?></small>
</div>


<div id="error-message"></div>


<div style="padding: 20px; background-color: #ebeff2; overflow: hidden;">

    <div class="container" id='container'>
        <p style="font-weight: bold; line-height: 1.5; font-size: 1.5rem;" class="text-center text-md-left">USER DETAILS</p>

        <hr class="mx-auto mb-5 w-100%" />

        <div id="content-wrapp" style="background: #fff; padding: 40px;">
            <div id="table-container">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search by username" id="search-input">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="search-btn">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="show-entries">
                    <span>Show Entries</span>
                    <select id="show-entries" class="form-control">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <table class="table" id="my-table">
                    <thead>
                        <tr>
                            <th class="line">ID</th>
                            <th class="line">Email</th>
                            <th class="line"><a href="#" id="sort-username">Username</a></th>
                            <th class="line">Phone Number</th>
                            <th class="line">Registration Date</th>
                            <th class="line">Invited By</th>
                            <th class="line">Status</th>
                            <th class="line">Actions</th>
                        </tr>
                    </thead>
                    <!-- Inside your table body -->
                    <tbody id="table-body">
                        <!-- Data from the database will be inserted here -->
                        <tr>
                            <td contenteditable="true">${row.id}</td>
                            <td contenteditable="true">${row.email}</td>
                            <td contenteditable="true">
                                <!-- Add an onclick attribute to call the openUserDetails function with the username -->
                                <a href="#" class="username-link" data-username="${row.username}" onclick="openUserDetails('${row.username}')">
                                    ${row.username}
                                </a>
                            </td>
                            <td contenteditable="true">${row.phone_number}</td>
                            <td contenteditable="true">${row.registration_date}</td>
                            <td contenteditable="true">${row.invited_by}</td>
                            <td>
                                <button class="btn btn-actions btn-delete" data-id="${row.id}">Delete</button>
                            </td>
                            <td>
                                <button class="btn btn-actions btn-edit" data-id="${row.id}">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                    <button class="btn btn" id="previous-btn"><i class="bi bi-chevron-left"></i> Previous</button>
                    <span id="page-number">1</span>
                    <button class="btn btn" id="next-btn">Next <i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>


  <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <!-- Content for the first column (col-md-2) -->
            </div>

            <div class="col-md-8">
                <!-- UserInfoDiv with col-md-8 -->
                <div id="userInfoDiv" class="mt-3 text-center">
                    <button id="generateButton" style="width: 100%; background-color: #ff9933;" class="btn " onclick="generateUserInfo()">Generate User Details</button>
                    <p id="userInfoText"></p>
                </div>
            </div>

            <div class="col-md-2">
                <!-- Content for the third column (col-md-2) -->
            </div>
        </div>
    </div>


<!-- Inside your HTML body, add the modal structure -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Packages:</h5>
                <p id="packagesData"></p>

                <h5>Deposits:</h5>
                <p id="depositsData"></p>

                <h5>Referral Bonus:</h5>
                <p id="referralBonusData"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>








<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

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
        
    function openUserDetails(username) {
        $.ajax({
            type: 'POST',
            url: 'user_details_modal.php',
            data: { selected_username: username },
            dataType: 'json', // Expect JSON response
            success: function(data) {
                // Display the data in the modal
                $('#packagesData').text(JSON.stringify(data.packages));
                $('#depositsData').text(JSON.stringify(data.deposits));
                $('#referralBonusData').text(JSON.stringify(data.referral_bonus));

                // Show the modal
                $('#userDetailsModal').modal('show');
            },
            error: function(error) {
                console.error('Error fetching user details:', error);
            }
        });
    }
        
                function generateUserInfo() {
            // Use AJAX or fetch to call the server-side script
            fetch('generate_user_info.php')
                .then(response => response.text())
                .then(data => {
                    // Display the user info in the div
                    document.getElementById('userInfoText').innerText = data;
                })
                .catch(error => {
                    console.error('Error fetching user info:', error);
                });
        }
        
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
    const searchInput = document.getElementById("search-input");
    const searchButton = document.getElementById("search-btn");

    let currentPage = 1;
    let entriesPerPage = parseInt(showEntries.value, 10);

    // Function to populate the table
    function populateTable(page, search = '') {
        const start = (page - 1) * entriesPerPage;

        // Use fetch to make an AJAX request
        fetch(`admin_data.php?page=${page}&entries=${entriesPerPage}&search=${search}`)
            .then((response) => response.json())
            .then((data) => {
                tableBody.innerHTML = ""; // Clear existing table rows

                // Loop through the data and append rows to the table
                data.forEach((row) => {
                    tableBody.innerHTML += `
                        <tr>
                            <td>${row.id}</td>
                            <td>${row.email}</td>
                            <td><a href="#" class="username-link" data-username="${row.username}">${row.username}</a></td>
                            <td>${row.phone_number}</td>
                            <td>${row.registration_date}</td>
                            <td>${row.invited_by}</td>
                            <td>
                                <button class="btn btn-actions btn-delete" data-id="${row.id}">Delete</button>
                            </td>
                            <td>
                                <button class="btn btn-actions btn-edit" data-id="${row.id}">Edit</button>
                            </td>
                        </tr>`;
                });

                attachUpdateEventListeners(); // Attach event listeners after updating the table
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

function attachUpdateEventListeners() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    const editButtons = document.querySelectorAll('.btn-edit');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id');

            // Send an AJAX request to delete the user
            fetch('admin_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&user_id=${userId}`,
            })
            .then(response => response.text())
            .then(data => {
                // Handle success or error response
                console.log(data);
                // Optionally, you can update the table after a successful delete
                updateTable();
            })
            .catch(error => {
                console.error('Error deleting user:', error);
            });
        });
    });

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id');

            // Iterate through each editable cell and get the updated content
            const editableCells = document.querySelectorAll(`#table-body tr[data-id="${userId}"] [contenteditable="true"]`);
            const updatedData = {};
            
            editableCells.forEach(cell => {
                const fieldName = cell.getAttribute('data-field'); // Add data-field attribute to each cell
                const fieldValue = cell.textContent.trim();
                updatedData[fieldName] = fieldValue;
            });

            // Send an AJAX request to update the user
            fetch('admin_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=edit&user_id=${userId}&data=${JSON.stringify(updatedData)}`,
            })
            .then(response => response.text())
            .then(data => {
                // Handle success or error response
                console.log(data);
                // Optionally, you can update the table after a successful edit
                updateTable();
            })
            .catch(error => {
                console.error('Error editing user:', error);
            });
        });
    });
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
        if (currentPage > 1) {
            currentPage--;
            updateTable();
            if (currentPage === 1) {
                previousButton.style.display = "none";
            }
        }
    });

    // Event listener for changing entries per page
    showEntries.addEventListener("change", () => {
        entriesPerPage = parseInt(showEntries.value, 10);
        currentPage = 1;
        updateTable();
    });

    // Event listener for search button
    searchButton.addEventListener("click", () => {
        currentPage = 1;
        updateTable();
    });
});
    </script>
</body>

</html>