<?php
session_start();
require_once "elements/header.php";
require_once "elements/redirect.php";

// Check if user is not logged in
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contribute_book']) && !isset($_SESSION['user_id'])) {
    // If user is not logged in, redirect to login page
    header("Location: login.php?error1=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['contribute_book'])) {
        $isbn = $_POST['isbn'];

        // Validation for the input format
        if (!preg_match('/^\d{9}[\d|X]$|^\d{13}$/', $isbn)) {
            header("Location: contribute.php?falseisbn=1");
        } else {
            redirectTo("contributeresponse.php?isbn=" . $isbn);
        }
    }
}
?>

<style>

    .row-container {
        display: flex;
        flex-wrap: wrap;
    }

    .circle-background {
        width: 600px;
        height: 600px;
        background-color: darkred;
        border-radius: 50%;
        position: absolute;
        top: 65%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: -1;
    }

    .form-container {
        position: relative;
    }

    .colored-div {
        background-color: grey;
        padding: 10px;
        width: 500px;
        height: 300px;
    }

    .colored-div2 {
        background-color: darkred;
        padding: 70px;
        width: 500px;
        height: 300px;
        box-shadow: 10px 10px 10px 10px rgba(0, 0, 0, 0.2);
        color: white;
    }

    .row-container {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    @media (max-width: 768px) {
        .circle-background {
            display: none; /* Hide circle background on smaller screens */
        }
    }

    @media screen and (max-width: 768px) {
        .colored-div {
            width: 100%; /* Each box takes up 100% of the container width */
            order: 2;
            margin-top: 0 !important;
        }
    }

    @media screen and (max-width: 768px) {
        .colored-div2 {
           width: 100%; /* Each box takes up 100% of the container width */
            order: 1;
            margin-top: 0 !important;
        }
    }


    @media screen and (max-width: 768px) {
        .row-container {
            flex-direction: column; /* Change to column layout on smaller devices */
            align-items: flex-start; /* Align items at the start of the cross axis */
        }
    }

</style>

<?php
require_once 'elements/nav.php';
?>

<div class="circle-background"></div>

<div class="row-container">
    <div class="align-items-center form-container colored-div">
        <form action="contribute.php" method="POST" id="contributeForm">
            <div class="row g-2 m-5 justify-content-center align-items-center">
                <div class="col-auto">
                    <label for="isbn" id="isbn" class="col-form-label">ISBN-Nummer</label>
                </div>
                <div class="col-auto">
                    <input class="form-control" type="text" name="isbn" aria-label="default input example" required>
                </div>
                <div class="col-auto"></div>
                <div class="col text-center">
                    <!-- Changed the button type to submit -->
                    <button style="color: white;" type="submit" name="contribute_book" id="contributeButton" class="btn btn-outline-danger border-white row g-3 m-5 align-items-center">Check the book informations</button>
                </div>
            </div>
        </form>
    </div>

    <div class="align-items-center form-container colored-div2" style="display: flex; justify-content: center; align-items: center;">
        <p class="mt-5" style="font-size: 70px; font-family: 'Soria'; ">Contribute a book</p>
    </div>
</div>

<?php
require_once "elements/footer.php";
require_once "elements/modal.php";

if (isset($_GET["contribute_books"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Your book was sent to the admin", "success");
}

if (isset($_GET["falseisbn"])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>", "You should give a correct isbn number!", "danger");
}


?>
