<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";
require_once "elements/header.php";
require_once "elements/nav.php";
?>

<header class="text-center mb-4 mt-5">
    <h1 style="font-family: Soria">Welcome to the administration area</h1>
</header>

<main class="container d-flex justify-content-center mb-4" style="min-height: 55vh">
    <ul class="list-group w-50">
        <a href="transaction.php" class="list-group-item list-group-item-action rounded-0 text-center">Edit Transactions/Borrowings</a>
        <a href="admin_add_transaction.php" class="list-group-item list-group-item-action rounded-0 text-center">Add Transactions/Borrowings</a>
        <a href="bookadmin.php" class="list-group-item list-group-item-action rounded-0 text-center">Book administration</a>
        <a href="contactadmin.php" class="list-group-item list-group-item-action rounded-0 text-center">Contact administration</a>
        <a href="useradmin.php" class="list-group-item list-group-item-action rounded-0 text-center">User administration</a>
        <a href="reviewadmin.php" class="list-group-item list-group-item-action rounded-0 text-center">Review administration</a>
        <a href="feesadmin.php" class="list-group-item list-group-item-action rounded-0 text-center">Fees administration</a>
        <a href="eventadmin.php" class="list-group-item list-group-item-action rounded-0 text-center">Event administration</a>
        <a href="admin_contribute.php" class="list-group-item list-group-item-action rounded-0 text-center">Contribution Administration</a>
    </ul>
</main>

<?php
require_once "elements/footer.php";
?>
