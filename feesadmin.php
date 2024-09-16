<?php
session_start();


require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";
require_once "notification_system.php";

try {
    $sqlGetOverdueBorrowings = "CALL GetOverdueBorrowings()";
    $resultGetOverdueBorrowings = $con->query($sqlGetOverdueBorrowings);
    $rows = $resultGetOverdueBorrowings->fetchAll(PDO::FETCH_ASSOC);
    $resultGetOverdueBorrowings->closeCursor();
} catch (PDOException $e) {
    echo "PDO Exception: " . $e->getMessage();
}
if (!empty($rows)) {
    foreach ($rows as $row) {
        $borrowing_id = $row["borrowing_id"];
        $check_sql = "SELECT COUNT(*) FROM Fees WHERE borrowing_id = :borrowing_id";
        $stmt = $con->prepare($check_sql);
        $stmt->bindParam(':borrowing_id', $borrowing_id);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {

            $return_date = $row["ReturnDate"];
            try {
                try {
                    $stmt_insert_fee = $con->prepare("CALL InsertFee(?, ?)");
                    $stmt_insert_fee->bindParam(1, $borrowing_id, PDO::PARAM_INT);
                    $stmt_insert_fee->bindParam(2, $return_date, PDO::PARAM_STR);
                    $stmt_insert_fee->execute();
                    $stmt_insert_fee->closeCursor();
                } catch (PDOException $e) {
                    echo "Error calling InsertFee: " . $e->getMessage();
                }
                try {
                    // Fetch user information for sending email
                    $stmt_fetch_user = $con->prepare("SELECT u.first_name, u.last_name, u.email FROM User u
                                      JOIN Borrowing b ON u.id = b.user_id
                                      WHERE b.id = :borrowing_id");
                    $stmt_fetch_user->execute([
                        "borrowing_id" => $borrowing_id
                    ]);

                    $user_info = $stmt_fetch_user->fetch(PDO::FETCH_ASSOC);

                    $to = $user_info['email'];
                    $firstName = $user_info['first_name'];
                    $lastName = $user_info['last_name'];
                    $message_text = "You have an overdue transaction. Please return the book as soon as possible.";

                    sendEmail($to, $firstName, $lastName, 'Unreturned Book', $message_text);
                    redirectTo("feesadmin.php");
                } catch (PDOException $e) {
                    echo "Error sending email: " . $e->getMessage();
                }
            } catch (PDOException $e) {
                // echo "Error inserting fee: " . $e->getMessage();
            }


        } else {
            // echo "Record for borrowing ID: $borrowing_id already exists in the Fees table.<br>";
        }
    }
} else {
    // echo "No overdue transactions found.";
}

if (isset($_POST["edit"])) {
    $daysPast = $_POST["daysPast"];
    try {
        $id = $_POST["id"];
        $sql = "CALL UpdateFeeDaysPast(:fee_id, :days_past)";
        $sth = $con->prepare($sql);
        $sth->bindParam(':fee_id', $id, PDO::PARAM_INT);
        $sth->bindParam(':days_past', $daysPast, PDO::PARAM_INT);
        $sth->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

/*
if (isset($_GET["toDel"])) {
    $id = $_GET["toDel"];

    try {
        $sql = "UPDATE Fees SET isPayed = 1 WHERE id = :id";
        $sth = $con->prepare($sql);
        $sth->bindParam('id', $id);
        $sth->execute();

        $sql_update_borrowing = "UPDATE Borrowing SET finished = 1, active = 0 WHERE id = (
            SELECT borrowing_id FROM Fees WHERE id = :id
        )";
        $sth_update_borrowing = $con->prepare($sql_update_borrowing);
        $sth_update_borrowing->bindParam('id', $id);
        $sth_update_borrowing->execute();
        redirectTo("feesadmin.php");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

}*/
if (isset($_GET["toDel"])) {
    $id = $_GET["toDel"];

    try {
        // Update Fee
        try{
            $sql_update_fee = "CALL DeleteFee(:fee_id)";
            $sth_update_fee = $con->prepare($sql_update_fee);
            $sth_update_fee->bindParam(':fee_id', $id, PDO::PARAM_INT);
            $sth_update_fee->execute();

            $sql_update_borrowing = "UPDATE Borrowing SET finished = 1, active = 0 WHERE id = (
            SELECT borrowing_id FROM Fees WHERE id = :id
        )";
            $sth_update_borrowing = $con->prepare($sql_update_borrowing);
            $sth_update_borrowing->bindParam('id', $id);
            $sth_update_borrowing->execute();
            redirectTo("feesadmin.php");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        redirectTo("feesadmin.php");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if (isset($_POST["fineRate"])) {
    $fineRate = $_POST["fineRate"];
    $_SESSION['fineRate'] = $fineRate;
    try {
        $updateFineGradeSql = "CALL UpdateFineGrade(:fine_rate)";
        $updateFineGradeStmt = $con->prepare($updateFineGradeSql);
        $updateFineGradeStmt->bindParam(':fine_rate', $fineRate, PDO::PARAM_STR);
        $updateFineGradeStmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


require_once "elements/header.php";
require_once "elements/nav.php";

/*
$sql_fees = "SELECT u.id as User_id, u.username as Username, b.id as borrowing_id,
            b.return_date as return_date, f.id as id, f.fine as fine, f.daysPast as daysPast,
            f.fineGrade as fineGrade, bo.title as Title
             FROM User u
             JOIN Borrowing b ON u.id = b.user_id
             JOIN Book bo ON b.isbn = bo.isbn
             JOIN Fees f ON b.id = f.borrowing_id
             WHERE f.isPayed = 0
             ORDER BY return_date desc";

$result_fees = $con->query($sql_fees);

$fees = $result_fees->fetchAll(PDO::FETCH_ASSOC);*/
try{
$sql_fees = "CALL GetUnpaidFees()";
$result_fees = $con->query($sql_fees);
$fees = $result_fees->fetchAll(PDO::FETCH_ASSOC);
}catch (PDOException $e) {
    echo $e->getMessage();
}
?>
    <div class="container">
        <div class="mt-5 d-flex justify-content-between"><b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
        </div>
        <h1 style="font-family: 'Soria'; font-size: 40pt;" class="text-center">Edit Fees</h1>

        <div class="mb-4">
            <h4 style="font-family: 'Soria'; ">Set Fine Rate</h4>
            <form action="feesadmin.php" method="post" class="d-flex justify-content-center align-content-center">
                <div class="col-md-10">
                    <div class="form-group">
                        <input type="number" class="form-control" style="font-family: 'Soria'; " id="fineRate"
                               name="fineRate" step="0.01"
                               placeholder="2.00" required>
                    </div>
                </div>
                <div class="col-md-2 d-flex justify-content-center">
                    <button type="submit" class="btn btn-outline-primary rounded-0 me-2"
                            style="font-family: 'Prociono TT'">
                        Set Fine
                    </button>
                </div>
            </form>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">User</th>
                <th scope="col">Title</th>
                <th scope="col">Fine</th>
                <th scope="col">Fine Rate</th>
                <th scope="col">Return Date</th>
                <th scope="col">Days Past</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($fees as $fee) {
                $fine = $fee["fineGrade"] * $fee["daysPast"];

                $update_sql = "UPDATE Fees SET fine = :fine WHERE id = :id";
                $update_stmt = $con->prepare($update_sql);
                $update_stmt->bindParam(':fine', $fine);
                $update_stmt->bindParam(':id', $fee['id']);
                $update_stmt->execute();

                $fee["fine"] = $fine;

                echo "<tr>";
                echo "<td>" . $fee["Username"] . "</td>";
                echo "<td>" . $fee["Title"] . "</td>";
                echo "<td>" . number_format($fee["fine"], 2) . "</td>";
                echo "<td>" . $fee["fineGrade"] . "</td>";
                echo "<td>" . $fee["return_date"] . "</td>";
                echo "<td>" . $fee["daysPast"] . "</td>";

                ?>
                <td>
                    <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-info<?php echo $fee["id"] ?>" style="height: 40px; width: 40px;">
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                </td>
                <td>
                    <a href="feesadmin.php?toDel=<?php echo $fee['id']; ?>"
                       class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center"
                       style="height: 40px; width: 40px;">
                        <i class="fa-solid fa-check"></i>
                    </a>
                </td>
                <div class="modal fade" id="modal-info<?php echo $fee["id"] ?>" tabindex="-1"
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
                                <form action="feesadmin.php" method="POST">
                                    <div class="container">
                                        <div class="row">
                                            <input type="hidden" class="form-control" name="id"
                                                   value="<?php echo $fee['id'] ?>">
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
                echo "</tr>";
            }
            ?>

            </tbody>
        </table>
    </div>
<?php
require_once "elements/footer.php";

?>