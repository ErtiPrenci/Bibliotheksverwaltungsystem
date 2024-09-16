<?php
session_start();
require_once "elements/redirect.php";

if ($_SERVER['REQUEST_METHOD']=='POST') {
    require_once "elements/database.php";
    $email = $_POST["email"];
    $pwdHash1 = $_POST["pwdHash"];

    $stmt = $con->prepare("SELECT * FROM User WHERE  email = :email");
    $stmt->execute(["email" => $email]);
    $user = $stmt->fetch();

    if ($user) {
        if(password_verify($_POST["pwdHash"],$user["pwd_hash"],)){
            $_SESSION["username"] = $user["username"];
            $_SESSION["firstName"] =$user["firstName"];
            $_SESSION["lastName"] = $user["lastName"];
            $_SESSION["email"] =  $user["email"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["user_id"] = $user["id"];

            redirectTo("index.php?login=1");
        } else {
            $failed = true;
        }
    } else {
        $failed=true;
    }
}

require_once "elements/header.php";
require_once "elements/nav.php";
?>

<!-- Hero Bereich-->
<div class=" pt-5 text-center d-md-flex" style="height: 56rem;
                background: linear-gradient(rgb(40, 12, 26, 0.0), rgb(23, 23, 23, 0.86)), url('assets/pictures/heroregister.jpg ');
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
<div class="container">
    <section class="vh-100 bg-image">
        <div class="mask d-flex align-items-center h-100 gradient-custom-3">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card" style="border-radius: 0px;">
                            <div class="card-body p-5">
                                <h2 class="text-uppercase text-center mb-5" style="font-family: 'soria'">Login</h2>

                                <form class ="needs-validation" method="POST"  novalidate  action ="login.php">
                                    <?php

                                    if(isset($failed)){ ?>
                                        <div class="bg-danger bg-opacity-50  d-flex align-items-center justify-content-center rounded-5" style="height: 50px;"> The Email or Passowrd may be wrong!  </div>
                                    <?php  }
                                    ?>
                                    <div class="form-outline mb-4 needs-validation" >
                                        <label class="form-label d-flex justify-content-start" for="email"> Email</label>
                                        <input type="email" id="email" name="email" class="form-control form-control-lg" style="border-radius: 0px;" required/>

                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>


                                    <div class="form-outline mb-4">
                                        <label class="form-label d-flex justify-content-start" for="pwdHash" >Password</label>
                                        <input type="password" id="pwdHash" name="pwdHash" class="form-control form-control-lg" style="border-radius: 0px;" required />

                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>


                                    <button class="btn btn-primary rounded-0 " type="submit">login</button>
                                    <p class="text-center text-muted mt-5 mb-0">Are you new here? <a href="register.php" class="fw-bold text-body"><u>Create an account</u></a></p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</div>
<?php
require_once "elements/modal.php";

if (isset($_GET["borrow_failure"])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>","Please login before you request to borrow any books!","danger");
}

if (isset($_GET["error"])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>","Please login before you send a message!","danger");
}

if (isset($_GET["error1"])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>","Please login before you contribute a book!","danger");
}

if (isset($_GET["submiterror"])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>","Please login before you send a review!","danger");
}


require_once "elements/footer.php";
?>
<script>
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>