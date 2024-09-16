<?php
ob_start();
session_start();

require_once "elements/redirect.php";
require_once "elements/database.php";
require_once "elements/header.php";
?>

<style>
    body {
        background-color: #efedee;
    }
</style>

<?php
require_once "elements/nav.php";

$added_to_favorites = false;
$removed_from_favorites = false;
if (isset($_GET['isbn'])) {
    $isbn = $_GET["isbn"];
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION["user_id"];
        try {
            // Check if the book is a favorite
            $check_stmt = $con->prepare("CALL CheckFavoriteBook(:user_id, :isbn)");
            $check_stmt->bindParam(':user_id', $user_id);
            $check_stmt->bindParam(':isbn', $isbn);
            if ($check_stmt->execute()) {
                $count = $check_stmt->fetchColumn();
                if ($count > 0) {
                    // Remove from favorites
                    $remove_stmt = $con->prepare("CALL RemoveFromFavorites(:user_id, :isbn)");
                    $remove_stmt->bindParam(':user_id', $user_id);
                    $remove_stmt->bindParam(':isbn', $isbn);
                    if ($remove_stmt->execute()) {
                        $removed_from_favorites = true;
                    } else {
                        echo "Error executing the SQL statement for removal";
                    }
                } else {
                    // Add to favorites
                    $add_stmt = $con->prepare("CALL AddToFavorites(:user_id, :isbn)");
                    $add_stmt->bindParam(':user_id', $user_id);
                    $add_stmt->bindParam(':isbn', $isbn);
                    if ($add_stmt->execute()) {
                        $added_to_favorites = true;
                    } else {
                        echo "Error executing the SQL statement for addition";
                    }
                }
            } else {
                echo "Error executing the SQL statement for checking.";
            }
        } catch (PDOException $e) {
            echo "PDO Exception: " . $e->getMessage();
        }
    } else {
        echo "Not logged in";
    }
} else {
    echo "ISBN not correctly received";
}

if ($removed_from_favorites) {
    redirectTo("books.php?removed_from_favorites=1");
} else if ($added_to_favorites) {
    redirectTo("books.php?added_to_favorites=1");
}

require_once "elements/footer.php";
ob_end_flush();
?>
