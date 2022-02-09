<?php
include("inc/db.php");
include("inc/header.php");

session_start();

function render_registration(\League\Plates\Engine $templates, array $data): string {
    return $templates->render("main::event_registration", array(
        "title" => $data["title"],
        "description" => $data["description"], // ACHTUNG dieser Wert wird nicht escaped. Hier wird HTML erwartet, in der Datenbank ist KEIN HTML hinterlegt!
        "days" => $data["days"],
        "event_id" => $data["event_id"],
        "errors" => $data["errors"] ?? array(),
        "values" => $data["values"] ?? array(),
    ));
}

function render_overview(\League\Plates\Engine $templates, array $data): string {
    $data["messages"] = $data["messages"] ?? array();
    if (isset($data["errors"])) {
        $data["errors"] = add_type_to_msgs($data["errors"], "danger");
        $data["messages"] = array_merge($data["messages"], $data["errors"]);
    }
    if (isset($_SESSION["messages"])) {
        $data["messages"] = array_merge($data["messages"], $_SESSION["messages"]);
    }
    unset($_SESSION["messages"]);
    return $templates->render("main::events_overview", array(
        "title" => "Veranstaltungen",
        "count_events" => $data["count_events"],
        "events" => $data["events"],
        "messages" => $data["messages"] ?? array(),
    ));
}

function render_events_data(\League\Plates\Engine $templates, Database $db, array $data = array()) {
    $data = array_merge($data, get_events_data($db));
    echo render_overview($templates, $data);
    exit;
}

function get_events_data(Database $db): array {
    $sql_events = "SELECT id, beschreibung, titel, anmeldeende FROM veranstaltungen WHERE anmeldestart <= CURRENT_TIMESTAMP AND anmeldeende >= CURRENT_TIMESTAMP";
    $query_events = $db->query($sql_events);
    $count_events = $query_events->rowCount();
    $events = $query_events->fetchAll();
    return array(
        "events" => $events,
        "count_events" => $count_events
    );
}

function get_event_data(int $id, Database $db): array {
    $sql_event = "SELECT beschreibung, titel, anmeldestart, anmeldeende, stationen FROM veranstaltungen WHERE id = ? AND anmeldestart <= CURRENT_TIMESTAMP AND anmeldeende >= CURRENT_TIMESTAMP";
    $query_event = $db->query($sql_event, array($id));
    if ($query_event->rowCount() === 0){
        return array("error" => "closed");
    }

    $sql_days = "SELECT tagDatum, tagID FROM tage WHERE veranstaltungsId = ? ORDER BY tagDatum";
    $query_days = $db->query($sql_days, array($id));
    if ($query_days->rowCount() === 0) {
        return array("error" => "no_days");
    }

    $data_event = $query_event->fetch();
    $description = parse_delta($data_event["beschreibung"]);
    $data_days = $query_days->fetchAll();

    return array(
        "title" => $data_event["titel"],
        "description" => $description,
        "days" => $data_days,
        "event_id" => $_GET["event"],
        "stations" => $data_event["stationen"]
    );
}

function render_event_errors(\League\Plates\Engine $templates, Database $db, array $data) {
    if (!isset($data["error"])) return;

    if ($data["error"] === "closed") {
        $msg = "Für diese Veranstaltung kann man sich nicht anmelden.";
    } elseif ($data["error"] === "no_days") {
        $msg = "Dieser Veranstaltung wurden keine Tage zugewiesen. Bitte versuche es später nochmal.";
    }

    render_events_data($templates, $db, ["errors" => [["msg" => $msg]]]);
}

function check_val(array $data, string $key) {
    if(key_exists($key, $data)) {
        return $data[$key];
    }
    return false;
}

if (isset($_GET["event"])) {
    if (!empty($_POST)){
        include("send.php");
        exit;
    }
    $data = get_event_data($_GET["event"], $db);
    render_event_errors($templates, $db, $data);

    echo render_registration($templates, $data);
    exit;
}

echo render_events_data($templates, $db);

?>