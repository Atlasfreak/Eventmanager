<?php
session_start();

if(!isset($_GET["participant_id"], $_SESSION["registration_username"], $_SESSION["registration_password"])) {
    http_response_code(403);
    exit;
}
if(!is_numeric($_GET["participant_id"])) {
    http_response_code(400);
    exit;
}

include("../inc/db.php");

$condition = "id = ?";
$query = $db->delete("teilnehmer", $condition, $_GET["participant_id"]);

?>