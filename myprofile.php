<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"])) {
    session_destroy();
    redirectTo("index.php");
}
require_once  "elements/database.php";

if (isset($_POST["username"]) ){
    $username = $_POST["username"];

    $statement = $con->prepare("UPDATE User SET username = :username WHERE id = :id");
    $statement->execute([
        "username" => $username,
        "id" => $_SESSION["user_id"]
    ]);

    $_SESSION["username"] = $username;
}
if (isset($_POST["firstName"]) ){
    $firstName = $_POST["firstName"];

    $statement = $con->prepare("UPDATE User SET first_name = :firstName WHERE id = :id");
    $statement->execute([
        "firstName" => $firstName,
        "id" => $_SESSION["user_id"]
    ]);

    $_SESSION["firstName"] = $firstName;
}

if (isset($_POST["lastName"]) ){
    $lastName = $_POST["lastName"];

    $statement = $con->prepare("UPDATE User SET last_name = :lastName WHERE id = :id");
    $statement->execute([
        "lastName" => $lastName,
        "id" => $_SESSION["user_id"]
    ]);

    $_SESSION["lastName"] = $lastName;
}

if (isset($_POST["pwdHash"])) {
    $pwdHash = password_hash($_POST["pwdHash"], PASSWORD_DEFAULT);

    $statement = $con->prepare("UPDATE User SET pwd_hash = :pwdHash WHERE id = :id");
    $statement->execute([
        "pwdHash" => $pwdHash,
        "id" => $_SESSION["user_id"]
    ]);

    $_SESSION["pwdHash"] = $pwdHash;
}

if (isset($_POST["email"]) ){
    $email = $_POST["email"];

    $statement = $con->prepare("UPDATE User SET email = :email WHERE id = :id");
    $statement->execute([
        "email" => $email,
        "id" => $_SESSION["user_id"]
    ]);

    $_SESSION["email"] = $email;
}


if (isset($_POST["submit1"])) {

    $newfilepath = null;

    if (is_uploaded_file($_FILES['profilepicture']["tmp_name"])) {
        $target_dir = "assets/pictures/users/";
        $tmp_file = $_FILES['profilepicture']["tmp_name"];
        $newfilepath = $target_dir . $_FILES["profilepicture"]["name"];
        $imageFileType = strtolower(pathinfo($tmp_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profilepicture"]["tmp_name"]);
        $uploadOk = 1;
        if ($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";

            move_uploaded_file($tmp_file, $newfilepath);
            $uploadOk = 1;
        } else {
            //echo "<br> File is not an image. <br>";
            $uploadOk = 0;
        }
    } else {
        //echo "<br> empty image <br>";
    }


    //echo "this is the filepath for the database:" . $newfilepath;
    $sql = "UPDATE User SET  picturePath = :profilepicture WHERE id = :id ;";
    $sth = $con->prepare($sql);
    $sth->bindParam('profilepicture', $newfilepath);
    $sth->bindParam('id', $_SESSION["user_id"]);
    $sth->execute();

    redirectTo("myprofile.php");
}

require_once "elements/header.php";
require_once "elements/nav.php";
?>
    <script>
        function toggle(){

            document.getElementById("username").disabled = !document.getElementById("username").disabled;
        }

        function toggle2(){
            document.getElementById("firstName").disabled = !document.getElementById("firstName").disabled;

        }
        function toggle3(){
            document.getElementById("lastName").disabled = !document.getElementById("lastName").disabled;

        }
        function toggle4(){
            document.getElementById("pwdHash").disabled = !document.getElementById("pwdHash").disabled;

        }
        function toggle5(){
            document.getElementById("email").disabled = !document.getElementById("email").disabled;

        }




    </script>

<style>
    input.fileimg {
        display: flex;
        width: 40px;

        padding:40px 0 0 0;
        overflow: hidden;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background: url('assets/pictures/pencil-solid.svg') center center no-repeat #e4e4e4;
        border-radius: 50%;
        background-size: 30px 30px;
        background-color: #CA2D2D;
        background-size: 18px;
        margin-bottom: -1.5rem;
        z-index: 1;
        margin-left: 6.5rem;

    }

</style>

</style>

<div class="container rounded  bg-white mt-5 mb-5">
    <div class="row justify-content-center d-flex">
        <div class="col-md-3  d-flex align-items-center justify-content-center border-right">
            <form action="myprofile.php" method="POST" enctype="multipart/form-data" novalidate>

            <div class="d-flex flex-column justify-content-center align-items-center text-center p-3 py-5">
                <?php
                if (isset($_SESSION["username"])) {
                    require_once "elements/database.php";
                    $stmt = $con->prepare("SELECT * FROM User WHERE email = :email");
                    $stmt->execute(["email" => $_SESSION["email"]]);
                    $user = $stmt->fetch();

                    ?>
                  <input type="file"  class="fileimg form-control  fa-solid fa-pencil text-center me-4   rounded-5 " name="profilepicture" id="profilepicture"  value="" <?php  ?>required style=" width: 40px; height: 20px; ">

                    <?php
                    if (!empty($user["picturePath"])){
                        ?>
                        <img id="previewpfp" src="<?php  echo $user["picturePath"] ?>" class="img-fluid mt-0" style="border-radius: 50%; width: 120px; height: 120px; object-position: center;   object-fit:cover;">

                    <?php }else{  ?>
                        <img id="previewpfp"src="assets/pictures/users/admin.png" class="img-fluid mt-0" style="border-radius: 50%; width: 120px; height: 120px; object-position: center;   object-fit:cover;">

                    <?php  } ?>
              <?php }  ?>


                <span class="font-weight-bold " style="font-weight: bold;"><?php echo $user["username"]; ?></span><span class="text-black-50"></span><span><?php echo $user["email"]; ?> </span></div>

                <div class="  text-center">
                    <button class="submit btn btn-primary rounded-0 " name="submit1" type="submit">Update  picture</button>
                </div>
</form>
          </div>

        <div class="col-md-5 border-right">

            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">


                    <h4 class="text-right"style="font-family: 'soria'; font-size: 30px;">Customise Profile</h4>
                    <form action="myprofile.php" method="POST" enctype="multipart/form-data" novalidate>
                </div>
                <?php
                if (isset($_SESSION["username"])) {
                require_once "elements/database.php";
                $stmt = $con->prepare("SELECT * FROM User WHERE email = :email");
                $stmt->execute(["email" => $_SESSION["email"]]);
                $user = $stmt->fetch();
                ?>

                <div class="row mt-2">
                    <div class="col-md-6"><label class="labels" style="font-family: ''">Username</label>
                        <div class="d-flex">
                        <input type="text" class="form-control" id="username" name="username" style="border-radius: 0px; " disabled   placeholder="<?php echo $user["username"]; ?>" value="<?php $username ?>" >
                            <button class="btn btn-primary rounded-0 text-light" type="button" onclick="toggle()"    >
                            <i class="fa-solid fa-pencil"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="labels " for="firstName" style="font-family: ''">First Name</label>
                        <div class="d-flex">

                        <input type="text" class="form-control" name="firstName" id="firstName" style="border-radius: 0px;" disabled placeholder="<?php echo  $user["first_name"]; ?>" value=""> <button class="btn btn-primary rounded-0 text-light" type="button" onclick="toggle2()">
                                <i class="fa-solid fa-pencil"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="labels" for="lastName" style="font-family: ''">Last Name</label>
                        <div class="d-flex">
                        <input type="text" class="form-control" name="lastName" id="lastName" style="border-radius: 0px;" value="" disabled placeholder="<?php echo  $user["last_name"]; ?>">
                            <button class="btn btn-primary rounded-0 text-light" type="button" onclick="toggle3()">
                                <i class="fa-solid fa-pencil"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">  <label class="labels" for="pwdHash" style="font-family: ''" >Change Password</label>
                        <div class="d-flex">
                        <input type="password" id="pwdHash" name="pwdHash" class="form-control  "  placeholder="******"  disabled style="border-radius: 0px;" required />
                            <button class="btn btn-primary rounded-0 text-light" type="button" onclick="toggle4()">
                                <i class="fa-solid fa-pencil"></i></button>
                        </div></div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12"><label class="labels" for="email" style="font-family: ''">Email ID</label>
                        <div class="d-flex">
                        <input type="text" class="form-control" id="email" name="email" style="border-radius: 0px;" placeholder="<?php echo $_SESSION["email"];  ?>" disabled value="" <?php $email ?>>
                            <button class="btn btn-primary rounded-0 text-light" type="button" onclick="toggle5()">
                                <i class="fa-solid fa-pencil"></i></button>

                        </div>  <hint class="opacity-75 text-muted" style="font-size: 13px;" >The email should be the school-email: @htl-shkoder.com</hint></div>
                    <?php }?>
                </div>
                <div class="mt-5 text-center">
                    <button class="submit btn btn-primary rounded-0 " name="submit" type="submit">Update Profile</button>
                   </div>

            </div>

        </div>
</form>
    </div>
</div>
</div>
</div>


<?php
require_once "elements/footer.php";
?>


<script>
    const fileInput = document.getElementById('profilepicture');
    const imagePreview = document.getElementById('previewpfp');
    const imgsrc =

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
            imagePreview.src = <?php echo  $user["picturePath"];?>
        }
    });


</script>