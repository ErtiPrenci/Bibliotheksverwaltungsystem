<?php
session_start();
require_once "elements/database.php";
require_once "elements/header.php";
?>
<style>

</style>
<?php
require_once "elements/nav.php";
?>
<div class=" pt-5 text-center d-md-flex d-flex align-items-center" style="height: 30rem;
                background: linear-gradient(rgb(40, 12, 26, 0.0), rgb(23, 23, 23, 0.86)), url('assets/pictures/book_shelf_3.png ');
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
    <div class="card m-5 border-3 border-white text-center text-white bg-transparent"
         style="height: 10rem; width: 25rem;">
        <div class="card-body">
            <h5 class="card-title" style="font-family: 'Soria';">About Us</h5>
            <p class="card-text" style="font-family: 'Prociono TT';">Das ist unsere Diplomaarbeit.</p>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="assets/pictures/book_shelf_2.png" class="w-100 h-100 m-2" style="object-fit: cover;">
        </div>
        <div class="col-md-6 ">
            <h1>Warum ist unsere Diplomaarbeit entstanden?</h1>
            <hr class="border-2">
            <p class="" style="font-family: 'Prociono TT';">Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                sed do eiusmod tempor incididunt
                ut labore et dolore magna aliqua. Quis auctor elit sed vulputate mi sit amet. Aliquam ut porttitor
                leo a.
                Orci dapibus ultrices in iaculis. Sed euismod nisi porta lorem mollis aliquam ut porttitor leo. </p>
        </div>
    </div>
</div>
<div class="bg-primary mt-5 text-center d-flex align-items-center text-white" style="height: 300px;">
    <h4>
        <i class="bi bi-quote"></i>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
        incididunt ut labore et dolore magna aliqua.
        <i class="bi bi-quote"></i></h4>

</div>

    <h1 class="text-center m-5">Project Team</h1>
<div class="container">
    <div class="row">
        <div class="col-md-3">
        <div class="">
            <img src="assets/pictures/users/arens.jpg" class="w-100 h-100 mt-2 " alt="...">
            <p class="text-center"><b>Arens Danja</b></p>
            <p class="text-center text-muted">Projekt Leiter</p>
        </div>
        </div>
        <div class="col-md-3">
            <div class="">
                <img src="assets/pictures/users/megi.jpg" class="w-100 h-100 mt-2" alt="...">
                <p class="text-center"><b>Megi Rrotani</b></p>
                <p class="text-center text-muted">Stv. Projekt Leiter</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="">
                <img src="assets/pictures/users/matea.jpg" class="w-100 h-100 mt-2" alt="...">
                <p class="text-center"><b>Matea Cepi</b></p>
                <p class="text-center text-muted">Team Mitglied</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="">
                <img src="assets/pictures/users/Dodge&Burn+Frequenztrennung.jpg" class="w-100 h-100 mt-2 " alt="...">
                <p class="text-center"><b>Erti Prenci</b></p>
                <p class="text-center text-muted">Team Mitglied</p>
            </div>
        </div>
        </div>
</div>
    <?php
require_once "elements/footer.php";
?>
