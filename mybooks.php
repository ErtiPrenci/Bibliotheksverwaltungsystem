<?php
session_start();

require_once "elements/redirect.php";
require_once "elements/database.php";

if (!isset($_SESSION["user_id"])) {
    session_destroy();
    redirectTo("index.php");
}

$user_id = $_SESSION["user_id"];

try {
    $stmt_rented_books = $con->prepare("CALL GetRentedBooks(:user_id)");
    $stmt_rented_books->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_rented_books->execute();
    $rented_books = $stmt_rented_books->fetchAll(PDO::FETCH_ASSOC);
    $stmt_rented_books->closeCursor();
} catch (PDOException $e) {
    echo "PDO Exception: " . $e->getMessage();
}

try {
    $stmt_rented_history = $con->prepare("CALL GetRentedHistory(:user_id)");
    $stmt_rented_history->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_rented_history->execute();
    $rented_history = $stmt_rented_history->fetchAll(PDO::FETCH_ASSOC);
    $stmt_rented_history->closeCursor();
} catch (PDOException $e) {
    echo "PDO Exception: " . $e->getMessage();
}

try {
    $stmt_favorite_books = $con->prepare("CALL GetFavoriteBooks(:user_id)");
    $stmt_favorite_books->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_favorite_books->execute();
    $favorite_books = $stmt_favorite_books->fetchAll(PDO::FETCH_ASSOC);
    $stmt_favorite_books->closeCursor();
} catch (PDOException $e) {
    echo "PDO Exception: " . $e->getMessage();
}

try{
    $stmt_borrowings = $con->prepare("CALL GetPendingBorrowings(:user_id)");
    $stmt_borrowings->bindParam(':user_id', $user_id);
    $stmt_borrowings->execute();

    $borrowings = $stmt_borrowings->fetchAll(PDO::FETCH_ASSOC);
    $stmt_borrowings->closeCursor();
}catch (PDOException $e) {
    echo "PDO Exception: " . $e->getMessage();
}

if(isset($_GET["isbn"])) {
    $stmt_cancel_pending = $con->prepare("DELETE FROM Borrowing where isbn = :isbn AND user_id = :user_id");
    $stmt_cancel_pending->execute([
            "isbn" => $_GET["isbn"],
        "user_id" => $_SESSION["user_id"]
    ]);

    redirectTo("mybooks.php");
}
/*
if(isset($_GET["isbn"])) {
    try{
    $stmt_cancel_pending = $con->prepare("CALL CancelPendingBorrowing(:isbn, :user_id)");
    $stmt_cancel_pending->execute([
        "isbn" => $_GET["isbn"],
        "user_id" => $_SESSION["user_id"]
    ]);

    redirectTo("mybooks.php");
    }catch (PDOException $e) {
        echo "PDO Exception: " . $e->getMessage();
    }
}
*/
require_once "elements/header.php";
?>
<style>
    .pt-3-5 {
        padding-top: 1.25rem;
    }

    .card-body {
        position: absolute;
        width: 100%;
        height: 70%;
        top: 30%;
        overflow: hidden;
        background-color: transparent;
        -webkit-transition: all 1s;
        -o-transition: all 1s;
        transition: all 1s;
    }

    .closed {
        top: 100%;
        height: 10.5rem;
        margin-top: -10.5rem;
    }

    .book-cover {
        overflow: hidden;
        padding-bottom: 56.25%;
        position: relative;
        height: 0;
    }

    .btn-floating {
        margin-top: -1.3rem;
    }
    }

    .new-arrivals-container {
        .pt-3-5 {
            padding-top: 1.25rem;
        }

        .card-body {
            position: absolute;
            width: 100%;
            height: 70%;
            top: 30%;
            overflow: hidden;
            background-color: transparent;
            -webkit-transition: all 1s;
            -o-transition: all 1s;
            transition: all 1s;
        }

        .closed {
            top: 100%;
            height: 10.5rem;
            margin-top: -10.5rem;
        }

        .book-cover {
            overflow: hidden;
            padding-bottom: 56.25%;
            position: relative;
            height: 0;
        }

        .btn-floating {
            margin-top: -1.3rem;
        }
</style>
<?php
require_once "elements/nav.php";
?>

<header class="text-center my-5">
    <h1 style="font-family: 'Soria'; font-size: 40pt;">My Boooks</h1>
</header>

<div class="container">
    <h1 class="mb-3" style="font-family: 'Soria'">Pending Books</h1>
    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">Book Cover</th>
            <th scope="col">ISBN</th>
            <th scope="col">Title</th>
            <th scope="col">Category</th>
            <th scope="col">Pages</th>
            <th scope="col"></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach($borrowings as $borrowing) { ?>
            <tr>
                <td>
                    <img src="<?php echo $borrowing["picture_path"] ?>" alt="<?php echo $borrowing["title"]?>" class="img-fluid" style="height: 8rem; width: 5.5rem;">
                </td>
                <td><?php echo $borrowing["isbn"] ?></td>
                <td><?php echo $borrowing["title"] ?></td>
                <td><?php echo $borrowing["name"] ?></td>
                <td><?php echo $borrowing["num_of_pages"] ?></td>
                <td>
                    <a href="?isbn=<?php echo $borrowing["isbn"]?>" class="btn btn-outline-primary rounded-0">Cancel</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<div class="container mt-5">
    <h1 class="d-flex justify-content-center" style="font-family: 'Soria';">Currently Rented Books</h1>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">Book Cover</th>
                <th scope="col">Title</th>
                <th scope="col">Category</th>
                <th scope="col">Return Date</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rented_books as $rented_book) { ?>
                <tr>
                    <td>
                        <img src="<?php echo $rented_book["picture_path"] ?>" alt="<?php echo $rented_book["title"] ?>"
                             class="img-fluid" style="height: 8rem; width: 5.5rem;">
                    </td>
                    <td><?php echo $rented_book["title"] ?></td>
                    <td><?php echo $rented_book["name"] ?></td>
                    <td><?php echo $rented_book["return_date"] ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<div class="container mt-5">
    <h1 class="d-flex justify-content-center" style="font-family: 'Soria';">Favorite Books</h1>
    <div class="row justify-content-center">
        <?php foreach ($favorite_books as $favorite_book) { ?>
            <div class="card book-card rounded-0 border-0 col-9 col-md-3 m-3 m-md-2 px-0" style="height: 29rem;">
                <div class="book-cover h-100" style="background-image: url('<?php echo $favorite_book["picture_path"] ?>');
                        background-repeat: no-repeat; background-size: 100% auto;"></div>
                <div class="card-body closed px-0">
                    <div class="p-3 bg-dark text-light pt-3-5" style="min-height: 400px">
                        <div class="mb-3">
                            <a href="<?php echo "borrow.php?isbn=" . $favorite_book["isbn"]?>" class="btn btn-primary px-3 me-1 rounded-0">Borrow</a>
                            <!-- Add to favourites button -->
                            <a href="<?php echo "add_to_favorites.php?isbn=" . $favorite_book["isbn"]?>" class="btn btn-outline-primary me-1 rounded-0">
                                <i class="fa-solid fa-heart"></i>
                            </a>
                            <!-- Book detail view button-->
                            <a href="book_detail.php?isbn=<?php echo $favorite_book["isbn"] ?>" class="btn btn-outline-primary rounded-0">
                                <i class="fa-solid fa-circle-info"></i>
                            </a>
                        </div>
                        <h3 class="card-title d-flex" style="font-family: 'Soria';"><?php echo $favorite_book["title"] ?></h3>
                        <?php if(strlen($favorite_book["title"]) < 25) { ?>
                            <p class="mb-4 mb-md-3" style="font-family: 'Prociono TT';">Description: <span><i class="fa-solid fa-arrow-down"></i></span></p>
                        <?php } ?>
                        <p class="mt-4 mt-md-3" style="font-family: 'Prociono TT';">
                            <?php
                            $stringCut = substr($favorite_book["description"], 0, 170);
                            $endPoint = strrpos($stringCut, ' ');

                            $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                            echo $string .= '...';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="container mt-5">
    <h1 class="d-flex justify-content-center" style="font-family: 'Soria';">My Rental History</h1>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">Book Cover</th>
                <th scope="col">Title</th>
                <th scope="col">Category</th>
                <th scope="col">Return Date</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rented_history as $rented_history_book) { ?>
                <tr>
                    <td>
                        <img src="<?php echo $rented_history_book["picture_path"] ?>" alt="<?php echo $rented_history_book["title"] ?>"
                             class="img-fluid" style="height: 8rem; width: 5.5rem;">
                    </td>
                    <td><?php echo $rented_history_book["title"] ?></td>
                    <td><?php echo $rented_history_book["name"] ?></td>
                    <td><?php echo $rented_history_book["return_date"] ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Select all book cards
    const bookCards = document.querySelectorAll('.book-card');

    // Add a click event listener to each book card
    bookCards.forEach(card => {
        const cardBody = card.querySelector('.card-body');
        card.addEventListener('click', () => {
            cardBody.classList.toggle('closed');
        });
    });
</script>
<?php
    require_once "elements/footer.php";
    ?>
