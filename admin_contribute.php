<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";

$stmt_contributions = $con->prepare("SELECT * FROM Contributions");
$stmt_contributions->execute();
$contributions = $stmt_contributions->fetchAll(PDO::FETCH_ASSOC);

$stmt_categories = $con->prepare("SELECT * FROM Category");
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST["edit_contribution"])) {
    $isbn = $_POST["isbn"];
    $title = $_POST["title"];
    $category = $_POST["category"];
    $description = trim($_POST["description"]);
    $language = $_POST["language"];
    $pages = $_POST["pages"];

    try {
        $stmt_default_cover = $con->prepare("SELECT picture_path FROM Contributions WHERE isbn = :isbn");
        $stmt_default_cover->execute(["isbn" => $isbn]);
        $default_cover = $stmt_default_cover->fetchAll(PDO::FETCH_ASSOC);

        $filepath = $default_cover[0]["picture_path"];

        if (is_uploaded_file($_FILES['cover']["tmp_name"])) {
            $target_dir = "assets/pictures/";
            $tmp_file = $_FILES['cover']["tmp_name"];
            $filepath = $target_dir . $_FILES["cover"]["name"];
            $imageFileType = strtolower(pathinfo($tmp_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["cover"]["tmp_name"]);
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

        $stmt_update_contributions = $con->prepare(
            "UPDATE Contributions SET title = :title, category = :category, language = :language, num_of_pages = :pages, description = :description, picture_path = :cover WHERE isbn = :isbn");
        $stmt_update_contributions->execute([
                "isbn" => $isbn,
            "title" => $title,
            "category" => $category,
            "language" => $language,
            "pages" => $pages,
            "description" => $description,
            "cover" => $filepath
        ]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    redirectTo("admin_contribute.php?book_edited=1");
}

if(isset($_POST["approve_contribution"])) {
    $isbn = $_POST["isbn"];

    try {
        $stmt_contributions = $con->prepare("SELECT * FROM Contributions WHERE isbn = :isbn");
        $stmt_contributions->execute(["isbn" => $isbn]);
        $contributions = $stmt_contributions->fetchAll(PDO::FETCH_ASSOC);

        $stmt_insert_author = $con->prepare("INSERT IGNORE INTO Author (name) VALUES (:authorname)");
        $stmt_insert_author->execute(["authorname" => $contributions[0]["author"]]);

        if ($contributions[0]["publisher"] != "No Publisher") {
            $stmt_insert_publisher = $con->prepare("INSERT IGNORE INTO Publisher (name) VALUES (:publishername)");
            $stmt_insert_publisher->execute(["publishername" => $contributions[0]["publisher"]]);

            $stmt_publisher_id = $con->prepare("SELECT id FROM Publisher WHERE name = :publishername");
            $stmt_publisher_id->execute(["publishername" => $contributions[0]["publisher"]]);
            $publisher = $stmt_publisher_id->fetchAll(PDO::FETCH_ASSOC);

            $publisher_id = $publisher[0]["id"];
        } else {
            $publisher_id = 866;
        }

        $stmt_insert_book = $con->prepare("INSERT IGNORE INTO Book (isbn, title, publisher, language, num_of_pages, description, inStock, picture_path, accepted) 
                                                VALUES (:isbn, :title, :publisher, :language, :num_of_pages, :description, 1, :picture_path, 1)");
        $stmt_insert_book->execute([
            "isbn" => $isbn,
            "title" => $contributions[0]["title"],
            "publisher" => $publisher_id,
            "language" => $contributions[0]["language"],
            "num_of_pages" => $contributions[0]["num_of_pages"],
            "description" => $contributions[0]["description"],
            "picture_path" => $contributions[0]["picture_path"]
        ]);

        $stmt_author_id = $con->prepare("SELECT id FROM Author WHERE name = :authorname");
        $stmt_author_id->execute(["authorname" => $contributions[0]["author"]]);
        $author = $stmt_author_id->fetchAll(PDO::FETCH_ASSOC);

        $author_id = $author[0]["id"];

        $stmt_insert_book_author = $con->prepare("INSERT IGNORE INTO Book_Author (isbn, author_id) VALUES (:isbn, :author_id)");
        $stmt_insert_book_author->execute(["isbn" => $isbn, "author_id" => $author_id]);

        $stmt_category_id = $con->prepare("SELECT id FROM Category WHERE name = :categoryname");
        $stmt_category_id->execute(["categoryname" => $contributions[0]["category"]]);
        $category = $stmt_category_id->fetchAll(PDO::FETCH_ASSOC);

        $category_id = $category[0]["id"];

        $stmt_insert_book_category = $con->prepare("INSERT IGNORE INTO Book_Category (isbn, category_id) VALUES (:isbn, :category_id)");
        $stmt_insert_book_category->execute(["isbn" => $isbn, "category_id" => $category_id]);

        $stmt_delete_contribution = $con->prepare("DELETE FROM Contributions WHERE isbn = :isbn");
        $stmt_delete_contribution->execute(["isbn" => $isbn]);

        redirectTo("admin_contribute.php?book_approved=1");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

/*echo "<pre>";
var_export($contributions);
echo "</pre>";*/

require_once "elements/header.php";
require_once "elements/nav.php";
?>

<header class="text-center my-5">
    <div class="container">
        <div class="mt-5 d-flex justify-content-between">
            <b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
        </div>
        <h1 style="font-family: 'Soria'; font-size: 40pt;">Book Contributions</h1>
    </div>
</header>

<main class="container mb-4" style="min-height: 25rem; ">
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
                <th scope="col">Language</th>
                <th scope="col">Pages</th>
                <th scope="col">Description</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>

            <tbody>
            <?php $cnt = 0; foreach ($contributions as $contribution) { ?>
                <tr>
                    <td>
                        <?php if(!empty($contribution["picture_path"])) { ?>
                            <img src="<?php echo $contribution["picture_path"] ?>" alt="<?php echo $contribution["title"] ?>"
                                 class="img-fluid" style="height: 8rem; width: 5.5rem;">
                        <?php } else { ?>
                            <img src="assets/pictures/Bibliothekverwaltung-Logo-Placeholder.png" alt="<?php echo $contribution["title"] ?>"
                                 class="img-fluid" style="height: 8rem; width: 5.5rem;">
                        <?php } ?>
                    </td>
                    <td><?php echo $contribution["isbn"] ?></td>
                    <td><?php echo $contribution["title"] ?></td>
                    <td><?php echo $contribution["author"] ?></td>
                    <td><?php echo $contribution["publisher"] ?></td>
                    <td><?php echo $contribution["category"] ?></td>
                    <td><?php echo $contribution["language"] ?></td>
                    <td><?php echo $contribution["num_of_pages"] ?></td>
                    <td><?php echo $contribution["description"] ?></td>
                    <td>
                        <a data-bs-toggle="modal" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                           data-bs-target="#editContributionModal<?php echo $cnt; ?>" style="height: 40px; width: 40px;">
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                    </td>
                    <td>
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="isbn" value="<?php echo $contribution["isbn"] ?>">
                            <button type="submit" class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center"
                                    style="height: 40px; width: 40px;" name="approve_contribution">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Edit Contributions Modal -->
                <div class="modal fade" id="editContributionModal<?php echo $cnt; ?>" tabindex="-1" aria-labelledby="editContributionLabel<?php echo $cnt; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="editContributionLabel<?php echo $cnt; ?>">Edit: <?php echo $contribution["title"]; ?></h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control rounded-0" id="title" name="title" value="<?php echo $contribution["title"]; ?>">

                                    <label for="category" class="form-label">Category</label>
                                    <select id="category" name="category" class="form-select rounded-0" aria-label="Default select example">
                                        <option value="<?php echo $contribution["category"]; ?>"><?php echo $contribution["category"]; ?></option>
                                        <?php foreach($categories as $category) {
                                            if($category["name"] != $contribution["category"]) {?>
                                            <option value="<?php echo $category["name"] ?>"><?php echo $category["name"] ?></option>
                                        <?php } } ?>
                                    </select>

                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control rounded-0" id="description" name="description" style="height: 100px">
                                        <?php echo $contribution["description"]; ?>
                                    </textarea>

                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <label for="language" class="form-label">Language</label>
                                            <input type="text" class="form-control rounded-0" id="language" name="language" value="<?php echo $contribution["language"]; ?>">
                                        </div>

                                        <div>
                                            <label for="pages" class="form-label">Pages</label>
                                            <input type="number" class="form-control rounded-0" id="pages" name="pages" value="<?php echo $contribution["num_of_pages"]; ?>">
                                        </div>
                                    </div>

                                    <label for="cover" class="form-label">Book Cover</label>
                                    <input type="file" class="form-control rounded-0" name="cover" id="cover">

                                    <input type="hidden" name="isbn" value="<?php echo $contribution["isbn"]; ?>">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-outline-primary rounded-0" name="edit_contribution">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php $cnt++; } ?>
            </tbody>
        </table>
    </div>
</main>

<?php
require_once 'elements/footer.php';
?>
