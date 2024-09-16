<?php
session_start();

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
?>
<div class="container">
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Message</th>
            <th scope="col">Send Date</th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $sql = "SELECT * FROM Notification WHERE user_id = :user_id";
        $sth = $con->prepare($sql);
        $sth->bindParam('user_id', $user_id);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $item) {?>

            <tr>
                <td><?php echo $item['Username']; ?></td>
                <td><?php echo $item['Email']; ?></td>
                <td><?php echo $item['Message']; ?></td>
                <td><?php echo $item['Sent_date']; ?></td>
                <?php
                }
                } else {
                    echo "<p>User ID not set in session.</p>";
                }
                ?>
        </tbody>
    </table>
</div>
<?php
require_once "elements/footer.php";
?>
