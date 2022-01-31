<?php
include("../inc/db.php");

include("inc/header.php");

if ($db->query("SELECT id FROM admin")->rowCount() == 0) redirect("/");

if(is_logged_in()) {
    include("home.php");
}
else {
    include("login.php");
}
?>
