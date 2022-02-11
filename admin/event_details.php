<?php
include("../inc/db.php");
include("inc/header.php");


if (!is_logged_in()) redirect("../admin");

if(!isset($_GET["event_id"]) or !is_numeric($_GET["event_id"])) {
    exit_with_code(400);
}

if (!empty($_POST)) {
    include("edit.php");
}

$event_id = $_GET["event_id"];

$event_sql = "SELECT id,
    titel AS title,
    beschreibung AS description,
    anmeldestart AS reg_startdate,
    anmeldeende AS reg_enddate,
    emailVorlage AS email_template,
    stationen AS stations
    FROM veranstaltungen
    WHERE id = ?
    ORDER BY anmeldestart ASC,
    anmeldeende";
$event_query = $db->query($event_sql, [$event_id]);

if ($event_query->rowCount() === 0) {
    exit_with_code(404);
}

$event_data = $event_query->fetch();

echo $templates->render("admin::event_details", [
    "id" => $event_data["id"],
    "errors" => $data["errors"] ?? null,
    "title_value" => $event_data["title"],
    "description" => $event_data["description"],
    "email_template" => $event_data["email_template"],
    "stations_val" => $event_data["stations"],
    "reg_startdate_val" => $event_data["reg_startdate"],
    "reg_enddate_val" => $event_data["reg_enddate"]
]);

?>