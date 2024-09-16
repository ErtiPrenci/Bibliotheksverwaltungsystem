<?php
session_start();

include_once("elements/database.php");

if (isset($_POST["submit"])){
if (isset($_POST["message"]) && !empty(trim($_POST["message"]))) {
    if (isset($_SESSION["user_id"])) {
        $message = $_POST["message"];
        $insertStatement = $con->prepare(
            "INSERT INTO Notification (user_id, message, sent_date) VALUES (:user_id, :message, now())");
        $insertStatement->execute([
            "message" => $message,
            "user_id" => $_SESSION["user_id"],
        ]);
        // feedba kwhen message was sent successfully
        $_SESSION["messagesent"] = "Your message was sent to the admin";
    } else {
        // feedback when user is not logged in
        header("Location: login.php?error=login_required");
        exit();
    }
} else {
    // feedback when input is empty
    $_SESSION["emptyinput"] = "Textarea should not be empty";
}
}


require_once 'elements/header.php';
?>

<style>
    .titel {
        text-align: center;
        font-size: 42pt;
        margin-bottom: 10px;
        margin-top: 10px;
    }

    .div1 {
        background-color: #b5bda8;
        height: 450px;
    }

    .map-container {
        margin-top: 20px; /* Add margin to create space between the rows */
    }
</style>

<?php
require_once 'elements/nav.php';
?>

<div class="container mt-5">
    <div class="row g-3">
        <h1 class="titel mt-5 mb-3" style="font-family: 'Soria'; color: #CA2D2D;">Contact us</h1>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="div1 mb-3">
                <form action="contact.php" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="row g-2 m-3 align-items-center">
                        <div class="col-md-6">
                            <label for="username" class="col-form-label">Username</label>
                        </div>
                        <div class="col-md-6">
                            <?php if (isset($_SESSION["username"])) { ?>
                                <input class="form-control" type="text" name="username" disabled value="<?php echo $user["username"]; ?>" id="username" aria-label="default input example" required>
                            <?php } else { ?>
                                <input class="form-control" type="text" name="username" id="username" aria-label="default input example" required>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row g-3 m-3 align-items-center">
                        <div class="col-md-6">
                            <label for="email" class="col-form-label">Email</label>
                        </div>
                        <div class="col-md-6">
                            <?php if (isset($_SESSION["username"])) { ?>
                                <input class="form-control" type="email" name="email" id="email" disabled value="<?php echo $user["email"]; ?> " aria-label="default input example" required>
                            <?php } else { ?>
                                <input class="form-control" type="email" name="email" id="email" aria-label="default input example" required>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-sm-11 row g-3 m-3 align-items-center">
                        <textarea class="form-control" name="message" id="message" placeholder="Please describe the reason you want to contact us!" style="height: 100px" required></textarea>
                    </div>


                    <button type="submit" class="btn btn-outline-danger row g-3 m-4 align-items-center" name="submit" data-bs-toggle="modal" data-bs-target="#exampleModal">Send form</button>



                </form>
            </div>
        </div>


        <div class="col-lg-6">
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2962.249056827319!2d19.514963175954023!3d42.05928067122243!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x134e01011216f087%3A0xdc82e550ef4b83ca!2sShkolla%20e%20Mesme%20Austriake!5e0!3m2!1sde!2s!4v1704957598020!5m2!1sde!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>


<?php
require_once "elements/footer.php";
require_once "elements/modal.php";

// feedback for message sent successfully
if (isset($_SESSION["messagesent"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", $_SESSION["messagesent"], "success");
    unset($_SESSION["messagesent"]); // Remove notification after displaying
}

//feedback for empty input
if (isset($_SESSION["emptyinput"])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>", $_SESSION["emptyinput"], "danger");
    unset($_SESSION["emptyinput"]); // Remove notification after displaying
}


?>
