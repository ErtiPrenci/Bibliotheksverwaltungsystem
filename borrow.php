<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || !isset($_GET["isbn"]) || !isset($_GET["userid"])) {
    session_destroy();
    redirectTo("login.php?borrow_failure=1");
} else {
    require_once "elements/database.php";

    $book_is_borrowed_error = false;
    $book_limit_reached_error = false;
    $you_borrowed_this_error = false;
    $isbn = $_GET["isbn"];
    $user_id = $_GET["userid"];

    $stmt_borrowing = $con->prepare("SELECT count(*) AS 'count' FROM Borrowing WHERE user_id = :user_id");
    $stmt_borrowing->execute(array("user_id" => $_SESSION["user_id"]));
    $borrowings_for_user = $stmt_borrowing->fetchAll(PDO::FETCH_ASSOC);

    if($borrowings_for_user[0]["count"] < 5) {
        $stmt_check_borrow = $con->prepare("SELECT * FROM Borrowing WHERE isbn = :isbn");
        $stmt_check_borrow->execute(array("isbn" => $isbn));
        $check_borrow = $stmt_check_borrow->fetchAll(PDO::FETCH_ASSOC);

        $stmt_t_insert = $con->prepare("INSERT INTO Borrowing(user_id, isbn, finished, active) 
            VALUES (:user_id, :isbn, false, false);");

        if (empty($check_borrow)) {
            $stmt_t_insert->execute(["user_id" => $user_id, "isbn" => $isbn]);
        } else {
            foreach ($check_borrow as $borrow) {
                if ($borrow["active"] == 1) {
                    $book_is_borrowed_error = true;
                } else if ($user_id == $borrow["user_id"] && $borrow["finished"] != 1) {
                    $you_borrowed_this_error = true;
                }
            }

            if ($book_is_borrowed_error) {
                redirectTo("books.php?book_is_borrowed_error=1");
            } else if ($you_borrowed_this_error) {
                redirectTo("books.php?you_borrowed_this_error=1");
            } else {
                $stmt_t_insert->execute(["user_id" => $user_id, "isbn" => $isbn]);
            }
        }
    } else {
        $book_limit_reached_error = true;
        redirectTo("books.php?book_limit_reached_error=1");
    }

    redirectTo("books.php?borrow_success=1");
}
?>
