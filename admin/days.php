<?php
$session_options = ["read_and_close" => true];

include("inc/header.php");
include("../inc/db.php");

if(!is_logged_in()) {
    exit_with_code(403);
}
if(!isset($_GET["event_id"]) or !is_numeric($_GET["event_id"])) {
    exit_with_code(400);
}

header("Content-Type: application/json; charset=UTF-8");

$event_id = $_GET["event_id"];

$event_sql = "SELECT id
    FROM veranstaltungen
    WHERE id = ?";
$event_query = $db->query($event_sql, [$event_id]);
if ($event_query->rowCount() === 0) {
    exit_with_code(404);
}

$day_id = (!empty($_POST["day_id"]) and is_numeric($_POST["day_id"])) ? $_POST["day_id"] : null;

if(isset($_POST["delete"], $day_id)) {
    $deleted_query = $db->delete("tage", "tagID = ?", $day_id);
    $deleted_rows = $deleted_query->rowCount();
    if($deleted_rows === 0) {
        exit_with_code(404);
    }
    exit_with_code(200);
}

$date_pattern = "/day_date_\w/";

$errors = check_if_empty($_POST, [$date_pattern], "Erforderlich");

if ($errors !== []) {
    echo json_encode(["errors" => $errors]);
    exit_with_code(400);
}

$date_key = preg_grep_0($date_pattern, $_POST)[0];
$date = $_POST[$date_key];

$sql_day_exists = "SELECT EXISTS(
    SELECT *
    FROM tage
    WHERE tagDatum = ? AND veranstaltungsId = ?".
    (!empty($day_id) ? " AND tagID != ?" : null).
    ") AS day_exists";
$day_params = [$date, $event_id];
(!empty($day_id)) ? array_push($day_params, $day_id) : null;
$day_exists = $db->query($sql_day_exists, $day_params)->fetch()[0];

if($day_exists) {
    echo json_encode(["errors" => [
        "day_date" => "Existiert bereits"
    ]]);
    exit_with_code(400);
}

if(isset($_POST["add"])) {
    $db->insert("tage", [
        "tagDatum" => $date,
        "veranstaltungsId" => $event_id
    ]);
    echo json_encode(["day_id" => $db->mysql->lastInsertId()]);
} else if(isset($_POST["update"], $day_id)) {
    $db->update("tage", ["tagID" => $day_id], [
        "tagDatum" => $date
    ]);
} else {
    exit_with_code(400);
}

exit_with_code(200);
?>