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

// Initialize variables to store user data
$sessionUsername = $username = $full_name = $phone_number = $email = "";
$message = ""; // Variable to store success or error message

// Check if the session variable is set and not empty
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $sessionUsername = $_SESSION['user_name'];

    // Pre-fill the form with user data
    $sql = "SELECT * FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sessionUsername);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $username = $row['username'];
            $email = $row['email'];
            $full_name = $row['full_name'];
            $phone_number = $row['phone_number'];
        }
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user data from the form
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            // SQL query to update user data (including the profile image)
            $sql = "UPDATE Users SET full_name = ?, phone_number = ?, email = ?, profile_image = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssss", $full_name, $phone_number, $email, $target_file, $sessionUsername);

                if ($stmt->execute()) {
                    // Data updated successfully
                    $message = 'Account profile updated successfully';
                    echo '<script>
                            alert("'.$message.'");
                            window.location.href = "dashboard.php";
                          </script>';
                } else {
                    // Error occurred during execution
                    $message = 'Error updating user data: ' . $stmt->error;
                    echo '<script>
                            alert("'.$message.'");
                            window.location.href = "dashboard.php";
                          </script>';
                }

                $stmt->close();
            } else {
                // Error preparing the statement
                $message = 'Error preparing statement: ' . $conn->error;
                echo '<script>
                        alert("'.$message.'");
                        window.location.href = "dashboard.php";
                      </script>';
            }
        } else {
            // Error moving the uploaded file
            $message = 'Error moving the uploaded file';
            echo '<script>
                    alert("'.$message.'");
                    window.location.href = "dashboard.php";
                  </script>';
        }
    } else {
        // Profile picture not uploaded
        $message = 'Error: Profile picture not uploaded';
        echo '<script>
                alert("'.$message.'");
                window.location.href = "dashboard.php";
              </script>';
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>TELMARK DASHBOARD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
      integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css"
    />
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <link rel="stylesheet" href="side.css" />
    <style>
        /* Add your custom styles for the modal here */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
    </style>
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
      
      
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p id="popupMessage"></p>
    </div>
</div>



 <div class="container light-style flex-grow-1 container-p-y">
    <h4 class="font-weight-bold py-3 mb-4">
        Account settings
    </h4>

    <div class="row">
        <!-- Profile Picture Card -->
        <div class="col-md-6">
            <form method="post" enctype="multipart/form-data">
            <div class="cardy mb-4 h-100" style="background:#fff;">
                <div class="card-header text-center">
                    Profile Details
                </div>
                <div class="card-body d-flex flex-column justify-content-center" style="text-align: center;">
                    <i class="fa fa-user-circle fa-5x mb-3" aria-hidden="true"></i>
                    <label class="btn btn-outline-secondary" for="upload-photo" style="margin: 0 20px;">
                        Upload new photo
                        <input type="file" class="account-settings-fileinput" id="upload-photo" name="profile_picture">
                    </label>
                    <div class="text-light small mt-2">Allowed JPG, GIF, or PNG. Max size of 800K</div>
                </div>
            </div>
        </div>

        <!-- Update Details Card -->
        <div class="col-md-6">
            <div class="cardy mb-4 h-100" style="background:#fff;">
                <div class="card-header text-center">
                    Update Details
                </div>
                <div class="card-body">
                    
                        <div class="form-group">
                            <label for="username">Username</label>
                            <!-- Make sure $username is defined -->
                            <input type="text" class="form-control" name="username" id="username" value="<?php echo isset($username) ? $username : ''; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>"readonly>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" class="form-control" name="full_name" id="full_name" value="<?php echo $full_name; ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" class="form-control" name="phone_number" id="phone_number" value="<?php echo $phone_number; ?>"readonly>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update" style="display: block; margin: 0 auto;" id="updateProfileBtn">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




    <!-- End demo content -->

    <script
      src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
      integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
      integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
      integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
      crossorigin="anonymous"
    ></script>
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

    $(document).ready(function() {
        // Event listener for update button
        $("#updateProfileBtn").click(function() {
            // Create FormData object to send form data
            var formData = new FormData($('form')[0]);

            // Send data to the server using AJAX
            $.ajax({
                type: 'POST',
                url: '',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert(response.message);
                },
                error: function(xhr, status, error) {
                    alert("Error: " + xhr.responseText);
                }
            });
        });
    });

    // Function to display a pop-up message
    function showMessage(message) {
        document.getElementById("popupMessage").innerHTML = message;
        document.getElementById("myModal").style.display = "block";
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById("myModal").style.display = "none";
    }

    // Check if the PHP variable $message is set
    <?php if (isset($message) && !empty($message)) { ?>
        // Display the pop-up message
        showMessage('<?php echo $message; ?>');
    <?php } ?>

    </script>
    <script src="doc.js"></script>
  </body>
</html>
