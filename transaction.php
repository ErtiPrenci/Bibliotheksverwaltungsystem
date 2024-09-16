<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";

$stmt_borrowings_not_active = $con->prepare(
    "SELECT * 
                FROM Book b join Borrowing t
                ON b.isbn = t.isbn
                join User u
                ON t.user_id = u.id
                WHERE t.active = 0 AND finished != 1");
$stmt_borrowings_not_active->execute();
$borrowings_not_active = $stmt_borrowings_not_active->fetchAll(PDO::FETCH_ASSOC);

$stmt_borrowings_active = $con->prepare(
    "SELECT * 
                FROM Book b join Borrowing t
                ON b.isbn = t.isbn
                join User u
                ON t.user_id = u.id
                WHERE t.active = 1");
$stmt_borrowings_active->execute();
$borrowings_active = $stmt_borrowings_active->fetchAll(PDO::FETCH_ASSOC);

/*echo "<pre>";
echo var_export($borrowings_not_active);
echo "</pre>";*/

if (isset($_POST["return_date"])) {
    $stmt_change_date = $con->prepare("UPDATE Borrowing SET return_date = :return_date WHERE isbn = :isbn AND user_id = :user_id");
    $stmt_change_date->execute([
        "return_date" => $_POST["return_date"],
        "isbn" => $_POST["isbn"],
        "user_id" => $_POST["user_id"]
    ]);

    redirectTo("transaction.php");
    $stmt_fetch_borrowings = $con->prepare("SELECT * FROM Borrowing b JOIN User u ON b.user_id = u.id WHERE b.finished != 1");
    $stmt_fetch_borrowings->execute();
    $borrowings = $stmt_fetch_borrowings->fetchAll(PDO::FETCH_ASSOC);

    // Iterate through borrowings to send email reminders
    foreach ($borrowings as $borrowing) {
        $due_date = strtotime($borrowing['return_date']);
        $two_days_before_due = strtotime('-2 days', $due_date);
        $current_date = time();

        if ($current_date >= $two_days_before_due && $current_date < $due_date) {
            $to = $borrowing['email']; // Assuming you have an 'email' column in your 'User' table
            $firstName = $borrowing['first_name'];
            $lastName = $borrowing['last_name'];
            $message_text = "Your book return date is approaching. Please make sure to return the book on time.";

            sendEmail($to, $firstName, $lastName, $message_text);
            echo "Email successfully";
        }
    }
}

require_once "elements/header.php";
require_once "elements/nav.php";
?>

    <header class="text-center my-5">
        <div class="container">
            <div class="mt-5 d-flex justify-content-between">
                <b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
            </div>
            <h1 style="font-family: 'Soria'; font-size: 40pt;">Book Borrowings and Transactions</h1>
        </div>
    </header>

    <main class="container mb-4">
        <div class="mb-5">
            <h3 class="mb-3" style="font-family: 'Soria'; font-size: 26pt">Waiting for Confirmation</h3>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Book Cover</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Title</th>
                    <th scope="col">Username</th>
                    <th scope="col">Name</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($borrowings_not_active as $borrow_not_active) { ?>
                    <tr>
                        <td>
                            <img src="<?php echo $borrow_not_active["picture_path"] ?>"
                                 alt="<?php echo $borrow_not_active["title"] ?>" class="img-fluid"
                                 style="height: 8rem; width: 5.5rem;">
                        </td>
                        <td><?php echo $borrow_not_active["isbn"] ?></td>
                        <td><?php echo $borrow_not_active["title"] ?></td>
                        <td><?php echo $borrow_not_active["username"] ?></td>
                        <td><?php echo $borrow_not_active["first_name"] . " " . $borrow_not_active["last_name"] ?></td>
                        <td>
                            <a href="approve_borrowing.php?isbn=<?php echo $borrow_not_active["isbn"] . "&userid=" . $borrow_not_active["user_id"] ?>"
                               class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                               style="height: 40px; width: 40px;">
                                <i class="fa-solid fa-check"></i>
                            </a>
                        </td>
                        <td>
                            <a href="deny_borrowing.php?isbn=<?php echo $borrow_not_active["isbn"] . "&userid=" . $borrow_not_active["user_id"] ?>"
                               class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center"
                               style="height: 40px; width: 40px;">
                                <i class="fa-solid fa-x"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div>
            <h3 class="mb-3" style="font-family: 'Soria'; font-size: 26pt">Active Transactions</h3>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Book Cover</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Title</th>
                    <th scope="col">Username</th>
                    <th scope="col">Name</th>
                    <th scope="col">Borrow Date</th>
                    <th scope="col">Return Date</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
                </thead>

                <tbody>
                <?php $cnt = 0;
                foreach ($borrowings_active as $borrow_active) { ?>
                    <tr>
                        <td>
                            <img src="<?php echo $borrow_active["picture_path"] ?>"
                                 alt="<?php echo $borrow_active["title"] ?>" class="img-fluid"
                                 style="height: 8rem; width: 5.5rem;">
                        </td>
                        <td><?php echo $borrow_active["isbn"] ?></td>
                        <td><?php echo $borrow_active["title"] ?></td>
                        <td><?php echo $borrow_active["username"] ?></td>
                        <td><?php echo $borrow_active["first_name"] . " " . $borrow_active["last_name"] ?></td>
                        <td><?php echo $borrow_active["borrow_date"] ?></td>
                        <td><?php echo $borrow_active["return_date"] ?></td>
                        <td>
                            <button id="transaction_edit_button" type="button"
                                    class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                                    data-bs-toggle="modal" data-bs-target="#transaction_edit_modal<?php echo $cnt ?>"
                                    style="height: 40px; width: 40px;">
                                <i class="fa-solid fa-pencil"></i>
                            </button>
                        </td>
                        <td>
                            <a href="finish_transaction.php?isbn=<?php echo $borrow_active["isbn"] . "&userid=" . $borrow_active["user_id"] ?>"
                               class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center"
                               style="height: 40px; width: 40px;">
                                <i class="fa-solid fa-check"></i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="transaction_edit_modal<?php echo $cnt ?>" tabindex="-1"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" style="font-family: 'Prociono TT'"
                                        id="exampleModalLabel">Change Return Date</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                                        <div class="mb-3">
                                            <label for="return_date" style="font-family: 'Prociono TT'"
                                                   class="form-label">Date</label>
                                            <input type="text" class="form-control rounded-0"
                                                   style="font-family: 'Prociono TT'" id="return_date"
                                                   name="return_date" aria-describedby="return_date_help">
                                            <div id="return_date_help" class="form-text">
                                                <p class="mb-0">Please maintain the format of the date and time present
                                                    in the table.</p>
                                                <p>'YYYY-MM-DD hh-mm-ss'</p>
                                            </div>
                                            <input type="hidden" id="isbn" name="isbn"
                                                   value="<?php echo $borrow_active["isbn"] ?>">
                                            <input type="hidden" id="user_id" name="user_id"
                                                   value="<?php echo $borrow_active["user_id"] ?>">
                                        </div>
                                        <hr class="border border-primary border-muted">
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" style="font-family: 'Prociono TT'"
                                                    class="btn btn-outline-primary rounded-0 me-2">Change
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
                    <?php $cnt++;
                } ?>
                </tbody>
            </table>
        </div>
    </main>

<?php
require_once "elements/footer.php";
?>