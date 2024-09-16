<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";

$stmt_books = $con->prepare(
    "SELECT b.isbn, b.title, c.name as catName, b.picture_path, a.name as authorName, p.name as publisherName
            FROM Book b
            JOIN Book_Category bc ON b.isbn = bc.isbn
            JOIN Category c ON bc.category_id = c.id
            JOIN Book_Author ba ON b.isbn = ba.isbn
            JOIN Author a ON ba.author_id = a.id
            JOIN Publisher p ON p.id = b.publisher 
            WHERE b.accepted = 1");

$stmt_books->execute();
$books = $stmt_books->fetchAll(PDO::FETCH_ASSOC);

$stmt_borrowings = $con->prepare("SELECT * FROM Borrowing WHERE active = 1");
$stmt_borrowings->execute();
$book_borrowing = $stmt_borrowings->fetchAll(PDO::FETCH_ASSOC);

require_once "elements/header.php";
require_once "elements/nav.php";
?>

    <header class="text-center my-5">
        <div class="container">
            <div class="mt-5 d-flex justify-content-between">
                <b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
            </div>
            <h1 style="font-family: 'Soria'; font-size: 40pt;">Select Book for Transaction</h1>
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
                    <th scope="col">Author</th>
                    <th scope="col">Publisher</th>
                    <th scope="col">Category</th>
                    <th scope="col"></th>
                </tr>
                </thead>

                <tbody>
                <?php
                $isActive = false;
                foreach ($books as $book) {
                    foreach ($book_borrowing as $active) {
                        if ($book["isbn"] == $active["isbn"]) {
                            $isActive = true;
                        }
                    }

                    if (!$isActive) { ?>
                        <tr>
                            <td>
                                <img src="<?php echo $book["picture_path"] ?>" alt="<?php echo $book["title"] ?>"
                                         class="img-fluid" style="height: 8rem; width: 5.5rem;">
                            </td>
                            <td><?php echo $book["isbn"] ?></td>
                            <td><?php echo $book["title"] ?></td>
                            <td><?php echo $book["authorName"] ?></td>
                            <td><?php echo $book["publisherName"] ?></td>
                            <td><?php echo $book["catName"] ?></td>
                            <td>
                                <a href="borrow.php?isbn=<?php echo $book["isbn"] . "&userid=" . $_GET["userid"]?>"
                                   class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                                   style="height: 40px; width: 40px;">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    } $isActive = false;
                } ?>
                </tbody>
            </table>
        </div>
    </main>

<?php
require_once 'elements/footer.php';
?>