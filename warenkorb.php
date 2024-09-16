<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location:http://bibliotheksverwaltung.htl-projekt.com/index.php");
    session_destroy();
    exit();
}

require_once "elements/database.php";

$stmt_borrowings = $con->prepare(
    "SELECT * 
                FROM Book b JOIN Borrowing t
                ON b.isbn = t.isbn
                JOIN Book_Category bc
                ON b.isbn = bc.isbn
                JOIN Category c
                on c.id = bc.category_id
                WHERE t.active = 0 AND t.user_id = :user_id");
$stmt_borrowings->execute([
    "user_id" => $_SESSION["user_id"]
]);
$borrowings = $stmt_borrowings->fetchAll(PDO::FETCH_ASSOC);

require_once "elements/header.php";
require_once "elements/nav.php";
?>

<header class="text-center my-5">
    <h1 style="font-family: 'Soria'; font-size: 40pt;">Shopping Cart</h1>
</header>

<main class="container">
    <h3 class="mb-3" style="font-family: 'Soria'; font-size: 26pt">Books</h3>
    <table class="table">
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
                    <button class="btn btn-primary">Delete</button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</main>

<?php
require_once "elements/footer.php";
?>
