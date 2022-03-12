<?php
$session_options = ["read_and_close" => true];

include("inc/header.php");

if(!is_logged_in()) {
    exit_with_code(403);
}
if(!isset($_GET["event_id"]) or !is_numeric($_GET["event_id"])) {
    exit_with_code(400);
}

include("../inc/db.php");

$condition = "id = ?";
$query = $db->delete("veranstaltungen", $condition, $_GET["event_id"]);

?>