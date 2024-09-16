<?php
session_start();

require_once "elements/database.php";

$stmt_cat = $con->prepare("SELECT name FROM Category");
$stmt_cat->execute();
$category = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['search'])) {
    $search_query = TRIM($_GET['search']);
    try {
        $stmt_book_cat_join = $con->prepare(
            "
            SELECT b.isbn, b.title, c.name, b.picture_path, b.description, a.name
            FROM Book b 
            JOIN Book_Category bc ON b.isbn = bc.isbn 
            JOIN Category c ON bc.category_id = c.id
            JOIN Book_Author ba ON b.isbn = ba.isbn
            JOIN Author a ON ba.author_id = a.id
            WHERE (b.title LIKE :search_query_title OR b.description LIKE :search_query_description 
                       OR a.name LIKE :search_query_name) AND b.accepted = 1
            "
        );

        $search_param = '%' . $search_query . '%';
        $stmt_book_cat_join->bindValue(':search_query_title', $search_param, PDO::PARAM_STR);
        $stmt_book_cat_join->bindValue(':search_query_description', $search_param, PDO::PARAM_STR);
        $stmt_book_cat_join->bindValue(':search_query_name', $search_param, PDO::PARAM_STR);
        $stmt_book_cat_join->execute();
        $book_cats = $stmt_book_cat_join->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    $stmt_book_cat_join = $con->prepare(
        "SELECT b.isbn, b.title, c.name, b.picture_path, b.description
        FROM Book b JOIN Book_Category bc 
        ON b.isbn = bc.isbn 
        JOIN Category c 
        ON bc.category_id = c.id
        WHERE b.accepted = 1");

    $stmt_book_cat_join->execute();
    $book_cats = $stmt_book_cat_join->fetchAll(PDO::FETCH_ASSOC);
}

$category_icons_books = array(
    $category[0]["name"] => '<i class="fa-solid fa-heart fa-beat"></i>',
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

require_once "elements/header.php";
?>

    <style>
        .pt-3-5 {
            padding-top: 1.25rem;
        }

        .flip-card {
            perspective: 1000px;
        }

        .flip-card-inner {
            width: 100%;
            height: 0;
            padding-bottom: 75%; /* Maintain a 4:3 aspect ratio for the card */
            position: relative;
            transition: transform 0.7s;
            transform-style: preserve-3d;
        }

        .flip-card:hover .flip-card-inner {
            transform: rotateY(180deg);
        }

        .flip-card-front, .flip-card-back {
            width: 100%;
            height: 100%;
            position: absolute;
            backface-visibility: hidden;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        }

        .flip-card-front {
            background: white;
        }

        .flip-card-back {
            background: white;
            transform: rotateY(180deg);
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
            height: 11.5rem;
            margin-top: -11.5rem;
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

    <header class="container mb-3 mt-5 text-center">
        <div class="d-flex flex-column align-items-center">
            <form class="d-flex mb-4 w-50" role="search" method="GET" action="<?php echo $this_site; ?>">
                <input class="form-control rounded-0" type="search" placeholder="Search" aria-label="Search" name="search">
                <button class="btn btn-primary rounded-0 text-light" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <h1 style="font-family: 'Soria'; font-size: 42pt">Search for your favourite books!</h1>
        </div>
    </header>

    <!-- Category Band -->

    <main class="container">
        <section class="d-flex">
            <button id="btnPrev" class="btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#CA2D2D"
                     class="bi bi-arrow-left-circle-fill" viewBox="0 0 16 16">
                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
                </svg>
            </button>
            <div id="genre-band" class="d-flex align-items-center overflow-hidden w-100"
                 style="list-style-type: none; white-space: nowrap;">
                <ul id="genre-list" class="pagination m-0 p-0">
                    <li>
                        <a class='page-link rounded-pill mx-1' style="font-family: 'Prociono TT';"
                           href='<?php echo "?category=allbooks"?>'>
                            All
                        </a>
                    </li>
                    <?php if (count($category) > 0) { foreach ($category as $item) { ?>
                        <li class='page-item d-inline-block'>
                            <a class='page-link rounded-pill mx-1' style="font-family: 'Prociono TT';"
                               href='<?php echo "?category=" . str_replace(' ', '+', $item["name"]) ?>'>
                                <?php echo $item["name"]; ?>
                            </a>
                        </li>
                    <?php } } else { ?>
                        <li>No genres found.</li>
                    <?php } ?>
                </ul>
            </div>
            <button id="btnNext" class="btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#CA2D2D"
                     class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                </svg>
            </button>
        </section>

        <!-- Book Section -->

        <section class="container my-4" style="min-height: 55vh;">
            <div class="row d-md-flex justify-content-center">
                <?php foreach ($book_cats as $book) { if(isset($_GET["category"]) && $_GET["category"] == $book["name"]) { ?>
                    <div class="card book-card rounded-0 border-0 col-9 col-md-3 m-3 m-md-2 px-0" style="height: 29rem;">
                        <div class="book-cover h-100" style="background-image: url('<?php echo $book["picture_path"] ?>');
                                background-repeat: no-repeat; background-size: 100% auto;"></div>
                        <div class="card-body closed px-0">
                            <div class="button px-2 mt-3">
                                <a class="btn btn-primary btn-floating float-end rounded-circle d-flex align-items-center justify-content-center"
                                   style="margin-right: .75rem; height: 40px; width: 40px;">
                                    <?php foreach($category_icons_books as $cat => $icon) {
                                        if($book["name"] == $cat) {
                                            echo $icon;
                                        }
                                    }?>
                                </a>
                            </div>
                            <div class="p-3 bg-dark text-light pt-3-5" style="min-height: 400px">
                                <div class="mb-3">
                                    <a href="<?php echo "borrow.php?isbn=" . $book["isbn"] . "&userid=" . $_SESSION["user_id"]?>" class="btn btn-primary px-3 me-1 rounded-0">Borrow</a>
                                    <!-- Add to favourites button -->
                                    <a href="<?php echo "add_to_favorites.php?isbn=" . $book["isbn"]?>" class="btn btn-outline-primary me-1 rounded-0">
                                        <i class="fa-solid fa-heart"></i>
                                    </a>
                                    <!-- Book detail view button-->
                                    <a href="book_detail.php?isbn=<?php echo $book["isbn"] ?>" class="btn btn-outline-primary rounded-0">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </a>
                                </div>
                                <h3 class="card-title d-flex" style="font-family: 'Soria';"><?php echo $book["title"] ?></h3>
                                <?php if(strlen($book["title"]) < 25) { ?>
                                    <p class="mb-4 mb-md-3" style="font-family: 'Prociono TT';">Description: <span><i class="fa-solid fa-arrow-down"></i></span></p>
                                <?php } ?>
                                <p class="mt-4 mt-md-3" style="font-family: 'Prociono TT';">
                                    <?php
                                    $stringCut = substr($book["description"], 0, 170);
                                    $endPoint = strrpos($stringCut, ' ');

                                    $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                    echo $string .= '...';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } else if(!isset($_GET["category"]) || $_GET["category"] == "allbooks") { ?>
                    <div class="card book-card rounded-0 border-0 col-9 col-md-3 m-3 m-md-2 px-0" style="height: 29rem;">
                        <div class="book-cover h-100" style="background-image: url('<?php echo $book["picture_path"] ?>');
                                background-repeat: no-repeat; background-size: 100% auto;"></div>
                        <div class="card-body closed px-0">
                            <div class="button px-2 mt-3">
                                <a class="btn btn-primary btn-floating float-end rounded-circle d-flex align-items-center justify-content-center"
                                   style="margin-right: .75rem; height: 40px; width: 40px;">
                                    <?php foreach($category_icons_books as $cat => $icon) {
                                        if($book["name"] == $cat) {
                                            echo $icon;
                                        }
                                    }?>
                                </a>
                            </div>
                            <div class="p-3 bg-dark text-light pt-3-5" style="min-height: 400px">
                                <div class="mb-3">
                                    <a href="<?php echo "borrow.php?isbn=" . $book["isbn"] . "&userid=" . $_SESSION["user_id"]?>" class="btn btn-primary px-3 me-1 rounded-0">Borrow</a>
                                    <!-- Add to favourites button -->
                                    <a href="<?php echo "add_to_favorites.php?isbn=" . $book["isbn"]?>" class="btn btn-outline-primary me-1 rounded-0">
                                        <i class="fa-solid fa-heart"></i>
                                    </a>
                                    <!-- Book detail view button-->
                                    <a href="book_detail.php?isbn=<?php echo $book["isbn"] ?>" class="btn btn-outline-primary rounded-0">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </a>
                                </div>
                                <h3 class="card-title d-flex" style="font-family: 'Soria';"><?php echo $book["title"] ?></h3>
                                <?php if(strlen($book["title"]) < 25) { ?>
                                    <p class="mb-4 mb-md-3" style="font-family: 'Prociono TT';">Description: <span><i class="fa-solid fa-arrow-down"></i></span></p>
                                <?php } ?>
                                <p class="mt-4 mt-md-3" style="font-family: 'Prociono TT';">
                                    <?php
                                    $stringCut = substr($book["description"], 0, 170);
                                    $endPoint = strrpos($stringCut, ' ');

                                    $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                    echo $string .= '...';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } } ?>
            </div>
        </section>

        <!-- <a href="assets/Downloadable/app-release.apk">Download App Here!</a> -->
    </main>

    <script>
        const genreList = document.getElementById('genre-list');
        const genreBand = document.getElementById('genre-band');

        document.getElementById("btnNext").addEventListener("click", scrollGenres_to_right);
        document.getElementById("btnPrev").addEventListener("click", scrollGenres_to_left);

        function scrollGenres_to_right() {
            if (genreBand.scrollLeft <= genreList.scrollWidth - genreBand.clientWidth) {
                let scroll_right = genreBand.scrollLeft + genreBand.clientWidth;
                genreBand.scroll({
                    left: scroll_right,
                    behavior: 'smooth'
                });
            }
        }

        function scrollGenres_to_left() {
            if (genreBand.scrollLeft <= genreList.scrollWidth - genreBand.clientWidth) {
                let scroll_left = genreBand.scrollLeft - genreBand.clientWidth;
                genreBand.scroll({
                    left: scroll_left,
                    behavior: 'smooth'
                });
            }
        }

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
require_once "elements/modal.php";

if (isset($_GET["borrow_success"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Your request to borrow this book has been sent for approval!","success");
}

if (isset($_GET["book_is_borrowed_error"])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>","Book is currently being borrowed!","danger");
}

if (isset($_GET["you_borrowed_this_error"])) {
    createMyModal("<i class='fa-solid fa-triangle-exclamation fa-2x' style='color: #ffffff;'></i>","Your request for this book is being approved!","warning");
}

if (isset($_GET["book_limit_reached_error"])) {
    createMyModal("<i class='fa-solid fa-triangle-exclamation fa-2x' style='color: #ffffff;'></i>","You can only request to borrow 5 books at a time!","warning");
}

if (isset($_GET['added_to_favorites'])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Book added to favorites successfully!", "success");
    unset($_GET['added_to_favorites']);
}

if (isset($_GET['removed_from_favorites'])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Book removed to favorites successfully!", "success");
    unset($_GET['removed_from_favorites']);
}

require_once "elements/footer.php";
?>