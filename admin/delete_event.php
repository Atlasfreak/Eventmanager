<?php
if(!isset($_GET["event_id"], $_SESSION["registration_username"], $_SESSION["registration_password"])) {
    die(header("Location:../admin"));
}
if(!is_numeric($_GET["event_id"])) {
    http_response_code(400);
    exit;
}

include("../inc/db.php");

$condition = "id = ?";
$query = $db->delete("veranstaltungen", $condition, $_GET["event_id"]);

?>