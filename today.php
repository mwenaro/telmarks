<?php
// Database connection parameters
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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if two files are uploaded
    if (count($_FILES['cover']['name']) != 2) {
        echo "Error: Please upload exactly two pictures.";
        exit;
    }

    // Define allowed file formats
    $allowedFormats = ['jpeg', 'jpg', 'png'];

    // Remove previous uploaded files from the database
    $deletePreviousFilesSql = "DELETE FROM uploaded_files";
    if ($conn->query($deletePreviousFilesSql) !== TRUE) {
        echo "Error: Unable to delete previous files from the database. " . $conn->error;
        exit;
    }

    // Loop through the uploaded files
    for ($i = 0; $i < 2; $i++) {
        $fileName = $_FILES['cover']['name'][$i];
        $fileSize = $_FILES['cover']['size'][$i];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file format
        if (!in_array($fileType, $allowedFormats)) {
            echo "Error: Invalid file format. Only JPEG, JPG, and PNG are allowed.";
            exit;
        }

        // Validate file size
        if ($fileSize > 800000) {
            echo "Error: File size exceeds the limit of 800KB.";
            exit;
        }

        // Generate unique filename
        $newFileName = "picture" . ($i + 1) . "." . $fileType;

        // Move uploaded file to the server (using 'today' directory)
        move_uploaded_file($_FILES['cover']['tmp_name'][$i], "today/" . $newFileName);

        // Insert file information into the database
        $sql = "INSERT INTO uploaded_files (filename, file_size, file_type) VALUES ('$newFileName', $fileSize, '$fileType')";
        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
            exit;
        }
    }

    // Close the database connection
    $conn->close();

    // Show success alert and redirect
    echo '<script>alert("Files uploaded successfully."); window.location.href = "admin.php";</script>';
    exit;

} else {
    // Show error alert and redirect
    echo '<script>alert("Upload unsuccessful. Try Again Later"); window.location.href = "todays_admin.php";</script>';
    exit;
}
?>
