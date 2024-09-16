<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
} else {
    require_once "elements/database.php";
    require_once "notification_system.php";

    if(isset($_GET["isbn"]) && isset($_GET["userid"])) {
        $stmt_update_borrowing = $con->prepare("UPDATE Borrowing SET borrow_date = now(), return_date = date_add(now(), INTERVAL 2 WEEK), active = 1
                                        WHERE user_id = :user_id and isbn = :isbn");
        $stmt_update_borrowing->execute([
            "user_id" => $_GET["userid"],
            "isbn" => $_GET["isbn"]
        ]);

        $stmt_delete_borrowing = $con->prepare("DELETE FROM Borrowing WHERE isbn = :isbn and active = 0");
        $stmt_delete_borrowing->execute([
            "isbn" => $_GET["isbn"]
        ]);


        $stmt_fetch_user = $con->prepare("SELECT first_name, last_name, email FROM User WHERE id = :user_id");
        $stmt_fetch_user->execute([
            "user_id" => $_GET["userid"]
        ]);

        $user_info = $stmt_fetch_user->fetch(PDO::FETCH_ASSOC);


        $to = $user_info['email'];
        $firstName = $user_info['first_name'];
        $lastName = $user_info['last_name'];
        $message_text = "Your book borrowing request has been confirmed. Please make sure to return the book on time.";

        sendEmail($to, $firstName, $lastName, 'Approve Borrow Request', $message_text);
    } else {
        redirectTo("transaction.php?error=1");
    }
}
redirectTo("transaction.php");

?>