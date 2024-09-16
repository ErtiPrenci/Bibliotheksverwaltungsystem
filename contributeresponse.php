<?php
session_start();
require_once 'elements/redirect.php';
require_once 'elements/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $isbn = $_POST["isbn"];
    $title = $_POST["title"];
    $author = $_POST["author"];

    if(isset($_POST["publisher"])) {
        $publisher = $_POST["publisher"];
    } else {
        $publisher = "No Publisher";
    }

    if(isset($_POST["category"])) {
        $category = $_POST["category"];
    } else {
        $category = "No Category";
    }

    $language = $_POST["language"];
    $num_of_pages = $_POST["num_of_pages"];

    if(isset($_POST["description"])) {
        $description = $_POST["description"];
    } else {
        $description = "No Description";
    }

    if(isset($_POST["picture_path"])) {
        $picture = $_POST["picture_path"];
    } else {
        $picture = null;
    }

    var_dump($isbn);
    var_dump($title);
    var_dump($author);
    var_dump($publisher);
    var_dump($category);
    var_dump($language);
    var_dump($num_of_pages);
    var_dump($description);
    var_dump($picture);

    $insertStatement = $con->prepare(
            "INSERT INTO Contributions (isbn, title, author, publisher, category, language, num_of_pages, description, picture_path) 
                    VALUES (:isbn, :title, :author, :publisher, :category, :language, :num_of_pages, :description, :picture_path)");
    $insertStatement->execute([
        "isbn" => $isbn,
        "title" => $title,
        "author" => $author,
        "publisher" => $publisher,
        "category" => $category,
        "language" => $language,
        "num_of_pages" => $num_of_pages,
        "description" => $description,
        "picture_path" => $picture
    ]);

    redirectTo("contribute.php?contribute_books=1");
}

require_once 'elements/header.php';
?>

<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>

<?php
require_once 'elements/nav.php';
?>

<div>
    <h1 class="d-flex justify-content-center m-3" style="font-family: 'Soria';">Informations of the book you want to contribute</h1>
    <div class="container mt-5">
        <?php
        if (isset($_GET['isbn']) && !empty($_GET['isbn'])) {
            $isbn = $_GET['isbn'];
            $service_url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . urlencode($isbn);
            $curl = curl_init($service_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $curl_response = curl_exec($curl);

            if ($curl_response === false) {
                $info = curl_getinfo($curl);
                curl_close($curl);
                die('Error occurred during cURL execution. Additional info: ' . var_dump($info));
            }
            curl_close($curl);

            $decoded_response = json_decode($curl_response, true);

            /*echo "<pre>";
            var_export($decoded_response);
            echo "</pre>";*/
            ?>

            <div class="d-flex justify-content-center">
                <form action="contributeresponse.php" method="POST">
                    <table>
                        <input type="hidden" name="isbn" value="<?php echo $_GET["isbn"] ?>">
                        <?php if (isset($decoded_response['items'][0]['volumeInfo']['title'])): ?>
                            <tr>
                                <td><strong>Title:</strong></td>
                                <td><input type="text" name="title" value="<?php echo $decoded_response['items'][0]['volumeInfo']['title']; ?>"></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (isset($decoded_response['items'][0]['volumeInfo']['authors'][0])): ?>
                            <tr>
                                <td><strong>Authors:</strong></td>
                                <td><input type="text" name="author" value="<?php echo $decoded_response['items'][0]['volumeInfo']['authors'][0]; ?> "></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (isset($decoded_response['items'][0]['volumeInfo']['publisher'])): ?>
                            <tr>
                                <td><strong>Publisher:</strong></td>
                                <td><input type="text" name="publisher" value="<?php echo $decoded_response['items'][0]['volumeInfo']['publisher']; ?>"></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (isset($decoded_response['items'][0]['volumeInfo']['description'])): ?>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td><input type="text" name="description" value="<?php echo $decoded_response['items'][0]['volumeInfo']['description']; ?>"></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (isset($decoded_response['items'][0]['volumeInfo']['pageCount'])): ?>
                            <tr>
                                <td><strong>Page Count:</strong></td>
                                <td><input type="text" name="num_of_pages" value="<?php echo $decoded_response['items'][0]['volumeInfo']['pageCount']; ?>"></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (isset($decoded_response['items'][0]['volumeInfo']['categories'][0])): ?>
                            <tr>
                                <td><strong>Categories:</strong></td>
                                <td><input type="text" name="category" value="<?php echo $decoded_response['items'][0]['volumeInfo']['categories'][0]; ?>"></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (isset($decoded_response['items'][0]['volumeInfo']['language'])): ?>
                            <tr>
                                <td><strong>Language:</strong></td>
                                <td><input type="text" name="language" value="<?php echo $decoded_response['items'][0]['volumeInfo']['language']; ?>"></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (isset($decoded_response['items'][0]['volumeInfo']['imageLinks']['thumbnail'])): ?>
                            <tr>
                                <td><strong>Image:</strong></td>
                                <td>
                                    <input type="hidden" name="picture_path" value="<?php echo $decoded_response['items'][0]['volumeInfo']['imageLinks']['thumbnail']; ?>">
                                    <img src="<?php echo $decoded_response['items'][0]['volumeInfo']['imageLinks']['thumbnail']; ?>">
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>

                    <button type="submit" name="contribute_books" class="btn btn-outline-danger row g-3 m-5 align-items-center">Contribute this book</button>
                </form>
            </div>
        <?php } ?>
    </div>
</div>


<?php require_once 'elements/footer.php'; ?>

