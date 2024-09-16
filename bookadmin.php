<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";


$stmt_books = $con->prepare("SELECT b.isbn, title, p.name as 'publisher_name', a.name as 'author_name', c.name as 'category_name',c.id as 'category_id', b.language, b.num_of_pages, b.description, b.accepted, b.inStock, b.picture_path FROM Publisher p 
    JOIN Book b
    ON b.publisher = p.id
    join Book_Author ba
    on b.isbn = ba.isbn
    join Author a
    on ba.author_id = a.id
    join Book_Category bc
    on b.isbn = bc.isbn
    join Category c
    on bc.category_id = c.id
    WHERE b.accepted = 1");
$stmt_books->execute();
$result = $stmt_books->fetchAll(PDO::FETCH_ASSOC);

$stmt_unaccepted_books = $con->prepare("SELECT b.isbn, title, p.name as 'publisher_name', a.name as 'author_name', c.name as 'category_name',c.id as 'category_id', b.language, b.num_of_pages, b.description, b.accepted, b.inStock, b.picture_path FROM Publisher p 
    JOIN Book b
    ON b.publisher = p.id
    join Book_Author ba
    on b.isbn = ba.isbn
    join Author a
    on ba.author_id = a.id
    join Book_Category bc
    on b.isbn = bc.isbn
    join Category c
    on bc.category_id = c.id
    WHERE b.accepted = 0");
$stmt_unaccepted_books->execute();
$result_unaccepted_books = $stmt_unaccepted_books->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['toDel'])) {
    $stmt_delete_book_category = $con->prepare("DELETE FROM Book_Category WHERE isbn = :isbn");
    $stmt_delete_book_category->bindParam(':isbn', $result[$_POST["toDel"]]["isbn"]);
    $stmt_delete_book_category->execute();

    $stmt_delete_book_author = $con->prepare("DELETE FROM Book_Author WHERE isbn = :isbn");
    $stmt_delete_book_author->bindParam(':isbn', $result[$_POST["toDel"]]["isbn"]);
    $stmt_delete_book_author->execute();

    $stmt_borrowing = $con->prepare("Select * FROM Borrowing WHERE isbn = :isbn");
    $stmt_borrowing->bindParam(':isbn', $result[$_POST["toDel"]]["isbn"]);
    $stmt_borrowing->execute();
    $borrowing = $stmt_books->fetchAll(PDO::FETCH_ASSOC);


    if (!empty($borrowing)) {
        $stmt_delete_borrowing = $con->prepare("DELETE FROM Borrowing WHERE isbn = :isbn");
        foreach ($borrowing as $borrowing_item) {
            $stmt_delete_borrowing->bindParam(':isbn', $borrowing_item["isbn"]);
            $stmt_delete_borrowing->execute();
        }
    }

    $stmt_delete_book = $con->prepare("DELETE FROM Book WHERE isbn = :isbn");
    $stmt_delete_book->bindParam(':isbn', $result[$_POST["toDel"]]["isbn"]);
    $stmt_delete_book->execute();

    redirectTo("bookadmin.php");
}

if (isset($_POST["title"]) || isset($_POST["instock"]) || isset($_POST["description"]) || isset($_POST["category"]))  {
    $title = $_POST["title"];
    $inStock = $_POST["instock"];
    $description = $_POST["description"];
    $category = $_POST["category"];

    try {
        $statement1 = $con->prepare("UPDATE Book SET title = :title WHERE isbn = :isbn");
        $statement1->execute(["title" => $title, "isbn" => $_POST["isbn"]]);

        $statement2 = $con->prepare("UPDATE Book_Category SET category_id  = :category WHERE isbn = :isbn");
        $statement2->execute(["category" => $category, "isbn" => $_POST["isbn"]]);


        $statement3 = $con->prepare("UPDATE Book SET description = :description WHERE isbn = :isbn");
        $statement3->execute(["description" => $description, "isbn" => $_POST["isbn"]]);


        $statement4 = $con->prepare("UPDATE Book SET inStock = :instock WHERE isbn = :isbn");
        $statement4->execute(["instock" => $inStock, "isbn" => $_POST["isbn"]]);

        $statement4 = $con->prepare("UPDATE Book SET inStock = :instock WHERE isbn = :isbn");
        $statement4->execute(["instock" => $inStock, "isbn" => $_POST["isbn"]]);

        $statement8 = $con->prepare("SELECT picture_path from Book WHERE isbn = :isbn");
        $statement8->execute([ "isbn" => $_POST["isbn"]]);
        $bookpic =  $statement8->fetchAll();
            $newfilepath = $bookpic[0]["picture_path"];

    if (is_uploaded_file($_FILES['changeprofilepicture']["tmp_name"])) {
        $target_dir = "assets/pictures/";
        $tmp_file = $_FILES['changeprofilepicture']["tmp_name"];
        $newfilepath = $target_dir . $_FILES["changeprofilepicture"]["name"];
        $imageFileType = strtolower(pathinfo($tmp_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["changeprofilepicture"]["tmp_name"]);
        $uploadOk = 1;
        if ($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";

            move_uploaded_file($tmp_file, $newfilepath);
            $uploadOk = 1;
        } else {
            //echo "<br> File is not an image. <br>";
            $uploadOk = 0;
        }
    } else {
        //echo "<br> empty image <br>";
    }

    //echo "this is the filepath for the database:" . $newfilepath;
    $sql = "UPDATE Book SET  picture_path = :changeprofilepicture WHERE isbn = :isbn ;";
    $sth = $con->prepare($sql);
    $sth->bindParam('changeprofilepicture', $newfilepath);
    $sth->bindParam('isbn', $_POST["isbn"]);
    $sth->execute();

        redirectTo("bookadmin.php?changed=1");
    }catch (PDOException $e){
        echo $e->getMessage();

    }


}


if (isset($_POST["addisbn"]) && isset($_POST["addtitle"])&& isset($_POST["addlanguage"])&& isset($_POST["addauthor"])&&isset($_POST["addpublisher"]) && isset($_POST["addnumofpages"]) &&isset($_POST["adddescription"]) && isset($_POST["addinstock"]) ){
    $addisbn = $_POST["addisbn"];
    $addtitle = $_POST["addtitle"];
    $addlanguage = $_POST["addlanguage"];
    $addnumofpages = $_POST["addnumofpages"];
    $adddescription = $_POST["adddescription"];
    $addnumofpages = filter_var($_POST["addnumofpages"], FILTER_VALIDATE_INT);
    $addinstock = filter_var( $_POST["addinstock"], FILTER_VALIDATE_INT);

    var_dump($addisbn);
    var_dump($addtitle);
    var_dump($addlanguage);
    var_dump($addnumofpages);
    var_dump($addinstock);
    var_dump($adddescription);
    var_dump($_POST["addcategory"]);

    $filepath = null;

    if (is_uploaded_file($_FILES['profilepicture']["tmp_name"])) {
        $target_dir = "assets/pictures/users/";
        $tmp_file = $_FILES['profilepicture']["tmp_name"];
        $filepath = $target_dir . $_FILES["profilepicture"]["name"];
        $imageFileType = strtolower(pathinfo($tmp_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profilepicture"]["tmp_name"]);
        $uploadOk = 1;

        if ($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
            move_uploaded_file($tmp_file, $filepath);
            $uploadOk = 1;
        } else {
            //echo "<br> File is not an image. <br>";
            $uploadOk = 0;
        }
    } else {
        //echo "<br> empty image <br>";
    }

    $adduathor = $_POST["addauthor"];
    $statement5 = $con->prepare(
        "INSERT IGNORE INTO Author(name) VALUES (:addauthor);"
    );
    $statement5->execute([
        "addauthor" => $adduathor
    ]);

    $addpublisher = $_POST["addpublisher"];
    $statement6 = $con->prepare(
        "INSERT IGNORE INTO Publisher(name) VALUES (:addpublisher);"
    );
    $statement6->execute([
        "addpublisher" => $addpublisher
    ]);

    $statementpub = $con->prepare("Select id from Publisher where name = :addpublisher");
    $statementpub->execute(["addpublisher" => $addpublisher]);
    $publisher = $statementpub->fetchAll(PDO::FETCH_ASSOC);

    $statementauth = $con->prepare("Select id from Author where name = :addauthor");
    $statementauth->execute(["addauthor" => $adduathor]);
    $author = $statementauth->fetchAll(PDO::FETCH_ASSOC);


    $statement = $con->prepare(
        "INSERT IGNORE INTO Book(isbn,title,publisher, language,num_of_pages,description,inStock,picture_path, accepted) VALUES (:addisbn,:addtitle,:addpublisher,:addlanguage,:addnumofpages,:adddescription,:addinstock,:profilepicture,1);"
    );
    $statement->execute([
        "addisbn" => $addisbn,
        "addtitle" => $addtitle,
        "addpublisher" => $publisher[0]["id"],
        "addlanguage" => $addlanguage,
        "addnumofpages" => $addnumofpages,
        "adddescription" => $adddescription,
        "addinstock" => $addinstock,
        "profilepicture" => $filepath
    ]);

    echo $publisher[0]["id"];
    var_dump($filepath);

    $statement2 = $con->prepare(
        "INSERT IGNORE INTO Book_Author(isbn,author_id) VALUES (:addisbn,:addbok_author);"
    );
    $statement2->execute([
        "addisbn" => $addisbn,
        "addbok_author" => $author[0]["id"]
    ]);
    $statement3 = $con->prepare(
        "INSERT IGNORE INTO Book_Category(isbn,category_id) VALUES (:addisbn,:addcategory_id);"
    );
    $statement3->execute([
        "addisbn" => $addisbn,
        "addcategory_id" => $_POST["addcategory"]
    ]);

    redirectTo("bookadmin.php?added=1");
}
if (isset($_GET['search'])) {
    $search_query = TRIM($_GET['search']);
    try {
        $stmt_books = $con->prepare("SELECT b.isbn, title, p.name as 'publisher_name', a.name as 'author_name', c.name as 'category_name',c.id as 'category_id', b.language, b.num_of_pages, b.description, b.accepted, b.inStock, b.picture_path FROM Publisher p 
    JOIN Book b
    ON b.publisher = p.id
    join Book_Author ba
    on b.isbn = ba.isbn
    join Author a
    on ba.author_id = a.id
    join Book_Category bc
    on b.isbn = bc.isbn
    join Category c
    on bc.category_id = c.id
     WHERE (a.name LIKE :search_query_name OR title LIKE :search_query_title    OR b.isbn LIKE :isbn OR  p.name LIKE :publisher_name) AND b.accepted = 1
            ");



        $search_param = '%' . $search_query . '%';
        $stmt_books->bindValue(':search_query_name', $search_param, PDO::PARAM_STR);
        $stmt_books->bindValue(':search_query_title', $search_param, PDO::PARAM_STR);
        $stmt_books->bindValue(':isbn', $search_param, PDO::PARAM_STR);
        $stmt_books->bindValue(':publisher_name', $search_param, PDO::PARAM_STR);

        $stmt_books->execute();
        $result = $stmt_books->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {

}
$this_site = htmlspecialchars($_SERVER['PHP_SELF']);



require_once "elements/header.php";
require_once "elements/nav.php";
require_once "elements/modal.php";
?>
<div class="container">
    <div class="mt-5 d-flex justify-content-between">
        <b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
    </div>
</div>
    <header class="text-center my-5">
        <h1 style="font-family: 'Soria'; font-size: 40pt;">Book Administration</h1>
    </header>


    <main class="container mb-4">
        <div class="mb-5">

            <h3 class="mb-3" style="font-family: 'Soria'; font-size: 26pt;">Book details</h3>
            <div class="d-flex justify-content-between align-items-center mb-3 ">
                <div class="d-flex flex-column align-items-center">
                    <form class="d-flex mb-4 w-100" role="search" method="GET" action="<?php echo $this_site; ?>">
                        <input class="form-control rounded-0" type="search" placeholder="Search" aria-label="Search" name="search">
                        <button class="btn btn-primary rounded-0 text-light" type="submit">
                            <i class="bi bi-search"></i>
                        </button>

                    </form>
                    <pre>Search by ISBN,Title, Author, Publisher</pre>

                </div>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary rounded-0" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Add Book
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Add a new Book</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="bookadmin.php" method="POST" enctype="multipart/form-data" novalidate>
                                <div class="modal-body">

                                    <label class="d-flex justify-content-start">ISBN</label>
                                    <input type="text" class="form-control" name="addisbn" id="addisbn"
                                           style="border-radius: 0px;" placeholder="Add ISBN">
                                    <label class="d-flex justify-content-start">Title</label>
                                    <input type="text" class="form-control" name="addtitle" id="addtitle"
                                           style="border-radius: 0px;" placeholder="Add the Title">
                                    <label class="d-flex justify-content-start">Publisher</label>
                                    <input type="text" class="form-control" name="addpublisher" id="addpublisher"
                                           style="border-radius: 0px;" placeholder="Add the Publisher">
                                    <label class="d-flex justify-content-start">Author</label>
                                    <input type="text" class="form-control" name="addauthor" id="addauthor"
                                           style="border-radius: 0px;" placeholder="Add the Author">
                                    <label class="d-flex justify-content-start">Category</label>
                                    <select name="addcategory" id="addcategory">
                                        <optgroup label="Categories">
                                            <option value="">Select Category</option>
                                            <?php
                                            $stmt_category = $con->prepare("SELECT * FROM Category");
                                            $stmt_category->execute();
                                            $cat_result = $stmt_category->fetchAll();


                                            foreach ($cat_result as $category) {
                                                ?>


                                                <option value="<?php echo $category["id"]; ?>"><?php echo $category["name"]; ?></option>
                                            <?php } ?>
                                    </select>
                                    <label class="d-flex justify-content-start">Language</label>
                                    <input type="text" class="form-control" name="addlanguage" id="addlanguage"
                                           style="border-radius: 0px;" placeholder="Add the Language">
                                    <label class="d-flex justify-content-start">Number of Pages</label>
                                    <input type="number" class="form-control" name="addnumofpages" id="addnumofpages"
                                           style="border-radius: 0px;" placeholder="Add the Number of Pages">
                                    <label class="d-flex justify-content-start">Description</label>
                                    <input type="text" class="form-control" name="adddescription" id="adddescription"
                                           style="border-radius: 0px;" placeholder="Add the Description">
                                    <label class="d-flex justify-content-start">In Stock</label>
                                    <input type="number" class="form-control" name="addinstock" id="addinstock"
                                           style="border-radius: 0px;" placeholder="Add the InStock">
                                    <label class="d-flex justify-content-start">Book Cover</label>
                                    <input type="file" class="fileimg form-control text-center me-4   rounded-0 " name="profilepicture" id="profilepicture" style=" width: 107px; height: 37px">

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="addbook" class="btn btn-outline-primary rounded-0 ">Save changes</button>
                                    <button type="button" class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close</button>

                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <?php # foreach ($stmt as $row) {?>
            <table class="table" id="myTable">
                <thead>
                <tr>
                    <th scope="col">Book Cover</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Title</th>
                    <th scope="col">Publisher</th>
                    <th scope="col">Author</th>
                    <th scope="col">Category</th>
                    <th scope="col">Language</th>
                    <th scope="col">Num_of_pages</th>
                    <th scope="col">Description</th>
                    <th scope="col">inStock</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $results_per_page = 10;
                $total_books = count($result);

                if (isset($_GET['apage'])) {
                    $page = $_GET['apage'];
                } else {
                    $page = 1;
                }

                $start_index = ($page - 1) * $results_per_page;
                $total_pages = ceil($total_books / $results_per_page);

                // Iterate through the books for the current page
                for ($i = $start_index; $i < min($start_index + $results_per_page, $total_books); $i++) {
                    $bookitems = $result[$i];
                    ?>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal<?php echo $i; ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?php echo $i; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Book edit</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <form action="bookadmin.php" method="POST" enctype="multipart/form-data" novalidate>
                                    <div class="modal-body">

                                        <input type="hidden" id="isbn" name="isbn" value="<?php echo $bookitems["isbn"] ?>">
                                        <input type="hidden" id="catid" name="catid"
                                               value="<?php echo $bookitems["category_id"] ?>">
                                        <label class="d-flex justify-content-start">Title</label>
                                        <input type="text" class="form-control" name="title" id="title"
                                               style="border-radius: 0px;" placeholder="Change the Title"
                                               value="<?php echo $bookitems["title"]; ?> ">
                                        <br>

                                        <label class="d-flex justify-content-start">Category</label>
                                        <select name="category" id="category">
                                            <optgroup label="Categories">

                                                <option value="<?php echo $bookitems["category_id"]; ?>"><?php echo $bookitems["category_name"]; ?></option>
                                                <?php
                                                $stmt_category = $con->prepare("SELECT * FROM Category");
                                                $stmt_category->execute();
                                                $cat_result = $stmt_category->fetchAll();


                                                foreach ($cat_result as $category) {
                                                    ?>


                                                    <option value="<?php echo $category["id"]; ?>"><?php echo $category["name"]; ?></option>
                                                <?php } ?>
                                        </select>
                                        <br>
                                        <br>
                                        <label class="d-flex justify-content-start">Description</label>
                                        <input type="text" class="form-control" name="description" id="description"
                                               style="border-radius: 0px;" placeholder="Change the Description"
                                               value="<?php echo $bookitems["description"]; ?> ">
                                        <br>

                                        <label class="d-flex justify-content-start">inStock</label>
                                        <input type="number" class="form-control" name="instock" id="instock"
                                               style="border-radius: 0px; width: 160px;" value="<?php echo $bookitems["inStock"]; ?>">

                                        <label class="d-flex justify-content-start">Change Cover Picture</label>
                                        <input type="file" class="fileimg form-control text-center me-4   rounded-0 " name="changeprofilepicture" id="changeprofilepicture" value="<?php echo $bookitems["picture_path"]; ?>" style=" width: 107px; height: 37px">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="bookedit" class="btn  rounded-0 btn-outline-primary">
                                            Save changes
                                        </button>
                                        <button type="button" class="btn btn-secondary rounded-0 " data-bs-dismiss="modal">
                                            Close
                                        </button>

                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                    <tr>
                        <td>
                            <?php if (!empty($bookitems["picture_path"])) { ?>
                                <img src="<?php echo $bookitems["picture_path"] ?>" class="img-fluid" style="width: 25rem">
                            <?php } else { ?>
                                <img src="assets/pictures/Bibliothekverwaltung-Logo-Placeholder.png" class="img-fluid" style="width: 25rem">
                            <?php } ?>

                        </td>
                        <td><?php echo $bookitems["isbn"] ?></td>
                        <td><?php echo $bookitems["title"] ?></td>
                        <td><?php echo $bookitems["publisher_name"] ?></td>
                        <td><?php echo $bookitems["author_name"] ?></td>
                        <td><?php echo $bookitems["category_name"] ?></td>
                        <td><?php echo $bookitems["language"] ?></td>
                        <td><?php echo $bookitems["num_of_pages"] ?></td>
                        <td><?php echo mb_strimwidth($bookitems["description"], 0, 125) . "..." ?></td>
                        <td><?php echo $bookitems["inStock"] ?></td>
                        <td>
                            <a href="bookadmin.php?id=<?php echo $i; ?>" data-bs-toggle="modal" class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center" data-bs-target="#exampleModal<?php echo $i; ?>" style="height: 40px; width: 40px;">
                                <i class="fa-solid fa-pencil  rounded-circle"></i>
                            </a>
                        </td>
                        <td>
                            <form action="bookadmin.php" method="POST" class="text-end" enctype="multipart/form-data">
                                <button name="toDel" type="submit" class="btn btn-outline-danger rounded-circle" value="<?php echo $i ?>" style="height: 40px; width: 40px;">
                                    <i class="bi bi-trash3-fill "></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php
                } // End of foreach loop
                ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav class="d-flex justify-content-center" aria-label="Page navigation example">
                <ul class="pagination">
                    <?php if ($page > 1) { ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo htmlentities($_SERVER['PHP_SELF']) . '?apage=' . ($page - 1); ?>" tabindex="-1">Previous</a>
                        </li>
                    <?php } ?>
                    <?php
                    // Calculate the range of pages to display
                    $page_start = max(1, $page - 5);
                    $page_end = min($page + 5, $total_pages);
                    for ($i = $page_start; $i <= $page_end; $i++) { ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo htmlentities($_SERVER['PHP_SELF']) . '?apage=' . $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($start_index + $results_per_page < $total_books) { ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo htmlentities($_SERVER['PHP_SELF']) . '?apage=' . ($page + 1); ?>">Next</a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>


        </div>
        <div class="mb-5">
            <h3 class="mb-3" style="font-family: 'Soria'; font-size: 26pt;">Unaccepted Books</h3>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Book Cover</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Title</th>
                    <th scope="col">Publisher</th>
                    <th scope="col">Author</th>
                    <th scope="col">Category</th>
                    <th scope="col">Language</th>
                    <th scope="col">Num_of_pages</th>
                    <th scope="col">Description</th>
                    <th scope="col">inStock</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $cnt1 = 0;
                $results_per_page = 10;
                $total_books = count($result_unaccepted_books);

                if(isset($_GET['page'])){
                    $page = $_GET['page'];
                } else {
                    $page = 1;
                }

                $start_index = ($page - 1) * $results_per_page;

                $total_pages = ceil($total_books / $results_per_page);
                // Iterate through the books for the current page
                for ($i = $start_index; $i < min($start_index + $results_per_page, $total_books); $i++) {
                    $bookitems1 = $result_unaccepted_books[$i]; ?>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal1<?php echo $i; ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?php echo $i; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Book edit</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <form action="bookadmin.php" method="POST" enctype="multipart/form-data" novalidate>
                                <div class="modal-body">

                                    <input type="hidden" id="isbn" name="isbn" value="<?php echo $bookitems1["isbn"] ?>">
                                    <input type="hidden" id="catid" name="catid"
                                           value="<?php echo $bookitems1["category_id"] ?>">
                                    <label class="d-flex justify-content-start">Title</label>
                                    <input type="text" class="form-control" name="title" id="title"
                                           style="border-radius: 0px;" placeholder="Change the Title"
                                           value="<?php echo $bookitems1["title"]; ?> ">
                                    <br>

                                    <label class="d-flex justify-content-start">Category</label>
                                    <select name="category" id="category">
                                        <optgroup label="Categories">

                                            <option value="<?php echo $bookitems1["category_id"]; ?>"><?php echo $bookitems1["category_name"]; ?></option>
                                            <?php
                                            $stmt_category = $con->prepare("SELECT * FROM Category");
                                            $stmt_category->execute();
                                            $cat_result = $stmt_category->fetchAll();


                                            foreach ($cat_result as $category) {
                                                ?>


                                                <option value="<?php echo $category["id"]; ?>"><?php echo $category["name"]; ?></option>
                                            <?php } ?>
                                    </select>
                                    <br>
                                    <br>
                                    <label class="d-flex justify-content-start">Description</label>
                                    <input type="text" class="form-control" name="description" id="description"
                                           style="border-radius: 0px;" placeholder="Change the Description"
                                           value="<?php echo $bookitems1["description"]; ?> ">
                                    <br>

                                    <label class="d-flex justify-content-start">inStock</label>
                                    <input type="number" class="form-control" name="instock" id="instock"
                                           style="border-radius: 0px; width: 160px;" value="<?php echo $bookitems1["inStock"]; ?>">

                                    <label class="d-flex justify-content-start">Change Cover Picture</label>
                                    <input type="file" class="fileimg form-control text-center me-4   rounded-0 " name="changeprofilepicture" id="changeprofilepicture" value="<?php echo $bookitems1["picture_path"]; ?>" style=" width: 107px; height: 37px">

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="bookedit" class="btn  rounded-0 btn-outline-primary">
                                        Save changes
                                    </button>
                                    <button type="button" class="btn btn-secondary rounded-0 " data-bs-dismiss="modal">
                                        Close
                                    </button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                    <tr>
                        <td>
                            <?php if (!empty($bookitems1["picture_path"])) { ?>
                                <img src="<?php echo $bookitems1["picture_path"] ?>" class="img-fluid" style="width: 25rem">
                            <?php } else { ?>
                                <img src="assets/pictures/Bibliothekverwaltung-Logo-Placeholder.png" class="img-fluid" style="width: 25rem">
                            <?php } ?>
                        </td>
                        <td><?php echo $bookitems1["isbn"] ?></td>
                        <td><?php echo $bookitems1["title"] ?></td>
                        <td><?php echo $bookitems1["publisher_name"] ?></td>
                        <td><?php echo $bookitems1["author_name"] ?></td>
                        <td><?php echo $bookitems1["category_name"] ?></td>
                        <td><?php echo $bookitems1["language"] ?></td>
                        <td><?php echo $bookitems1["num_of_pages"] ?></td>
                        <td><?php echo mb_strimwidth($bookitems1["description"], 0, 125) . "..." ?></td>
                        <td><?php echo $bookitems1["inStock"] ?></td>
                        <td>
                            <a href="bookadmin.php?id=<?php echo $cnt1; ?>" data-bs-toggle="modal"
                               class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center"
                               data-bs-target="#exampleModal1<?php echo $cnt1; ?>" style="height: 40px; width: 40px;">
                                <i class="fa-solid fa-pencil rounded-circle"></i>
                            </a>
                        </td>
                        <td>
                            <form action="bookadmin.php" method="POST" class="text-end" enctype="multipart/form-data">
                                <button name="toDel" type="submit" class="btn btn-outline-danger rounded-circle"
                                        value="<?php echo $cnt1 ?>" style="height: 40px; width: 40px;">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="accept_book.php?isbn=<?php echo $bookitems1["isbn"] ?>"
                               class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" style="height: 40px; width: 40px;">
                                <i class="fa-solid fa-check"></i>
                            </a>
                        </td>
                    </tr>
                    <?php $cnt1++;
                } ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav  class="d-flex justify-content-center " aria-label="Page navigation example">
                <ul class="pagination">
                    <?php if ($page > 1) { ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo htmlentities($_SERVER['PHP_SELF']) . '?page=' . ($page - 1); ?>" tabindex="-1">Previous</a>
                        </li>
                    <?php } ?>
                    <?php
                    // Calculate the range of pages to display
                    $page_start = max(1, $page - 5);
                    $page_end = min($page + 5, $total_pages);
                    for ($i = $page_start; $i <= $page_end; $i++) { ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo htmlentities($_SERVER['PHP_SELF']) . '?page=' . $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($start_index + $results_per_page < $total_books) { ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo htmlentities($_SERVER['PHP_SELF']) . '?page=' . ($page + 1); ?>">Next</a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>


        <nav aria-label="Page navigation example">
            <ul class="pagination">

            </ul>
        </nav>
    </main>

    <?php
    require_once "elements/footer.php";
    require_once "elements/modal.php";

    if (isset($_GET['book_accepted'])) {
        createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Book successfully accepted!", "success");
    }
    if (isset($_GET['added'])) {
        createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Book successfully added!", "success");
    }
    if (isset($_GET['changed'])) {
        createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Changes successfully saved!", "success");
    }
    ?>
