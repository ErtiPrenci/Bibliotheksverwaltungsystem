<?php
session_start();

require_once "elements/redirect.php";
if(!isset($_GET["isbn"]) || $_SESSION["role"] != 1) {
    redirectTo("index.php");
}

$isbn = $_GET["isbn"];
require_once "elements/database.php";

$stmt_accept = $con->prepare("UPDATE Book SET accepted = 1 WHERE isbn = :isbn");
$stmt_accept->execute(["isbn" => $isbn]);

redirectTo("bookadmin.php?book_accepted=1");
