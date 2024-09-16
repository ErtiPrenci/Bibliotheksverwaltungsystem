<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";
require_once "notification_system.php";

if (isset($_GET["toDel"])) {
    $id = $_GET["toDel"];
    try {
        $sql = "DELETE FROM Fees WHERE id = :id";
        $sth = $con->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->execute();
        redirectTo("feeadmin.php");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["fineRate"])) {
    $fineRate = $_POST["fineRate"];
    $_SESSION['fineRate'] = $fineRate;
    try {
        $updateFineGradeSql = "UPDATE Fees SET fineGrade = :fineRate";
        $updateFineGradeStmt = $con->prepare($updateFineGradeSql);
        $updateFineGradeStmt->bindParam('fineRate', $fineRate);
        $updateFineGradeStmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if (isset($_POST["edit"])) {
    $daysPast = $_POST["daysPast"];
    try {
        $id = $_POST["id"];
        $sql = "UPDATE Fees SET daysPast = :daysPast WHERE id = :id";
        $sth = $con->prepare($sql);
        $sth->bindParam('daysPast', $daysPast);
        $sth->bindParam('id', $id);
        $sth->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
require_once "elements/header.php";
?>
<!--
<style>
    body {
        background-color: #efedee;
    }
</style>
!-->
<?php
require_once "elements/nav.php";
?>
<div class="container">
    <div class="mt-5 d-flex justify-content-between"><b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
    </div>
    <h1 style="font-family: 'Soria'; font-size: 40pt;" class="text-center">Edit Fees</h1>

    <div class="mb-4">
        <h4 style="font-family: 'Soria'; ">Set Fine Rate</h4>
        <form action="feeadmin.php" method="post" class="d-flex justify-content-center align-content-center">
            <div class="col-md-10">
                <div class="form-group">
                    <input type="number" class="form-control" style="font-family: 'Soria'; " id="fineRate"
                           name="fineRate" step="0.01"
                           placeholder="2.00" required>
                </div>
            </div>
            <div class="col-md-2 d-flex justify-content-center">
                <button type="submit" class="btn btn-outline-primary rounded-0 me-2" style="font-family: 'Prociono TT'">
                    Set Fine
                </button>
            </div>
        </form>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Username</th>
            <th scope="col">ReturnDate</th>
            <th scope="col">Fine</th>
            <th scope="col">DaysPast</th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        try {

            /*$sql = "SELECT u.id, u.username as Username, b.id as borrowing_id,
            b.return_date as ReturnDate, f.id as Fees_id, f.fine as Fine, f.daysPast as DaysPast, f.fineGrade as fineGrade
            FROM User u
            JOIN Borrowing b ON u.id = b.user_id
            JOIN Fees f ON b.id = f.borrowing_id
            WHERE b.active = 1 AND f.daysPast > 0
            ORDER BY ReturnDate asc";
*/
            $sql= "select u.id, u.username as Username, b.id as borrowing_id, b.return_date as ReturnDate
                   from User u
                   join Borrowing b
                   on u.id = b.user_id
                   where b.active = 1 and DATEDIFF(CURRENT_DATE(), b.return_date) > 0
                   ORDER BY ReturnDate asc";
            $stmt = $con->prepare($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
            $stmt = false;
        }
        if ($stmt) {
            try {
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch (PDOException $e) {
                echo $e->getMessage();
            }

            echo "<pre>";
            var_dump($result);
            echo "</pre>";

            foreach ($result as $item) {
                $daysPast = $item['DaysPast'];
                $fine = $daysPast * $item['fineGrade'];

                try {
                    if (empty($item['Fees_id'])) {
                        $insertFeesSql = "INSERT INTO Fees (borrowing_id, fine, daysPast, fineGrade) VALUES (:borrowing_id, :fine, :daysPast, :fineGrade)";
                        $insertFeesStmt = $con->prepare($insertFeesSql);
                        $insertFeesStmt->bindParam('borrowing_id', $item['borrowing_id']);
                        $insertFeesStmt->bindParam('fine', $fine);
                        $insertFeesStmt->bindParam('daysPast', $daysPast);
                        $insertFeesStmt->bindParam('fineGrade', $item['fineGrade']);
                        $insertFeesStmt->execute();
                    } else {
                        $updateFineSql = "UPDATE Fees SET fine = :fine WHERE id = :feesId";
                        $updateFineStmt = $con->prepare($updateFineSql);
                        $updateFineStmt->bindParam('fine', $fine);
                        $updateFineStmt->bindParam('feesId', $item['Fees_id']);
                        $updateFineStmt->execute();
                    }
                    if (isset($_GET["userid"])) {
                        $user_id = $_GET["userid"];

                        $stmt_check_fees = $con->prepare("SELECT COUNT(*) as fees_count FROM Fees WHERE user_id = :user_id");

                        if ($stmt_check_fees) {
                            $stmt_check_fees->execute(["user_id" => $user_id]);


                            if ($fees_count > 0) {
                                $stmt_fetch_user = $con->prepare("SELECT first_name, last_name, email FROM User WHERE id = :user_id");
                                $stmt_fetch_user->execute(["user_id" => $user_id]);

                                $user_info = $stmt_fetch_user->fetch(PDO::FETCH_ASSOC);

                                $to = $user_info['email'];
                                $firstName = $user_info['first_name'];
                                $lastName = $user_info['last_name'];
                                $message_text = "You have outstanding fees. Please make sure to clear them to avoid any issues.";

                                sendEmail($to, $firstName, $lastName, $message_text);
                            }
                        } else {
                            echo "Error preparing SQL statement.";
                        }
                    }
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }

                ?>

                <tr>
                    <td><?php echo $item['Username']; ?></td>
                    <td><?php echo $item['ReturnDate']; ?></td>
                    <td><?php echo $item['Fine']; ?></td>
                    <td><?php echo $item['DaysPast']; ?></td>
                    <td>
                        <a href="feeadmin.php?toDel=<?php echo $item['Fees_id']; ?>"
                           class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                           style="height: 40px; width: 40px;">
                            <i class="fa-solid fa-check"></i>
                        </a>
                    </td>
                    <td>
                        <button class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modal-info<?php echo $item["Fees_id"] ?>">
                            Edit
                        </button>
                    </td>
                </tr>
                <div class="modal fade" id="modal-info<?php echo $item["Fees_id"] ?>" tabindex="-1"
                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" style="font-family: 'Prociono TT'" id="exampleModalLabel">
                                    Edit Fees</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="feeadmin.php" method="POST">
                                    <div class="container">
                                        <div class="row">
                                            <input type="hidden" class="form-control" name="id"
                                                   value="<?php echo $item['Fees_id'] ?>">
                                        </div>
                                        <div class="row">
                                            <div class="mb-3">
                                                <label for="category" class="form-label"
                                                       style="font-family: 'Prociono TT'">DaysPast</label>
                                                <input type="text" class="form-control" name="daysPast" id="daysPast"
                                                       aria-describedby="emailHelp">
                                            </div>
                                        </div>
                                        <hr class="border border-primary border-muted">
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" name="edit"
                                                    class="btn btn-outline-primary rounded-0 me-2"
                                                    style="font-family: 'Prociono TT'">Change
                                            </button>
                                            <button type="button" style="font-family: 'Prociono TT'"
                                                    class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close
                                            </button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>
<?php
require_once "elements/footer.php";

?>
