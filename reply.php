<?php
session_start();
require_once "elements/redirect.php";
require_once "elements/database.php";
require_once "notification_system.php";
if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}
if (isset($_POST["replyMessage"]) && isset($_POST["recipientEmail"])) {
    $replyMessage = $_POST["replyMessage"];
    $recipientEmail = $_POST["recipientEmail"];

    try {
        // Call the stored procedure to fetch user information
        $sql = "CALL GetUserByEmail(:recipient_email)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':recipient_email', $recipientEmail, PDO::PARAM_STR);
        $stmt->execute();
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_info) {
            $to = $user_info['email'];
            $firstName = $user_info['first_name'];
            $lastName = $user_info['last_name'];
            $message_text = $replyMessage;

            sendEmail($to, $firstName, $lastName, 'Reply To Your Message', $message_text);
            redirectTo("contactadmin.php?sent_email=true");
        } else {
            echo "Failed to send reply. User not found.";
        }
    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
