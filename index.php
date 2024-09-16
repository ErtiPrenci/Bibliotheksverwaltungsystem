<?php
session_start();

require_once "elements/database.php";
try {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Call the stored procedure
    $stmt_select_books = $con->prepare("CALL SelectBooks()");
    $stmt_select_books->execute();
    $books = $stmt_select_books->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select_books->closeCursor();

} catch (PDOException $e) {
    // Handle PDOException
    echo "PDO Exception: " . $e->getMessage();
    // You can log the error or display a more user-friendly message
}

try {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Call the stored procedure
    $stmt_liked_book = $con->prepare("CALL SelectLikedBooks()");

    $stmt_liked_book->execute();
    $liked_books = $stmt_liked_book->fetchAll(PDO::FETCH_ASSOC);
    $stmt_liked_book->closeCursor();

} catch (PDOException $e) {
    // Handle PDOException
    echo "PDO Exception: " . $e->getMessage();
    // You can log the error or display a more user-friendly message
}


$this_site = htmlspecialchars($_SERVER['PHP_SELF']);

try {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $stmt_event = $con->prepare("CALL SelectEvents()");
    $stmt_event->execute();
    $events = $stmt_event->fetchAll(PDO::FETCH_ASSOC);

    $stmt_event->closeCursor();

} catch (PDOException $e) {
    // Handle PDOException
    echo "PDO Exception: " . $e->getMessage();
    // You can log the error or display a more user-friendly message
}


require_once "elements/header.php";
?>
    <style>
        body {
            background-color: #efedee;
        }

        .event-container {
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
        }

        .trending-container {
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
                background-repeat: no-repeat;
                background-size: 100% auto;
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
        }
    </style>

<?php
require_once "elements/nav.php";
?>

    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="true">
        <div class="carousel-indicators mb-5">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active me-3"
                    aria-current="true" aria-label="Slide 1"
                    style="border-radius: 50%; width: 10px; height: 10px;"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" class="me-3"
                    aria-label="Slide 2" style="border-radius: 50%; width: 10px; height: 10px;"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" class="me-3"
                    aria-label="Slide 3" style="border-radius: 50%; width: 10px; height: 10px;"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="pt-5 text-center d-md-flex"
                     style="height: 60rem; background: linear-gradient(rgb(40, 12, 26, 0.0), rgb(23, 23, 23, 0.86)), url('assets/pictures/book_lamp.png'); background-position: center; background-repeat: no-repeat; background-size: cover;">
                    <div class="d-flex flex-column align-items-center justify-content-center py-3 px-3 col-md-12 h-50 text-white align-self-center">
                        <p class="fs-8 fw-bold" style="font-size: 70px; font-family: 'Soria';">Check out the books at our library</p>

                        <a href="books.php" class="btn btn-light mt-3 rounded-0 fw-bold"
                           style="width: 25%; font-size: 22px; font-family: 'Soria'; padding-top: 12px;">CHECK OUT BOOKS</a>
                    </div>
                </div>

            </div>
            <div class="carousel-item">
                <div class="pt-5 text-center d-md-flex"
                     style="height: 60rem; background: linear-gradient(rgb(40, 12, 26, 0.0), rgb(23, 23, 23, 0.86)), url('assets/pictures/book_shelf_3.png'); background-position: center; background-repeat: no-repeat; background-size: cover;">
                    <div class="d-flex flex-column align-items-center justify-content-center py-3 px-3 col-md-12 h-50 text-white align-self-center">
                        <p class="fs-8 fw-bold" style="font-size: 70px; font-family: 'Soria';">Want to donate to our
                            library?</p>

                        <a href="contribute.php" class="btn btn-light mt-3 rounded-0 fw-bold"
                           style="width: 25%; font-size: 22px; font-family: 'Soria'; padding-top: 12px;">DONATE</a>

                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="pt-5 text-center d-md-flex"
                     style="height: 60rem; background: linear-gradient(rgb(40, 12, 26, 0.0), rgb(23, 23, 23, 0.86)), url('assets/pictures/book_shelf_4.png'); background-position: center; background-repeat: no-repeat; background-size: cover;">
                    <div class="d-flex flex-column align-items-center justify-content-center py-3 px-3 col-md-12 h-50 text-white align-self-center">
                        <p class="fs-8 fw-bold" style="font-size: 70px; font-family: 'Soria';">Don't have an
                            account?</p>
                        <a href="register.php" class="btn btn-light mt-3 rounded-0 fw-bold"
                           style="width: 25%; font-size: 22px; font-family: 'Soria'; padding-top: 12px;">REGISTER</a>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container event-container">
        <div class="row m-5 text-center">
            <h1 style="font-family: 'Soria';">Events</h1>
            <p style="font-family: 'Prociono TT';">Here you can find all the latest events that take place at our
                library. You are more than
                welcome to join us.</p>
        </div>

        <div class="row d-flex d-md-flex justify-content-center">
            <?php foreach ($events as $event) { ?>
                <div class="col-xs-12 col-sm-6 col-md-4 mt-3">
                    <div class="flip-card">
                        <div class="flip-card-inner mt-sm-5 mt-md-0">
                            <div class="flip-card-front"
                                 style="background-image: url(<?php echo $event["picturepath"] ?>); background-size: cover; background-position: center; overflow: hidden; background-size: 100% 100%;">
                                <div class="card rounded-0 border-0"
                                     style="height: 45%; position: absolute; bottom: 0; width: 100%; padding: 10px;">
                                    <div class="card-body text-center" style="">
                                        <h4 class="card-title text-primary"
                                            style="font-family: 'Soria';"><?php echo $event["title"] ?></h4>
                                        <p class="card-text" style="font-family: 'Prociono TT';">
                                            <?php
                                            $stringCut = substr($event["description"], 0, 50);
                                            $endPoint = strrpos($stringCut, ' ');

                                            $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                            echo $string .= '...';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flip-card-back">
                                <div class="card border-0">
                                    <div class="card-body text-center">
                                        <h4 class="card-title text-primary"
                                            style="font-family: 'Soria';"><?php echo $event["title"] ?></h4>
                                        <p class="card-text"
                                           style="font-family: 'Prociono TT';"><?php echo $event["description"] ?></p>
                                        <div class="d-flex justify-content-between align-content-end">
                                            <div class="d-flex align-content-evenly">
                                                <i class="bi bi-calendar3 text-primary m-1"></i>
                                                <p class="text-primary m-1"><b>Date:</b></p>
                                                <p class="text-primary m-1""><?php echo $event["date"] ?></p>
                                            </div>
                                            <div class="d-flex align-content-evenly">
                                                <i class="bi bi-clock text-primary m-1""></i>
                                                <p class="text-primary m-1""><b>Time:</b></p>
                                                <p class="text-primary m-1""><?php echo $event["time"] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="container rules-container">
        <div class="row m-5 text-center">
            <h1 style="font-family: 'Soria';">Rules</h1>
            <p style="font-family: 'Prociono TT';">Here are the rules you need to follow at our library! </p>
        </div>
        <div class="row d-flex justify-content-center align-items-center mt-5">
            <div class="col-md-3 text-center text-primary">
                <h1 class="fs-1 fw-bold ">1</h1>
                <p style="font-family: 'Prociono TT';">First rule</p>
            </div>
            <div class="col">
                <p class="" style="font-family: 'Prociono TT';">Only users registered with the library will be allowed
                    to borrow library material.</p>
            </div>
        </div>
        <div class="row d-flex justify-content-center align-items-center mt-5">
            <div class="col-md-3 text-center text-primary">
                <h1 class="fs-1 fw-bold ">2</h1>
                <p style="font-family: 'Prociono TT';">Second rule</p>
            </div>
            <div class="col">
                <p class="" style="font-family: 'Prociono TT';">The books that are borrowed should be returned with
                    punctuality. If more time is needed to finish the book, you should contact with the library's
                    administrator. It is also important that the books are returned in the condition in which they are
                    given. It is not allowed to be marked, underlined or written.</p>
            </div>
        </div>
        <div class="row d-flex justify-content-center align-items-center mt-5 ">
            <div class="col-md-3 text-center text-primary">
                <h1 class="fs-1 fw-bold ">3</h1>
                <p style="font-family: 'Prociono TT';">Third rule</p>
            </div>
            <div class="col">
                <p class="" style="font-family: 'Prociono TT';">Users will be held responsible for any material lost
                    while in their custody and will be required to pay the cost of replacement. Lost books once
                    recovered will remain the property of the Library.</p>
            </div>
        </div>
        <!--
        <div class="row text-center">
            <p>
                To read the full terms of services, go to our page.
                <a class="text-primary link-offset-2 link-underline link-underline-opacity-0" href="" >Terms of Services</a>
            </p>
        </div>
        -->
    </div>

    <div class="container new-arrivals-container">
        <div class="row m-5 text-center">
            <h1 style="font-family: 'Soria';">New arrivals</h1>
            <p style="font-family: 'Prociono TT';">Here are the latest books at our library! </p>
        </div>
            <div class="row d-md-flex justify-content-center">
                <?php foreach ($books as $book) { ?>
                    <div class="card book-card rounded-0 border-0 col-9 col-md-3 m-3 m-md-2 px-0" style="height: 29rem;">
                        <div class="book-cover h-100" style="background-image: url('<?php echo $book["picture_path"] ?>');
                                background-repeat: no-repeat; background-size: 100% auto;"></div>
                        <div class="card-body closed px-0">
                            <div class="p-3 bg-dark text-light pt-3-5" style="min-height: 400px">
                                <div class="mb-3">
                                    <a href="<?php echo "borrow.php?isbn=" . $book["isbn"]?>" class="btn btn-primary px-3 me-1 rounded-0">Borrow</a>
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
                <?php } ?>
            </div>
    </div>

    <div class="container trending-container mb-5">
        <div class="row m-5 text-center">
            <h1 style="font-family: 'Soria';">Trending</h1>
            <p style="font-family: 'Prociono TT';">Here are the most liked books at our library! </p>
        </div>
            <div class="row d-md-flex justify-content-center">
                <?php foreach ($liked_books as $book) { ?>
                    <div class="card book-card rounded-0 border-0 col-9 col-md-3 m-3 m-md-2 px-0" style="height: 29rem;">
                        <div class="book-cover h-100" style="background-image: url('<?php echo $book["picture_path"] ?>');
                                background-repeat: no-repeat; background-size: 100% auto;"></div>
                        <div class="card-body closed px-0">
                            <div class="p-3 bg-dark text-light pt-3-5" style="min-height: 400px">
                                <div class="mb-3">
                                    <a href="<?php echo "borrow.php?isbn=" . $book["isbn"]?>" class="btn btn-primary px-3 me-1 rounded-0">Borrow</a>
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
                <?php } ?>
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
require_once "elements/modal.php";

//book borrowing process
if (isset($_GET["borrow_success"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Your request to borrow this book has been sent for approval!", "success");
}

if (isset($_GET["book_is_borrowed_error"])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>", "Book is currently being borrowed!", "danger");
}

if (isset($_GET["you_borrowed_this_error"])) {
    createMyModal("<i class='fa-solid fa-triangle-exclamation fa-2x' style='color: #ffffff;'></i>", "Your request for this book is being approved!", "warning");
}

if (isset($_GET["book_limit_reached_error"])) {
    createMyModal("<i class='fa-solid fa-triangle-exclamation fa-2x' style='color: #ffffff;'></i>", "You can only request to borrow 5 books at a time!", "warning");
}

//favorites
if (isset($_GET['added_to_favorites'])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Book added to favorites successfully!", "success");
    unset($_GET['added_to_favorites']);
}
if (isset($_GET['removed_from_favorites'])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Book removed to favorites successfully!", "success");
    unset($_GET['removed_from_favorites']);
}





//login
if (isset($_GET["logout"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "You have been successfully logged out", "success");
}
if (isset($_GET["login"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "You are successfully logged in", "success");
}

//handle book add through scanner
if (isset($_GET['errorCurlResponse'])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>", "There was a problem while adding the book! Please try again!", "danger");
    unset($_GET['errorCurlResponse']);
}
if (isset($_GET['errorBookNotFound'])) {
    createMyModal("<i class='fa-solid fa-circle-xmark fa-2x' style='color: #ffffff;'></i>", "The scanned book was not found! Please try again!", "danger");
    unset($_GET['errorBookNotFound']);
}
if (isset($_GET['errorAddingBook'])) {
    createMyModal("<i class='fa-solid fa-triangle-exclamation fa-2x' style='color: #ffffff;'></i>", "Not all the information about the book was added correctly! Please check and verify", "warning");
    unset($_GET['errorAddingBook']);
}





require_once "elements/footer.php";
?>