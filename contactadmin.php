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
        $sql = "CALL DeleteNotification(:notification_id)";
        $sth = $con->prepare($sql);
        $sth->bindParam(':notification_id', $id, PDO::PARAM_INT);
        $sth->execute();
        redirectTo("contactadmin.php");
        exit();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

}

require_once "elements/header.php";
require_once "elements/nav.php";
?>

<div class="container">
    <div class="mt-5 d-flex justify-content-between"><b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
    </div>
    <h1 style="font-family: 'Soria'; font-size: 40pt;" class="text-center">Edit Contact Messages</h1>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Message</th>
            <th scope="col">Send Date</th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php

        try {
            $sql = "CALL SelectNotifications()";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Handle any errors
            echo $e->getMessage();
        }

        foreach ($result as $item) {
            ?>

            <tr>
                <td><?php echo $item['Username']; ?></td>
                <td><?php echo $item['Email']; ?></td>
                <td><?php echo $item['Message']; ?></td>
                <td><?php echo $item['Sent_date']; ?></td>
                <td>
                    <a class= "btn btn-outline-danger rounded-circle" href="contactadmin.php?toDel=<?php echo $item['NotificationID']; ?>">
                        <i class="bi bi-trash3-fill " ></i>
                    </a>
                </td>
                <td>
                    <button class="btn btn-danger " data-bs-toggle="modal"
                            data-bs-target="#reply<?php echo $item["NotificationID"] ?>">
                        Reply
                    </button>
                </td>

            </tr>
            <!-- Modal for replying to messages -->
            <div class="modal fade" id="reply<?php echo $item["NotificationID"] ?>" tabindex="-1"
                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="replyModalLabel<?php echo $item['NotificationID']; ?>">Reply to <?php echo $item['Username']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="reply.php" method="POST">
                                <div class="mb-3">
                                    <label for="replyMessage" class="form-label">Your Message:</label>
                                    <textarea class="form-control" id="replyMessage" name="replyMessage" rows="5"></textarea>
                                    <input type="hidden" name="recipientEmail" value="<?php echo $item['Email']; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Send Reply</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <?php
        }
        ?>
        </tbody>
    </table>
</div>
<?php
require_once "elements/modal.php";

if (isset($_GET['sent_email']) && $_GET['sent_email'] == 'true') {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Email sent successfully!", "success");
    unset($_GET['sent_email']);
}

require_once "elements/footer.php";
?>

