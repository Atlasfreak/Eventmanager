<?php
include("../inc/db.php");

include("inc/header.php");

if(isset($_SESSION["registration_username"],$_SESSION["registration_password"])) {
    include("home.php");
}
else {
    include("login.php");
}
?>
