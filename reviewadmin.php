<?php
session_start();
require_once "elements/redirect.php";



if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once 'elements/database.php';

if (isset($_GET["toDel"])) {
    $id = $_GET["toDel"];
    $sql = "DELETE FROM Review WHERE id = :id";
    $sth = $con->prepare($sql);
    $sth->bindParam(':id', $id);
    if ($sth->execute()) {
        redirectTo("reviewadmin.php?delete=1");
    }
}

require_once 'elements/header.php';
require_once 'elements/nav.php';
?>

<main class="container">

    <div class="container">
        <div class="mt-5 d-flex justify-content-between"><b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
        </div>

        <h1 class="d-flex justify-content-center m-3" style="font-family: 'Soria'; font-size: 40pt;">Review Administration</h1>

        <table class="table table-hover">

            <thead>
            <tr>
                <th scope="col">ISBN</th>
                <th scope="col">Rating</th>
                <th scope="col">Comment</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <?php
            $stmt_2 = $con->prepare("SELECT * FROM Review");
            $stmt_2->execute();
            $result_2 = $stmt_2->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result_2 as $item) { ?>

                <tr>
                    <td scope="row"><?php echo $item['isbn']; ?><br></td>
                    <td scope="row"><?php echo $item['rating']; ?><br></td>
                    <td scope="row"><?php echo $item['comment']; ?><br></td>

                    <td>
                        <a class= "btn btn-outline-danger rounded-circle" href='reviewadmin.php?toDel=<?php echo $item['id'] ?>'>
                            <i class="bi bi-trash3-fill"></i>
                        </a>
                    </td>
                </tr>

            <?php } ?>

            </tbody>
        </table>

</main>

<?php
require_once 'elements/footer.php';
require_once "elements/modal.php";

if (isset($_GET["delete"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "The review was deleted!", "success");
}

?>
