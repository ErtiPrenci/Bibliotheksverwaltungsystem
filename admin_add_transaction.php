<?php
session_start();
require_once "elements/redirect.php";

if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}

require_once "elements/database.php";

$stmt_all_users = $con->prepare("SELECT * FROM User");
$stmt_all_users->execute();
$all_users = $stmt_all_users->fetchAll(PDO::FETCH_ASSOC);

require_once "elements/header.php";
require_once "elements/nav.php";
?>

<header class="text-center my-5">
    <div class="container">
        <div class="mt-5 d-flex justify-content-between">
            <b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
        </div>
        <h1 style="font-family: 'Soria'; font-size: 40pt;">List of Students</h1>
    </div>
</header>

<main class="container mb-4">
    <div class="mb-5">
        <h3 class="mb-3" style="font-family: 'Soria'; font-size: 26pt">Waiting for Confirmation</h3>
        <input class="form-control rounded-0 mb-4 w-25" type="search" placeholder="Search" aria-label="Search" name="search" id="myInput" onkeyup="myFunction()">

        <table id="myTable" class="table" style="border-collapse: collapse;">
            <thead>
            <tr>
                <th scope="col">Picture</th>
                <th scope="col">Username</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col"></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach($all_users as $user) { ?>
                <tr>
                    <td>
                        <?php if($user["picturePath"] != null) {?>
                            <img src="<?php echo $user["picturePath"]?>" alt="<?php echo $user["username"]?>" class="img-fluid" style="border-radius: 50%; width: 120px; height: 120px; object-position: center; object-fit:cover;">
                        <?php } else { ?>
                            <img src="assets/pictures/users/admin.png" alt="<?php echo $user["username"]?>" class="img-fluid" style="border-radius: 50%; width: 120px; height: 120px; object-position: center; object-fit:cover;">
                        <?php } ?>
                    </td>
                    <td><?php echo $user["username"]?></td>
                    <td><?php echo $user["first_name"] . " " . $user["last_name"]?></td>
                    <td><?php echo $user["email"]?></td>
                    <td>
                        <a href="choose_book_for_transaction.php?userid=<?php echo $user["id"]?>"
                           class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" style="height: 40px; width: 40px;">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[2];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>

<?php
require_once 'elements/footer.php';
?>
