<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Configuration
$toEmail = 'collinsyogoh@gmail.com'; // The recipient's email address
$fromName = 'Telmark Website';

$attachmentUploadDir = __DIR__ . '/uploads/'; // Directory where uploaded files will be saved
$allowFileTypes = array('jpeg', 'jpg', 'png'); // Allowed file types
$smtpHost = 'smtp.gmail.com'; // Gmail SMTP host
$smtpUsername = 'collinsyogoh@gmail.com'; // Your Gmail email
$smtpPassword = 'Benbrian@03'; // Your Gmail password
$smtpPort = 587; // SMTP port (587 for TLS)

// Function to send an email with attachments and delete uploaded files
function sendEmailAndDeleteFiles($toEmail, $fromName, $attachmentUploadDir, $allowFileTypes, $smtpHost, $smtpUsername, $smtpPassword, $smtpPort) {
    $postData = $statusMsg = $valErr = '';
    $msgClass = 'errordiv';

    if (isset($_POST['submit'])) {
        $postData = $_POST;
        $Name = trim($_POST['Name']);
        $Email = trim($_POST['Email']);
        $Phone = trim($_POST['Phone']);
        $uploadedFile = array(); // Initialize as an empty array

        if (empty($Name) || empty($Email) || !filter_var($Email, FILTER_VALIDATE_EMAIL) || empty($Phone)) {
            $valErr = 'Please fill all mandatory fields.';
        }

        if (empty($valErr)) {
            $uploadStatus = 1;

            if (!empty($_FILES['cover']['name'])) {
                $targetDir = $attachmentUploadDir;
                $coverFileName = basename($_FILES['cover']['name']);
                $coverTargetFilePath = $targetDir . $coverFileName;
                $coverFileType = pathinfo($coverTargetFilePath, PATHINFO_EXTENSION);

                if (in_array($coverFileType, $allowFileTypes)) {
                    if (move_uploaded_file($_FILES['cover']['tmp_name'], $coverTargetFilePath)) {
                        $uploadedFile = array($coverTargetFilePath);
                    } else {
                        $uploadStatus = 0;
                        $statusMsg = 'Sorry, there was an error uploading your file.';
                    }
                } else {
                    $uploadStatus = 0;
                    $statusMsg = 'Sorry, only ' . implode('/', $allowFileTypes) . ' files are allowed to upload.';
                }
            }

            if ($uploadStatus == 1) {
                $emailSubject = 'Submit Request Submitted by ' . $Name;
                $htmlContent = '
                <h2>Form Request Submitted</h2>
                <p><b>Name</b> ' . $Name . '</p>
                <p><b>Email</b> ' . $Email . '</p>
                <p><b>Phone</b> ' . $Phone . '</p>
                ';

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUsername;
                $mail->Password = $smtpPassword;
                $mail->SMTPSecure = 'tls';
                $mail->Port = $smtpPort;
                $mail->setFrom($smtpUsername, $fromName);
                $mail->addAddress($toEmail);
                $mail->Subject = $emailSubject;
                $mail->isHTML(true);
                $mail->Body = $htmlContent;

                foreach ($uploadedFile as $file) {
                    if (file_exists($file)) {
                        $mail->addAttachment($file);
                    }
                }

                if ($mail->send()) {
                    $statusMsg = 'Thanks, Your Request has been submitted Successfully.';
                    $msgClass = 'succdiv';
                } else {
                    $statusMsg = 'Failed. Something Went Wrong. Please Try Again.';
                }

                // Delete uploaded files
                foreach ($uploadedFile as $file) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        } else {
            $statusMsg = $valErr;
        }
    }

    return $statusMsg;
}

// Call the function to process the form and send the email
$statusMsg = sendEmailAndDeleteFiles($toEmail, $fromName, $attachmentUploadDir, $allowFileTypes, $smtpHost, $smtpUsername, $smtpPassword, $smtpPort);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Status</title>
</head>
<body>
    <div class="submission-status">
        <?php if (!empty($statusMsg)): ?>
            <div class="alert <?php echo $msgClass; ?>">
                <?php echo $statusMsg; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
