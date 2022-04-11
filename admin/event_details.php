<?php
include("../inc/db.php");
include("inc/header.php");


if (!is_logged_in()) redirect("../admin");

if(!isset($_GET["event_id"]) or !is_numeric($_GET["event_id"])) {
    exit_with_code(400);
}

if (!empty($_POST)) {
    if (isset($_POST["send_email"])) {
        include("mails.php");
    } else {
        include("edit.php");
    }
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

$participants_sql = "SELECT teilnehmer.nachname AS lastname,
        teilnehmer.vorname AS firstname,
        teilnehmer.email AS email,
        teilnehmer.id AS id
    FROM teilnehmer,
        veranstaltungen,
        tage,
        zeitfenster
    WHERE veranstaltungen.id = tage.veranstaltungsId
        AND
        tage.tagID = zeitfenster.tagID
        AND
        teilnehmer.ZeitfensterID = zeitfenster.ZeitfensterID
        AND
        veranstaltungen.id = ?
    ORDER BY teilnehmer.nachname";
$participants_query = $db->query($participants_sql, [$event_id]);
$participants_data = $participants_query->fetchAll();

$days_sql = "SELECT tage.tagDatum AS `date`, tage.tagID AS `id`
    FROM tage
    WHERE tage.veranstaltungsId = ?
    ORDER BY tage.tagDatum";
$days_data = $db->query($days_sql, [$event_id])->fetchAll();
$debug = array_search(5, array_column($days_data, "id"));

$timewindows_sql = "SELECT
        zeitfenster.von AS `from`,
        zeitfenster.bis AS `until`,
        zeitfenster.ZeitfensterID AS `id`,
        zeitfenster.maxTeilnehmer AS `max_participants`,
        zeitfenster.tagID AS `day_id`
    FROM zeitfenster,
        tage
    WHERE tage.veranstaltungsId = ?
        AND
        tage.tagID = zeitfenster.tagID";
$timewindows_query = $db->query($timewindows_sql, [$event_id]);
$timewindows_data = $timewindows_query->fetchAll();

foreach ($timewindows_data as $timewindow) {
    $day_key = array_search(
        $timewindow["day_id"],
        array_column($days_data, "id")
    );
    $day = $days_data[$day_key];
    if (!isset($day["timewindows"])) $day["timewindows"] = [];
    array_push($day["timewindows"], $timewindow);
    $days_data[$day_key] = $day;
}

if (isset($_GET["email"])) {
    $get_email = $_GET["email"];
    $emails_selected = (is_numeric($get_email) ? [$get_email] :
        (is_array($get_email) ? $get_email : exit_with_code(400))
    );
}

echo $templates->render("admin::event_details", [
    "id" => $event_data["id"],
    "errors" => $data["errors"] ?? null,
    "title_value" => $event_data["title"],
    "description" => $event_data["description"],
    "email_template" => $event_data["email_template"],
    "stations_val" => $event_data["stations"],
    "reg_startdate_val" => $event_data["reg_startdate"],
    "reg_enddate_val" => $event_data["reg_enddate"],
    "data_participants" => $participants_data,
    "emails_selected" => $emails_selected ?? array(),
    "days" => $days_data,
]);

?>