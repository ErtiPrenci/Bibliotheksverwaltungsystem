<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";

if (isset($_GET["toDel"])) {
    $id = $_GET["toDel"];
    try {
        $sql = "CALL DeleteEvent(:event_id)";
        $sth = $con->prepare($sql);
        $sth->bindParam(':event_id', $id);
        $sth->execute();
        redirectTo("eventadmin.php");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if (isset($_POST["edit"])) {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $id = $_POST["id"];

    try {
        $sql = "CALL UpdateEvent(:event_id, :new_title, :new_description, :new_date, :new_time)";
        $sth = $con->prepare($sql);
        $sth->bindParam(':event_id', $id);
        $sth->bindParam(':new_title', $title);
        $sth->bindParam(':new_description', $description);
        $sth->bindParam(':new_date', $date);
        $sth->bindParam(':new_time', $time);
    $sth->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if (isset($_POST["add"])) {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $filepath = null;

    if (is_uploaded_file($_FILES['profilepicture']["tmp_name"])) {
        $target_dir = "assets/pictures/users/";
        $tmp_file = $_FILES['profilepicture']["tmp_name"];
        $filename = $_FILES["profilepicture"]["name"];
        $filename = str_replace(' ', '_', $filename);
        $filepath = $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($tmp_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profilepicture"]["tmp_name"]);
        $uploadOk = 1;
        if ($check !== false) {
            move_uploaded_file($tmp_file, $filepath);
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
    }


    try {
        $sql = "CALL InsertEvent(:event_title, :event_description, :event_date, :event_time, :event_picturepath)";
        $sth = $con->prepare($sql);
        $sth->bindParam(':event_title', $title);
        $sth->bindParam(':event_description', $description);
        $sth->bindParam(':event_date', $date);
        $sth->bindParam(':event_time', $time);
        $sth->bindParam(':event_picturepath', $filepath);
        $sth->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if (isset($_POST["change-image"])) {
    $id = $_POST["id"];
    $filepath = null;

    if (isset($_FILES['profilepicture2']) && is_uploaded_file($_FILES['profilepicture2']["tmp_name"])) {
        $target_dir = "assets/pictures/users/";
        $tmp_file = $_FILES['profilepicture2']["tmp_name"];
        $filename = $_FILES["profilepicture2"]["name"];
        $filename = str_replace(' ', '_', $filename); 
        $filepath = $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($tmp_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profilepicture2"]["tmp_name"]);
        $uploadOk = 1;
        if ($check !== false) {
            move_uploaded_file($tmp_file, $filepath);
            $uploadOk = 1;

            $sth = $con->prepare("CALL UpdateEventPicturePath(:event_id, :event_picturepath)");
            $sth->bindParam(':event_id', $id);
            $sth->bindParam(':event_picturepath', $filepath);
            $sth->execute();
        } else {
            $uploadOk = 0;
        }
    }

}

require_once "elements/header.php";
?>
<style>
    body {
        background-color: #efedee;
    }
</style>
<?php
require_once "elements/nav.php";
?>
<div class="container">
    <div class="mt-5 d-flex justify-content-between"><b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
    </div>
    <h1 style="font-family: 'Soria'; font-size: 40pt;" class="text-center">Edit Event</h1>
    <button class="btn btn-primary rounded-0 me-2 d-flex justify-content-center mb-2" style="font-family: 'Prociono TT'"
            data-bs-toggle="modal" data-bs-target="#modal-add-event">
        Add Event
    </button>

    <div class="modal fade" id="modal-add-event" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" style="font-family: 'Prociono TT'" id="exampleModalLabel">Add
                        Event</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="eventadmin.php" method="POST" enctype="multipart/form-data">
                        <div class="container">
                            <div class="row">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="category" style="font-family: 'Prociono TT'" class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" id="title"
                                               aria-describedby="emailHelp">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3">
                                        <label for="category" style="font-family: 'Prociono TT'" class="form-label">Description</label>
                                        <input type="text" class="form-control" name="description" id="description"
                                               aria-describedby="emailHelp">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="category" style="font-family: 'Prociono TT'"
                                               class="form-label">Date</label>
                                        <input type="text" class="form-control" name="date" id="date"
                                               aria-describedby="emailHelp">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3">
                                        <label for="category" style="font-family: 'Prociono TT'"
                                               class="form-label">Time</label>
                                        <input type="text" class="form-control" name="time" id="time"
                                               aria-describedby="emailHelp">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3">
                                        <input type="file" class="fileimg form-control text-center me-4   rounded-0 "
                                               name="profilepicture" id="profilepicture"
                                               style=" width: 107px; height: 37px">
                                    </div>
                                </div>
                            </div>
                            <hr class="border border-primary border-muted">
                            <div class="d-flex justify-content-end">
                                <button type="submit" name="add" class="btn btn-outline-primary rounded-0 me-2"
                                        style="font-family: 'Prociono TT'">Add Event
                                </button>
                                <button type="button" style="font-family: 'Prociono TT'"
                                        class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Event</th>
            <th scope="col">Description</th>
            <th scope="col">Date</th>
            <th scope="col">Time</th>
            <th scope="col">Picture</th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php

        try {
            $sql = "CALL SelectEventsAdmin()";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            // Handle PDOException
            echo "PDO Exception: " . $e->getMessage();
            // You can log the error or display a more user-friendly message
        }

        $cnt = 0;

        foreach ($result as $item) { ?>

            <tr>
                <td><?php echo $item['title']; ?></td>
                <td><?php echo $item['description']; ?></td>
                <td><?php echo $item['date']; ?></td>
                <td><?php echo $item['time']; ?></td>
                <td><?php echo $item['picturepath']; ?></td>
                <td>
                    <button class="btn btn-danger rounded-circle  " data-bs-toggle="modal"
                            data-bs-target="#modal-info<?php echo $item["id"] ?>">
                        <i class="fa-solid fa-pencil  rounded-circle"></i>
                    </button>
                </td>
                <td>
                    <a class="btn btn-outline-danger rounded-circle"
                       href="eventadmin.php?toDel=<?php echo $item['id']; ?>">
                        <i class="bi bi-trash3-fill "></i>
                    </a>
                </td>
                <td>
                    <button class="btn btn-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#modal-change-picture<?php echo $item["id"] ?>">
                        <i class="bi bi-image"></i>
                    </button>
                </td>
            </tr>
            <div class="modal fade" id="modal-info<?php echo $item["id"] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" style="font-family: 'Prociono TT'" id="exampleModalLabel">Edit Event</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="eventadmin.php" method="POST">
                                <div class="container">
                                    <div class="row">
                                        <input type="hidden" class="form-control" name="id"
                                               value="<?php echo $item['id'] ?>">
                                    </div>
                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="category" style="font-family: 'Prociono TT'" class="form-label">Title</label>
                                            <input type="text" class="form-control" name="title" id="title"
                                                   aria-describedby="emailHelp">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="category" style="font-family: 'Prociono TT'" class="form-label">Description</label>
                                            <input type="text" class="form-control" name="description" id="description"
                                                   aria-describedby="emailHelp">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="category" style="font-family: 'Prociono TT'" class="form-label">Date</label>
                                            <input type="text" class="form-control" name="date" id="date"
                                                   aria-describedby="emailHelp">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="category" style="font-family: 'Prociono TT'" class="form-label">Time</label>
                                            <input type="text" class="form-control" name="time" id="time"
                                                   aria-describedby="emailHelp">
                                        </div>
                                    </div>

                                    <hr class="border border-primary border-muted">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" name="edit" class="btn btn-outline-primary rounded-0 me-2"
                                                style="font-family: 'Prociono TT'">Change
                                        </button>
                                        <br>
                                        <button type="button" style="font-family: 'Prociono TT'"
                                                class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php $cnt++; } ?>
        </tbody>
    </table>
</div>

<?php foreach ($result as $item) { ?>
<div class="modal fade" id="modal-change-picture<?php echo $item["id"]?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" style="font-family: 'Prociono TT'" id="exampleModalLabel">Change Picture</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="eventadmin.php" method="POST" enctype="multipart/form-data">
                    <div class="container">
                        <div class="row">
                            <div class="row">
                                <div class="mb-3">
                                    <input type="file" class="fileimg form-control text-center me-4  rounded-0 "
                                           name="profilepicture2" id="profilepicture2" style=" width: 107px; height: 37px">
                                </div>
                                <input type="hidden" value="<?php echo $item["id"]?>" name="id">
                            </div>
                        </div>
                        <hr class="border border-primary border-muted">
                        <div class="d-flex justify-content-end">
                            <button type="submit" name="change-image" class="btn btn-outline-primary rounded-0 me-2"
                                    style="font-family: 'Prociono TT'">Change
                            </button>
                            <button type="button" style="font-family: 'Prociono TT'"
                                    class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <?php } ?>

<?php
require_once "elements/footer.php";
?>
