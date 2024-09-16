<?php

//activate for error reports
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '/var/www/html/vendor/phpmailer/phpmailer/src/Exception.php';
require_once '/var/www/html/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once '/var/www/html/vendor/phpmailer/phpmailer/src/SMTP.php';

function sendEmail($to, $firstName, $lastName, $subject, $message_text)
{
    $mail = new PHPMailer(true); // Passing `true` enables exceptions

    try {
        // Server settings
        //$mail->SMTPDebug = 2; // Enable verbose debug output
        //ini_set('display_errors', 1);
        //error_reporting(E_ALL);
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.office365.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'Bibliothek@htl-shkoder.com'; // SMTP username
        $mail->Password = 'V3r-B1bL-2024!'; // SMTP password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to

        // Recipients
        $mail->setFrom('Bibliothek@htl-shkoder.com', 'Mailer');
        $mail->addAddress($to, "$firstName $lastName"); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = "<h1>Hello $firstName $lastName,</h1><p>$message_text</p>";
        $mail->AltBody = $message_text; // Plain text version of the email content

        $mail->send();


    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}

// Example usage:
// sendEmail('recipient@example.com', 'John', 'Doe', 'Return Reminder', 'This is a test message.');

?>
