<?php
session_start();
require_once "elements/redirect.php";



if (!isset($_SESSION["username"]) || $_SESSION["role"] != 1) {
    session_destroy();
    redirectTo("index.php");
}
require_once 'elements/database.php';

if (isset($_GET["toDel"])) {
    try {
        $id = $_GET["toDel"];
        $delete_stmt = $con->prepare("CALL DeleteUser(:user_id)");
        $delete_stmt->bindParam(':user_id', $id);
        $delete_stmt->execute();
        header("Location: useradmin.php?deleteuser=1");
    } catch (PDOException $e) {
        echo "Error deleting user: " . $e->getMessage();
    }
}


//Change username
if (isset($_POST["id"]) && isset($_POST["username"])){
    if($_POST["username"] != "") {
        try {
            $id = $_POST["id"];
            $username = $_POST["username"];
            $update_username_stmt = $con->prepare("CALL UpdateUsername(:user_id, :new_username)");
            $update_username_stmt->bindParam(':user_id', $id);
            $update_username_stmt->bindParam(':new_username', $username);
            $update_username_stmt->execute();
            header("Location: useradmin.php?change=1");
        } catch (PDOException $e) {
            echo "Error updating username: " . $e->getMessage();
        }
    }
}

//Change first name
if (isset($_POST["id"]) && isset($_POST["first_name"])){
if($_POST["first_name"] != "") {
    try {
        $id = $_POST["id"];
        $first_name = $_POST["first_name"];
        $update_firstname_stmt = $con->prepare("CALL UpdateFirstName(:user_id, :new_first_name)");
        $update_firstname_stmt->bindParam(':user_id', $id);
        $update_firstname_stmt->bindParam(':new_first_name', $first_name);
        $update_firstname_stmt->execute();
        header("Location: useradmin.php?change=1");
    } catch (PDOException $e) {
        echo "Error updating username: " . $e->getMessage();
    }
}
}

//Change last name
if (isset($_POST["id"]) && isset($_POST["last_name"])){
if($_POST["last_name"] != "") {
    try {
        $id = $_POST["id"];
        $last_name = $_POST["last_name"];
        $update_lastname_stmt = $con->prepare("CALL UpdateLastName(:user_id, :new_last_name)");
        $update_lastname_stmt->bindParam(':user_id', $id);
        $update_lastname_stmt->bindParam(':new_last_name', $last_name);
        $update_lastname_stmt->execute();
        header("Location: useradmin.php?change=1");
    } catch (PDOException $e) {
        echo "Error updating last name: " . $e->getMessage();
    }
}
}


//change email
if (isset($_POST["id"]) && isset($_POST["email"])) {
        // Database update
        if ($_POST["email"] != "") {
            try {
                $id = $_POST["id"];
                $email = $_POST["email"];
                $update_email_stmt = $con->prepare("CALL UpdateEmail(:user_id, :new_email)");
                $update_email_stmt->bindParam(':user_id', $id);
                $update_email_stmt->bindParam(':new_email', $email);
                $update_email_stmt->execute();
                header("Location: useradmin.php?change=1");
            } catch (PDOException $e) {
                echo "Error updating email: " . $e->getMessage();
            }
        }
    }

//Change role
if (isset($_POST["id"]) && isset($_POST["role"])) {
    $id = $_POST["id"];
    $role = $_POST["role"];

    // Map role names to database values
    $roleValue = ($role == "admin") ? 1 : (($role == "user") ? 2 : null);

    if ($roleValue !== null) {
        try {
            // Prepare the call to the stored procedure
            $sth = $con->prepare("CALL UpdateRole(:user_id, :new_role)");

            // Bind parameters
            $sth->bindParam(':user_id', $id);
            $sth->bindParam(':new_role', $roleValue);

            // Execute the stored procedure
            if ($sth->execute()) {
                // Redirect after successful update
                header("Location: useradmin.php?change=1");
                exit(); // Always exit after redirect
            } else {
                echo "Error executing the stored procedure.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Handle invalid role input
        echo "Invalid role input!";
    }
}
//Change pass
if (isset($_POST["id"]) && isset($_POST["pwd_hash"])) {
    if ($_POST["pwd_hash"] != "") {
        $id = $_POST["id"];
        $password = $_POST["pwd_hash"];

        // put pass in hash
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        echo "ID: " . $id . "<br>";
        echo "Password Hash: " . $password_hash . "<br>";

        try {
            // Prepare the call to the stored procedure
            $sth = $con->prepare("CALL UpdatePassword(:user_id, :new_password)");

            // Bind parameters
            $sth->bindParam(':user_id', $id);
            $sth->bindParam(':new_password', $password_hash);

            // Execute the stored procedure
            if ($sth->execute()) {
                // Redirect after successful update
                header("Location: useradmin.php?change=1");
                exit(); // Always exit after redirect
            } else {
                // Handle database errors
                echo "Error updating password.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

require_once 'elements/header.php';
require_once 'elements/nav.php';
?>




<main class="container">

    <div class="container">
        <div class="mt-5 d-flex justify-content-between"><b><a href="admin.php"><i class="bi bi-arrow-left"></i>Back</a></b>
        </div>

        <h1 class="d-flex justify-content-center m-3" style="font-family: 'Soria'; font-size: 40pt;">User Administration</h1>

        <h2 class="mb-3" style="font-family: 'Soria'; font-size: 21pt">Filter for last name</h2>

        <input class="form-control rounded-0 mb-4 w-25" type="search" placeholder="Search" aria-label="Search" name="search" id="myInput" onkeyup="myFunction()">

    <table id="myTable" class="table table-hover">

        <thead>
        <tr>
            <th scope="col">Picture</th>
            <th scope="col">Username</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>

            <th></th>
            <th></th>
        </tr>
        </thead>

        <tbody>

        <?php
        $stmt_select_users = $con->prepare("CALL SelectUser()");
        $stmt_select_users->execute();
        $result_2 = $stmt_select_users->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($result_2 as $item) { ?>

            <tr>
                <td>
                    <img src="<?php echo isset($item["picturePath"]) ? $item["picturePath"] : 'assets/pictures/profile.jpg'; ?>"
                         alt="<?php echo isset($item["username"]) ? $item["username"] : 'Default Username'; ?>"
                         class="img-fluid"
                         style="border-radius: 50%; width: 120px; height: 120px; object-position: center; object-fit: cover;">
                </td>
                <td scope="row"><?php echo $item['username']; ?><br></td>
                <td scope="row"><?php echo $item['first_name']; ?><br></td>
                <td scope="row"><?php echo $item['last_name']; ?><br></td>
                <td scope="row"><?php echo $item['email']; ?><br></td>
                <td scope="row"><?php echo $item['role_name']; ?><br></td>


                <td>
                <a href="useradmin.php?id=<?php echo $item["id"] ?>" data-bs-toggle="modal"
                   class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center"
                   data-bs-target="#modal-info<?php echo $item["id"] ?>" style="height: 40px; width: 40px;">
                    <i class="fa-solid fa-pencil  rounded-circle"></i>
                </a>
                </td>



                <td>
                    <a class= "btn btn-outline-danger rounded-circle" href='useradmin.php?toDel=<?php echo $item['id'] ?>'>
                        <i class="bi bi-trash3-fill"></i>
                    </a>
                </td>

            </tr>

            <div class="modal fade" id="modal-info<?php echo $item["id"] ?>" tabindex="-1"
                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit User</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">


                            <form action="useradmin.php" method="POST">

                                <div class="container">
                                    <div class="row">
                                        <input type="hidden" class="form-control" name="id" value="<?php echo $item['id'] ?>">
                                    </div>

                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" id="username"
                                                   aria-describedby="emailHelp">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name" id="first_name"
                                                   aria-describedby="emailHelp">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name" id="last_name"
                                                   aria-describedby="emailHelp">
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                   aria-describedby="emailHelp">

                                            <?php
                                            // Display error message if it exists
                                            if (!empty($error_message)) {
                                                echo '<div class="error-message">' . $error_message . '</div>';
                                            }
                                            ?>

                                        </div>
                                    </div>



                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <input type="text" class="form-control" name="role" id="role"
                                                   aria-describedby="emailHelp">
                                        </div>
                                    </div>



                                    <div class="row">
                                        <div class="mb-3">
                                            <label for="pwd_hash" class="form-label">Password</label>
                                            <input type="text" class="form-control" name="pwd_hash" id="pwd_hash"
                                                   aria-describedby="emailHelp"  <?php $pwd_hash ?>>



                                        </div>
                                    </div>

                                    <div class="row">
                                        <br>
                                        <button type="submit" id="editButton" name="edit" class="btn btn-primary b-3">Save changes
                                        </button>
                                        <button type="button" class="btn btn-secondary b-3" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                    </div>






                            </form>

                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        </tbody>
    </table>
</main>


<script>
    function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[3];
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
require_once "elements/modal.php";

if (isset($_GET["deleteuser"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "The user was deleted!", "success");
}

if (isset($_GET["change"])) {
    createMyModal("<i class='fa-solid fa-circle-check fa-2x' style='color: #ffffff;'></i>", "Changes are done successfully!", "success");
}



?>




