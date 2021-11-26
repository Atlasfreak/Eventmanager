<?php
// error_reporting(-1);
include("inc/db.php");
include("inc/header.php");

function render_regsitration($templates, $data) {
    return $templates->render("main::event_registration", array(
        "title" => $data["title"],
        "description" => $data["description"], // ACHTUNG dieser werd wird nicht escaped. Hier wird HTML erwartet, in der Datenbank ist KEIN HTML hinterlegt!
        "days" => $data["days"],
        "event_id" => $data["event_id"],
        "errors" => $data["errors"] ?? array(),
        "values" => $data["values"] ?? array(),
    ));
}

function get_event_data($id, $db) {
    $sql_event = "SELECT beschreibung, titel, anmeldestart, anmeldeende FROM veranstaltungen WHERE id = ? AND anmeldestart <= CURRENT_TIMESTAMP AND anmeldeende >= CURRENT_TIMESTAMP";
    $query_event = $db->query($sql_event, array($id));
    if ($query_event->rowCount() === 0){
        echo "<meta http-equiv='refresh' content='5; url=".ANMELDUNG_URL."'>";
        echo "Für diese Veranstaltung kann man sich nicht anmelden.";
        exit;
    }

    $sql_days = "SELECT tagDatum, tagID FROM tage WHERE veranstaltungsId = ? ORDER BY tagDatum";
    $query_days = $db->query($sql_days, array($id));
    if ($query_days->rowCount() === 0) {
        echo "<meta http-equiv='refresh' content='5; url=".ANMELDUNG_URL."'>";
        echo "Dieser Veranstaltung wurden keine Tage zugewiesen, bitte schauen sie später nochmal vorbei.";
        exit;
    }

    $data_event = $query_event->fetch();
    $description = parse_delta($data_event["beschreibung"]);
    $data_days = $query_days->fetchAll();

    return array(
        "title" => $data_event["titel"],
        "description" => $description,
        "days" => $data_days,
        "event_id" => $_GET["event"],
    );
}

function check_val($data, $key) {
    if(key_exists($key, $data)) {
        return $data[$key];
    }
    return false;
}

if (isset($_GET["event"])) {
    if (isset($_POST["street"])){
        // var_dump($_POST["street"]);
        // var_dump($_POST["city"]);
        include("send.php");
        exit;
    }
    $data = get_event_data($_GET["event"], $db);

    echo render_regsitration($templates, $data);
    exit;
}

$sql_events = "SELECT id, beschreibung, titel, anmeldeende FROM veranstaltungen WHERE anmeldestart <= CURRENT_TIMESTAMP AND anmeldeende >= CURRENT_TIMESTAMP";
$query_events = $db->query($sql_events);
$count_events = $query_events->rowCount();
$events = $query_events->fetchAll();

echo $templates->render("main::events_overview", array(
    "title" => "Veranstaltungen",
    "count_events" => $count_events,
    "events" => $events,
));

?>