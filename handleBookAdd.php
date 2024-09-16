<?php
session_start();
require_once "elements/redirect.php";

//if (!isset($_GET["isbn"])) {
  //  redirectTo("index.php");
//}

$isbn = $_GET["isbn"];

$url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn;

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);

if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    redirectTo("index.php?errorCurlResponse=1");
}

curl_close($curl);
$decoded_response = json_decode($curl_response, true);

echo "<pre>";
var_export($decoded_response);
echo "</pre>";

if($decoded_response["totalItems"] != 0) {
    require_once "elements/database.php";

    $error = false;
    $title = $decoded_response["items"][0]["volumeInfo"]["title"];

    $author = $decoded_response["items"][0]["volumeInfo"]["authors"][0];
    $publisher = $decoded_response["items"][0]["volumeInfo"]["publisher"];
    $category = $decoded_response["items"][0]["volumeInfo"]["categories"][0];

    $pageCnt = $decoded_response["items"][0]["volumeInfo"]["pageCount"];
    $language = $decoded_response["items"][0]["volumeInfo"]["language"];

    if(isset($decoded_response["items"][0]["volumeInfo"]["imageLinks"]["thumbnail"])) {
        $pic = $decoded_response["items"][0]["volumeInfo"]["imageLinks"]["thumbnail"];
    } else {
        $pic = null;
    }

    if(isset($decoded_response["items"][0]["volumeInfo"]["description"])) {
        $desc = $decoded_response["items"][0]["volumeInfo"]["description"];
    } else {
        $desc = null;
    }

    /*echo $title;
    echo $author;
    echo $publisher;
    echo $pic;
    echo $desc;
    echo $pageCnt;
    echo $language;
    echo $category;*/

    if($author != null) {
        $insert_into_author_stmt = $con->prepare("INSERT IGNORE INTO Author (name) VALUES (:authorname)");
        $insert_into_author_stmt->execute(["authorname" => $author]);
    }

    if($publisher != null) {
        $insert_into_publisher_stmt = $con->prepare("INSERT IGNORE INTO Publisher (name) VALUES (:publishername)");
        $insert_into_publisher_stmt->execute(["publishername" => $publisher]);
    }

    $publisher_stmt = $con->prepare("SELECT id FROM Publisher WHERE name = :publisher");
    $publisher_stmt->execute(["publisher" => $publisher]);
    $publishers = $publisher_stmt->fetchAll(PDO::FETCH_ASSOC);

    $author_stmt = $con->prepare("SELECT id FROM Author WHERE name = :author");
    $author_stmt->execute(["author" => $author]);
    $authors = $author_stmt->fetchAll(PDO::FETCH_ASSOC);

    $cat_stmt = $con->prepare("SELECT id FROM Category WHERE name = :category");
    $cat_stmt->execute(["category" => $category]);
    $cat = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($cat)) {
        $cat_id = $cat[0]["id"];
    } else {
        $cat_id = 16;
    }

    if(!empty($publisher)) {
        $publisher_id = $publishers[0]["id"];
    } else {
        $publisher_id = 866;
    }

    if(!empty($author)) {
        $author_id = $authors[0]["id"];
    } else {
        $author_id = 857;
    }

    $insert_into_book_stmt = $con->prepare("INSERT IGNORE INTO Book (isbn, title, publisher, language, num_of_pages, description, inStock, picture_path, accepted)
                                                    VALUES (:isbn, :title, :publisher, :book_language, :num_of_pages, :description, 1, :picture_path, 0)");
    $insert_into_book_stmt->execute([
        "isbn" => $isbn,
        "title" => $title,
        "publisher" => $publisher_id,
        "book_language" => $language,
        "num_of_pages" => $pageCnt,
        "description" => $desc,
        "picture_path" => $pic
    ]);

    $insert_into_book_author_stmt = $con->prepare("INSERT IGNORE INTO Book_Author (isbn, author_id) VALUES (:isbn, :author_id)");
    $insert_into_book_author_stmt->execute([
        "isbn" => $isbn,
        "author_id" => $author_id
    ]);

    $insert_into_book_cat_stmt = $con->prepare("INSERT IGNORE INTO Book_Category (isbn, category_id) VALUES (:isbn, :category_id)");
    $insert_into_book_cat_stmt->execute([
        "isbn" => $isbn,
        "category_id" => $cat_id
    ]);

    //if($error) {
      //  redirectTo("index.php?errorAddingBook=1");
    //}
}

//else {
      //  redirectTo("index.php?errorBookNotFound=1");
//}