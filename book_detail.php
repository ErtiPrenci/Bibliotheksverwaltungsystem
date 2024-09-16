<?php
session_start();
require_once "elements/redirect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header("Location: books.php?submit=1");
}

if(!isset($_GET["isbn"])) {
    redirectTo("books.php");
}

require_once "elements/database.php";

$isbn = $_GET["isbn"];

$stmt_cat = $con->prepare("SELECT name FROM Category");
$stmt_cat->execute();
$category = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

$stmt_book = $con->prepare(
    "SELECT b.title, p.name publisher, b.description, b.language, b.num_of_pages, b.inStock, b.picture_path, c.name cat
            FROM Book b join Publisher p
            ON b.publisher = p.id
            JOIN Book_Category bc
            ON b.isbn = bc.isbn
            JOIN Category c
            ON bc.category_id = c.id
            WHERE b.isbn = :isbn");
$stmt_book->execute(array("isbn" => $isbn));
$book_details = $stmt_book->fetchAll(PDO::FETCH_ASSOC);

$stmt_borrowings = $con->prepare("SELECT * FROM Borrowing WHERE isbn = :isbn");
$stmt_borrowings->execute(array("isbn" => $isbn));
$book_borrowing = $stmt_borrowings->fetchAll(PDO::FETCH_ASSOC);

$category_icons_books = array(
    $category[0]["name"] => '<i class="fa-solid fa-heart fa-beat d-flex align-items-center"></i>',
    $category[1]["name"] => '<i class="fa-solid fa-hat-wizard fa-beat"></i>',
    $category[2]["name"] => '<i class="fa-solid fa-face-laugh-squint fa-beat"></i>',
    $category[3]["name"] => '<i class="fa-solid fa-rocket fa-beat"></i>',
    $category[4]["name"] => '<i class="fa-solid fa-plus-minus fa-beat"></i>',
    $category[5]["name"] => '<i class="fa-solid fa-code fa-beat"></i>',
    $category[6]["name"] => '<i class="fa-solid fa-money-bill-trend-up fa-beat"></i>',
    $category[7]["name"] => '<i class="fa-solid fa-list-check fa-beat"></i>',
    $category[8]["name"] => '<i class="fa-solid fa-scroll fa-beat"></i>',
    $category[9]["name"] => '<i class="fa-solid fa-utensils fa-beat"></i>',
    $category[10]["name"] => '<i class="fa-solid fa-brain fa-beat"></i>',
    $category[11]["name"] => '<i class="fa-solid fa-handshake-angle fa-beat"></i>',
    $category[12]["name"] => '<i class="fa-solid fa-robot fa-beat"></i>',
    $category[13]["name"] => '<i class="fa-solid fa-dollar-sign fa-beat"></i>',
    $category[14]["name"] => '<i class="fa-solid fa-scale-balanced fa-beat"></i>',
);

$this_site = htmlspecialchars($_SERVER['PHP_SELF']);




// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Rating Review
    if (isset($_POST["submit"])) {
        // Initialize rating as null
        $rating = null;

        // Check if any rating option is selected
        if (isset($_POST["1"])) {
            $rating = 1;
        } else if (isset($_POST["2"])) {
            $rating = 2;
        } else if (isset($_POST["3"])) {
            $rating = 3;
        } else if (isset($_POST["4"])) {
            $rating = 4;
        }else{
            $rating = 5;
        }

        // Insert the review with the provided rating (or null if no rating is selected)
        $insertStatement = $con->prepare(
            "INSERT INTO Review (isbn, rating, comment) VALUES (:isbn , :rating, :comment)");

        if ($rating === null) {
            $rating = 0;
        }


        $insertStatement->execute([
            "isbn" => $isbn,
            "rating" => $rating,
            "comment" => $_POST["comment"]
        ]);
    } else {
        // If submit button is set, but no rating was provided, save the comment with a null rating
        if (isset($_POST["submit"])) {
            $insertStatement = $con->prepare(
                "INSERT INTO Review (isbn, rating, comment) VALUES (:isbn , :rating, :comment)");
            $insertStatement->execute([
                "isbn" => $isbn,
                "rating" => null,
                "comment" => $_POST["comment"]
            ]);

        }
    }
} else {
    // Redirect to login page if user is not logged in
    if (isset($_POST["submit"])) {
        header("Location: login.php?submiterror=1");
        exit();
    }
}





require_once "elements/header.php";
?>

<style>
    body {
        background-color: #efedee;
    }

    vr {
        height: 1.7rem;
    }

    div.stars {
        display: inline-block;
    }

    input.star { display: none; }

    label.star {
        float: right;
        margin-right: .4rem;
        font-size: 27px;
        color: #CA2D2D;
        transition: all .2s;
    }

    input.star:checked ~ label.star:before {
        content: '\f005';
        color: #CA2D2D;
        transition: all .25s;
    }

    label.star:hover { transform: rotate(-15deg) scale(1.3); }

    label.star:before {
        content: '\f006';
        font-family: FontAwesome;
    }
</style>

<?php
require_once "elements/nav.php";
?>

<header class="container mb-4 mt-5 px-0">
    <h1 class="text-dark mb-0 ps-5" style="font-family: 'Soria'; font-size: 48pt;"><?php echo $book_details[0]["title"]?></h1>
    <hr class="border border-primary mt-2">
</header>

<main class="container">
    <div class="row mb-4 px-5">
        <div class="col-md-4 ms-3 px-0 rounded-0">
            <div style="background-image: url('<?php echo $book_details[0]["picture_path"] ?>'); background-size: cover">
                <img src="<?php echo $book_details[0]["picture_path"] ?>" class="w-100" style="visibility: hidden">
            </div>
        </div>
        <div class="col-md-7 container">
            <div class="d-flex justify-content-between mb-4">
                <p class="mb-0"><span class="text-primary fw-bold">Author: </span>Null</p>
                <vr class='border border-primary'></vr>
                <p class="mb-0"><span class="text-primary fw-bold">Publisher: </span> <?php echo $book_details[0]["publisher"]?></p>
                <vr class='border border-primary'></vr>
                <p class="mb-0"><span class="text-primary fw-bold">Language: </span><?php echo $book_details[0]["language"]?></p>
                <vr class='border border-primary'></vr>
                <p class="mb-0"><span class="text-primary fw-bold">Pages: </span><?php echo $book_details[0]["num_of_pages"]?></p>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4" style="max-width: 60%">
                <div class="d-flex align-items-center">
                    <?php foreach ($category_icons_books as $cat => $icon) { if($cat == $book_details[0]["cat"]) { ?>
                        <p class='mb-0 me-2'>
                            <span class='text-dark fw-bold'>Genre: </span>
                            <?php echo $book_details[0]["cat"]; ?>
                        </p>
                        <div class='btn btn-primary rounded-circle d-flex align-items-center justify-content-center' style='height: 40px; width: 40px;'>
                            <?php echo $icon; ?>
                        </div>
                    <?php } } ?>
                </div>
                <vr class='border border-primary'></vr>
                <div class="d-flex align-items-center">
                    <span class="text-dark fw-bold me-2">Available: </span>
                    <?php if(count($book_borrowing) > 0) {
                        foreach($book_borrowing as $book_b) {
                            if($book_b["active"] == 1) { ?>
                                <div class='btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center' style='height: 40px; width: 40px;'>
                                    <i class="fa-solid fa-x fs-5 fa-beat"></i>
                                </div>
                            <?php } else { ?>
                                <div class='btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center' style='height: 40px; width: 40px;'>
                                    <i class="fa-solid fa-check fs-5 fa-beat"></i>
                                </div>
                            <?php }
                        }
                    } else { ?>
                        <div class='btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center' style='height: 40px; width: 40px;'>
                            <i class="fa-solid fa-check fs-5 fa-beat"></i>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="text-dark fw-bold me-2 mb-3">Description: </h4>
                <div><?php echo $book_details[0]["description"]?></div>
            </div>

            <div>
                <form action="<?php echo $this_site . "?isbn=" . $_GET["isbn"]?>" class="form-floating" method="POST">
                    <div class="d-flex align-items-center mb-3">
                        <h4 class="text-dark fw-bold mb-0 me-2">Rate: </h4>
                        <div class="stars w-auto">
                            <input class="star star-5" id="star-5" type="radio" name="5"/>
                            <label class="star star-5" for="star-5"></label>
                            <input class="star star-4" id="star-4" type="radio" name="4"/>
                            <label class="star star-4" for="star-4"></label>
                            <input class="star star-3" id="star-3" type="radio" name="3"/>
                            <label class="star star-3" for="star-3"></label>
                            <input class="star star-2" id="star-2" type="radio" name="2"/>
                            <label class="star star-2" for="star-2"></label>
                            <input class="star star-1" id="star-1" type="radio" name="1"/>
                            <label class="star star-1" for="star-1"></label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <textarea class="form-control rounded-0" placeholder="Leave a review" name="comment" id="floatingTextarea2" style="height: 100px"></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary rounded-0 px-5">Submit</button>
                </form>
            </div>
        </div>
    </div>




    <div class="mt-5">
        <h2 class="text-dark" style="font-family: 'Soria';">Ratings and Reviews</h2>
        <?php
        // Fetch existing ratings and reviews from the database
        $stmt_reviews = $con->prepare("SELECT * FROM Review WHERE isbn = :isbn");
        $stmt_reviews->execute(array("isbn" => $isbn));
        $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

        if (count($reviews) > 0) {
            foreach ($reviews as $review) {
                ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Rating: <?php echo $review['rating']; ?>/5</h5>
                        <p class="card-text"><?php echo $review['comment']; ?></p>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p style='font-family: Soria;'> No ratings or reviews yet.</p>";
        }
        ?>
    </div>



</main>

<div class="container px-0">
    <hr class="border border-primary">
</div>

<?php
require_once "elements/footer.php";

?>
