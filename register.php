<?php
session_start();
require_once "elements/redirect.php";
require_once 'notification_system.php';

if (isset($_POST["username"]) && isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["email"]) && isset($_POST["pwdHash"]) && isset($_POST["pwdHashr"])) {


    $username = $_POST["username"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $pwdHash = $_POST["pwdHash"];
    $pwdHashr = $_POST["pwdHashr"];

    if (isset($_POST["submit"])) {

        $filepath = null;

        if (is_uploaded_file($_FILES['profilepicture']["tmp_name"])) {
            $target_dir = "assets/pictures/users/";
            $tmp_file = $_FILES['profilepicture']["tmp_name"];
            $filepath = $target_dir . $_FILES["profilepicture"]["name"];
            $imageFileType = strtolower(pathinfo($tmp_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["profilepicture"]["tmp_name"]);
            $uploadOk = 1;
            if ($check !== false) {
             //   echo "File is an image - " . $check["mime"] . ".";

                move_uploaded_file($tmp_file, $filepath);
                $uploadOk = 1;
            } else {

           //     echo "<br> File is not an image. <br>";

                $uploadOk = 0;
            }
        } else {

        //    echo "<br> empty image <br>";
        }
       // echo "this is the filepath for the database:" . $filepath;
    }

    ?>

    <?php

    if ($pwdHash != $pwdHashr) {
        $failedPassword = true;

    } else {
        include_once("elements/database.php");
        $stmt = $con->prepare("SELECT * FROM User WHERE username = :username and email = :email ");
        $stmt->execute(["username" => $username, "email" => $email]);
        $user = $stmt->fetch();
        // Check is user already exists
        if ($user) {
            $failedUser = true;
        } else {
            $emailregex = $_POST["email"];
            $pattern = '/htl-shkoder\\.com/i';
            if(preg_match($pattern,$emailregex)){
            // Add new user
            $pwdHash1 = password_hash($_POST["pwdHash"], PASSWORD_DEFAULT);
            $statement = $con->prepare(
                "INSERT INTO User(username, first_name, last_name, email, pwd_hash, role, picturePath) VALUES (:username, :firstName, :lastName, :email, :pwd_hash, 2, :profilepicture);"
            );
            echo 'filepath: ' . $filepath;
            $statement->execute([
                "username" => $username,
                "firstName" => $firstName,
                "lastName" => $lastName,
                "email" => $email,
                "pwd_hash" => $pwdHash1,
                "profilepicture" => $filepath

            ]);
            // echo $_POST["pwdHash"];
            // Login and redirect
            $_SESSION["username"] = $username;
            $_SESSION["firstName"] = $firstName;
            $_SESSION["lastName"] = $lastName;
            $_SESSION["email"] = $email;
            $_SESSION["role"] = 2;
            $_SESSION["profilepicture"] = $filepath;


            redirectTo("index.php?register=1");
        }

            }



    }

    $to = $_POST["email"];
    $firstName2 = $_POST["firstName"];
    $lastName2 = $_POST["lastName"];
    $message_text = "Welcome to our Bibliothek! Thank you for signing up and enjoy your time with our books!";
    sendEmail($to, $firstName2, $lastName2, 'Unreturned Book', $message_text);
}
require "elements/header.php";
require "elements/nav.php";

?>

<style>
    .fileimg::file-selector-button {
        background-color:#CA2D2D;
        color: white;

    }



</style>

<!-- Hero Bereich-->
<div class=" pt-5 text-center d-md-flex " style="min-height: 80rem;
                background: linear-gradient(rgb(40, 12, 26, 0.0), rgb(23, 23, 23, 0.86)), url('assets/pictures/heroregister.jpg ');
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;">
        <div class="container ">
            <section class="vh-100 bg-image">
                <div class="mask d-flex align-items-center h-100 gradient-custom-3">
                    <div class="container h-100">
                        <div class="row d-flex justify-content-center align-items-start h-100">
                            <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                                <div class="card" style="border-radius: 0px;">
                                    <div class="card-body p-5">
                                        <h2 class="text-uppercase text-center mb-3" style="font-family: 'soria">Create an account</h2>
                                        <form class="needs-validation" action="register.php" method="POST" enctype="multipart/form-data" novalidate>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-outline mb-4  ">
                                                        <label class="form-label d-flex justify-content-start" for="profilepicture">Profile Picture</label>
                                                        <div class="d-flex justify-content-start mt-0 align-items-center ">
                                                            <input type="file" class="fileimg form-control text-center me-4   rounded-0 " name="profilepicture" id="profilepicture" style=" width: 107px; height: 37px">
                                                                <?php $default_pfp = "assets/pictures/users/pfpreview.png" ?>
                                                                <img id="previewpfp" src="<?php  echo $default_pfp  ?>" class="img-fluid mt-0" style="border-radius: 50%; width: 80px; height: 80px;">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-outline mb-4 ">
                                                        <label class="form-label d-flex justify-content-start" for="username">Username</label>
                                                        <input type="text" id="username" class="form-control mt-3 form-control-lg" name="username" style="border-radius: 0px;" required <?php $username ?> />
                                                        <?php
                                                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                                            if (!empty($_POST["username"])) {
                                                                if (strlen($_POST["username"]) >= 3) {
                                                                    $username = sanitizeInput($_POST["username"]);
                                                                    $usernameerr = "";
                                                                } else {
                                                                    echo " <div class='alert alert-danger  rounded-0 mt-2' role='alert'>";
                                                                    $usernameerr = "The username should contain at least 3 letters";
                                                                    echo $usernameerr;
                                                                    echo "</div>";
                                                                }
                                                            } else {
                                                                echo " <div class='alert alert-danger  rounded-0 mt-2' role='alert'>";
                                                                $usernameerr = "Username is not set!";
                                                                echo $usernameerr;
                                                                echo "</div>";
                                                            }
                                                        } else {
                                                            $usernameerr = "";
                                                        }

                                                        ?>

                                                        <div class="valid-feedback">
                                                            Looks good!
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                                <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-outline mb-4 ">
                                                        <label class="form-label d-flex justify-content-start" for="firstName">First Name</label>
                                                        <input type="text" id="firstName" class="form-control form-control-lg" name="firstName" style="border-radius: 0px;" required <?php $firstName ?> />
                                                        <?php
                                                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                                            if (!empty($_POST["firstName"])) {
                                                                if (strlen($_POST["firstName"]) >= 3) {
                                                                    $firstName = sanitizeInput($_POST["firstName"]);
                                                                    $fnamerr = "";
                                                                } else {
                                                                    echo " <div class='alert alert-danger  rounded-0 mt-2' role='alert'>";
                                                                    $fnamerr = "The name should contain at least 3 letters";
                                                                    echo $fnamerr;
                                                                    echo "</div>";
                                                                }
                                                            } else {
                                                                echo " <div class='alert alert-danger  rounded-0 mt-2' role='alert'>";
                                                                $fnamerr = "First name is not set!";
                                                                echo $fnamerr;
                                                                echo "</div>";
                                                            }
                                                        } else {
                                                            $fnamerr = "";
                                                        }

                                                        ?>

                                                        <div class="valid-feedback">
                                                            Looks good!
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-outline mb-4">
                                                        <label class="form-label d-flex justify-content-start" for="lastName">Last Name</label>
                                                        <input type="text" id="lastName" class="form-control form-control-lg" name="lastName" style="border-radius: 0px;" required <?php $lastName ?>/>

                                                        <?php
                                                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                                            if (!empty($_POST["lastName"])) {

                                                                $lastName = sanitizeInput($_POST["lastName"]);
                                                                $lnamerr = "";
                                                            } else {
                                                                echo " <div class='alert alert-danger  rounded-0 mt-2' role='alert'>";
                                                                $lnamerr = "Last name is not set!";
                                                                echo $lnamerr;
                                                                echo "</div>";
                                                            }
                                                        } else {
                                                            $lnamerr = "";
                                                        }

                                                        ?>
                                                        <div class="valid-feedback">
                                                            Looks good!
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-outline mb-4 needs-validation">
                                                <label class="form-label d-flex justify-content-start" for="email"> Email</label>
                                                <input type="email" id="email" class="form-control  form-control-lg" name="email" style="border-radius: 0px;" required <?php $email ?>/>
                                                <?php

                                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                                    if (!empty($_POST["email"])) {

                                                        $emailregex = $_POST["email"];
                                                        $pattern = '/htl-shkoder\\.com/i';
                                                        if(!preg_match($pattern,$emailregex)){
                                                            echo " <div class='alert alert-danger  rounded-0 mt-2' role='alert'>";
                                                            $emailerr = "the email should be like: test@htl-shkoder.com";
                                                            echo $emailerr;
                                                            echo "</div>";

                                                        }
                                                            else {
                                                                $emailregex = sanitizeInput($_POST["email"]);
                                                                $emailerr = "";
                                                        }
                                                    } else {
                                                        echo " <div class='alert alert-danger  rounded-0 mt-2' role='alert'>";
                                                        if (isset($emailerr)) {
                                                            echo "<p>You must write your email!</p>";
                                                        }
                                                        echo "</div>";
                                                    }
                                                } else {
                                                    $emailerr = "";
                                                }


                                                ?>
                                                <div class="valid-feedback rounded-0">
                                                    Looks good!
                                                </div>
                                            </div>



                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-outline mb-4">
                                                        <label class="form-label d-flex justify-content-start" for="pwdHash">Password</label>
                                                        <input type="password" id="pwdhash" class="form-control form-control-lg" name="pwdHash" style="border-radius: 0px;" required <?php $pwdHash ?>/>

                                                        <?php
                                                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                                            if (strlen($_POST["pwdHash"]) >= 6) {
                                                                $pwdHash = sanitizeInput($_POST["pwdHash"]);
                                                                $pwderr = "";
                                                            } else {
                                                                echo " <div class='alert alert-danger rounded-0 mt-2' role='alert'>";
                                                                $pwderr = "The password should contain at least 6 characters!";
                                                                echo $pwderr;
                                                                echo "</div>";
                                                            }
                                                        }
                                                        ?>
                                                        <div class="valid-feedback">
                                                            Looks good!
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-outline mb-4">
                                                        <label class="form-label d-flex justify-content-start" for="pwdHashr">Repeat your password</label>
                                                        <input type="password" id="pwdHashr" class="form-control form-control-lg" name="pwdHashr" style="border-radius: 0px;"  required <?php $pwdHashr ?>/>

                                                        <?php if (isset($failedPassword)) {
                                                            echo " <div class='alert alert-danger  rounded-0 mt-2' role='alert'>";
                                                            echo "<p>Passwords must be equal!</p>";
                                                            echo "</div>";
                                                        }
                                                        ?>
                                                        <div class="valid-feedback">
                                                            Looks good!
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>




                                            <div class="form-check d-flex justify-content-center mb-5">
                                                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3cg" />
                                                <label class="form-check-label" for="form2Example3g">
                                                    I agree all statements in <a href="#!" class="text-body"><u>Terms of service</u></a>
                                                </label>
                                            </div>

                                            <form action="register.php" class="d-flex justify-content-center" method="POST">
                                                <input type="checkbox" name="g-recaptcha" hidden="">
                                                <div class="g-recaptcha d-flex justify-content-center"  data-sitekey="6LcR5Z4pAAAAAFGZdN9lacVsSGYt68s1uxTJR9wZ">
                                                </div>
                                                <br/>

                                                <button class="btn btn-primary rounded-0 " name="submit" type="submit">Register</button>
                                                <script src="https://www.google.com/recaptcha/api.js"></script>
                                            </form>

                                            <br>


                                            <p class="text-center text-muted mt-5 mb-0">Have already an account? <a href="login.php" class="fw-bold text-body"><u>Login here</u></a></p>

                                            <?php


                                            if (isset($failedUser)) {
                                                echo "<p>Username is already used! Please choose a different one!</p>";
                                            }

                                            echo "</div>";
                                            ?>
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
</div>



<?php
$str = 'ä ö ü ß < > & " \'';
$str = htmlspecialchars($str);

function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

<?php
require_once "elements/footer.php";
?>
|<script>
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



    const fileInput = document.getElementById('profilepicture');
    const imagePreview = document.getElementById('previewpfp');

    // Add an event listener to the file input for the 'change' event
    fileInput.addEventListener('change', function () {
        // Check if a file has been selected
        if (fileInput.files.length > 0) {
            const selectedFile = fileInput.files[0];
            const reader = new FileReader();

            reader.onload = function (e) {
                // Display the selected image
                imagePreview.src = e.target.result;
            };

            // Read the selected file as a data URL$
              reader.readAsDataURL(selectedFile);
        } else {
            // No file selected, revert to default image
            imagePreview.src = 'assets/pictures/users/pfpreview.png';
        }
    });

</script>

<script src='https://www.google.com/recaptcha/api.js'></script>
