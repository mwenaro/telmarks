<?php
if (isset($_POST['send'])) {
    // Get the phone number and views from the form
    $phoneNumber = $_POST['Phone_Number'];
    $views = $_POST['views'];

    // Calculate the result by multiplying views by 100
    $result = $views * 100;

    // Handle the image upload
    if (isset($_FILES['cover'])) {
        $targetDirectory = "uploads/"; // Define your target directory
        $targetFile = $targetDirectory . basename($_FILES['cover']['name']);

        // Check if the file is an image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowedExtensions)) {
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetFile)) {
                // Image uploaded successfully
                if (file_exists($targetFile)) {
                    // Connect to your MySQL database
                    $servername = "localhost";
                    $username = "telmarka_db";
                    $password = "Benbrian@01";
                    $dbname = "telmarka_db";

                    // Create a connection
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check the connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Use a prepared statement to insert data into the database
                    $stmt = $conn->prepare("INSERT INTO data (phone_number, views, result, image_path) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiis", $phoneNumber, $views, $result, $targetFile);

                    if ($stmt->execute()) {
                        // Data inserted successfully, redirect to screenshot.php
                        header('Location: screenshot.php');
                        exit; // Stop execution to prevent further output
                    } else {
                        echo "Error inserting data: " . $stmt->error;
                    }

                    // Close the prepared statement and the database connection
                    $stmt->close();
                    $conn->close();
                } else {
                    echo "Error: File does not exist after move operation.";
                }
            } else {
                echo "Error uploading the image.";
            }
        } else {
            echo "Invalid image format. Only JPG, JPEG, PNG, and GIF allowed.";
        }
    }
}
?>
