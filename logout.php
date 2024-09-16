<?php
session_start();
session_destroy();

unset($_SESSION["username"]);
unset($_SESSION["firstName"]);
unset($_SESSION["role"]);
unset($_SESSION["lastName"]);
unset($_SESSION["email"]);
unset($_SESSION["pwd_hash"]);

require_once "elements/redirect.php";
redirectTo("index.php?logout=1");
?>