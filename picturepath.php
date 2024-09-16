<?php
require_once "elements/header.php";
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
            echo "File is an image - " . $check["mime"] . ".";

            move_uploaded_file($tmp_file, $filepath);
            $uploadOk = 1;
        } else {

            echo "<br> File is not an image. <br>";

            $uploadOk = 0;
        }
    } else {

        echo "<br> empty image <br>";
    }
    echo "this is the filepath for the database:" . $filepath;
}


?>

<form action="picturepath.php" method="POST" enctype="multipart/form-data">

    <div>

        <label for="profilepicture" class=>Laden Sie ein Bild hoch.</label>

        <input type="file" class="form-control" name="profilepicture" id="profilepicture">

    </div>

    <div>

        <button type="submit" name="submit" class="btn btn-primary">Upload</button>

    </div>

</form>
