<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/root/PHPMailer/src/Exception.php';
require '/root/PHPMailer/src/PHPMailer.php';
require '/root/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    echo "Try sending an email";
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.office365.com';                  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'Bibliothek@htl-shkoder.com';             // SMTP username
    $mail->Password = 'V3r-B1bL-2024!';                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable SSL encryption, TLS also accepted with port 465
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('Bibliothek@htl-shkoder.com', 'Mailer');          //This is the email your form sends From
    $mail->addAddress('megrro17@htl-shkoder.com', 'Joe User'); // Add a recipient address
    //$mail->addAddress('contact@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Subject line goes here';
    $mail->Body    = 'Body text goes here';
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
?>