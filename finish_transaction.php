<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";

if(isset($_GET["isbn"]) && isset($_GET["userid"])) {
    $stmt_finish_borrow = $con->prepare("UPDATE Borrowing SET finished = 1, active = 0 WHERE isbn = :isbn AND user_id = :user_id");
    $stmt_finish_borrow->execute([
        "isbn" => $_GET["isbn"],
        "user_id" => $_GET["userid"]
    ]);
}

redirectTo("transaction.php");
?>

