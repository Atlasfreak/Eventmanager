<?php
$session_options = ["read_and_close" => true];

include("inc/header.php");
include("../inc/db.php");

if(!is_logged_in()) {
    exit_with_code(403);
}
if(!isset($_GET["day_id"]) or !is_numeric($_GET["day_id"])) {
    exit_with_code(400);
}

header("Content-Type: application/json; charset=UTF-8");

$day_id = $_GET["day_id"];

$day_sql = "SELECT tagID
    FROM tage
    WHERE tagID = ?";
$day_query = $db->query($day_sql, [$day_id]);
if ($day_query->rowCount() === 0) {
    exit_with_code(404);
}

$timewindow_id = (!empty($_POST["timewindow_id"]) and is_numeric($_POST["timewindow_id"])) ? $_POST["timewindow_id"] : null;

if(isset($_POST["delete"], $timewindow_id)) {
    $deleted_query = $db->delete("zeitfenster", "zeitfensterID = ?", $timewindow_id);
    $deleted_rows = $deleted_query->rowCount();
    if($deleted_rows === 0) {
        exit_with_code(404);
    }
    exit_with_code(200);
}

$time_from_pattern = "/timewindow_from_\w/";

$errors = check_if_empty($_POST, [$time_from_pattern, "/timewindow_max_participants_\w/"], "Erforderlich");

if ($errors !== []) {
    echo json_encode(["errors" => $errors]);
    exit_with_code(400);
}

$time_from_key = preg_grep_0($time_from_pattern, $_POST)[0];
$time_from = $_POST[$time_from_key];
$time_until = null;

$time_until_key = preg_grep_0("/timewindow_until_\w/", $_POST)[0];

if(!empty($_POST[$time_until_key])) {
    $time_until = $_POST[$time_until_key];
    if (strtotime($time_from) > strtotime($time_until)) {
        echo json_encode(["errors" => [
            "timewindow_until" => "Kann nicht vor der Startzeit liegen"]
        ]);
        exit_with_code(400);
    }
}

$sql_timewindow_exists = "SELECT EXISTS(
    SELECT *
    FROM zeitfenster
    WHERE von = ? AND bis <=> ? AND tagID = ?".
    ((!empty($timewindow_id)) ? " AND zeitfensterID != ?" : null).
    ") AS timewindow_exists";

$timewindow_params = [$time_from, $time_until, $day_id];
(!empty($timewindow_id)) ? array_push($timewindow_params, $timewindow_id) : null;
$timewindow_exists = $db->query($sql_timewindow_exists, $timewindow_params)->fetch()[0];

if($timewindow_exists) {
    echo json_encode(["errors" => [
        "timewindow_from" => "Existiert bereits"
    ]]);
    exit_with_code(400);
}

$max_participants_key = preg_grep_0("/timewindow_max_participants_\w/", $_POST)[0];
$max_participants = $_POST[$max_participants_key];

if(isset($_POST["add"])) {
    $db->insert("zeitfenster", [
        "maxTeilnehmer" => $_POST[$max_participants_key],
        "von" => $time_from,
        "bis" => $time_until,
        "tagID" => $day_id
    ]);
    echo json_encode(["timewindow_id" => $db->mysql->lastInsertId()]);
} else if(isset($_POST["update"], $timewindow_id)) {
    $db->update("zeitfenster", ["zeitfensterID" => $timewindow_id], [
        "maxTeilnehmer" => $_POST[$max_participants_key],
        "von" => $time_from,
        "bis" => $time_until,
    ]);
} else {
    exit_with_code(400);
}

exit_with_code(200);
?>