<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
} else {
    require_once "elements/database.php";

    if (isset($_GET["isbn"]) && isset($_GET["userid"])) {
        $stmt_deny_borrowing = $con->prepare("DELETE FROM Borrowing WHERE isbn = :isbn and user_id = :user_id");
        $stmt_deny_borrowing->execute([
            "isbn" => $_GET["isbn"],
            "user_id" => $_GET["userid"]
        ]);
    } else {
        redirectTo("transaction.php?error=1");
    }

    redirectTo("transaction.php?error=0");
}
?>