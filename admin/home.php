<?php
use Atlasfreak\Eventmanager\Update;
use Atlasfreak\Eventmanager\CommandNotFound;
basename($_SERVER['PHP_SELF']) == basename(__FILE__) && die();

require("classes/Update.php");

$sql_events = "SELECT id, titel FROM veranstaltungen ORDER BY anmeldestart ASC, anmeldeende";
$query_events = $db->query($sql_events);
$data_events = $query_events->fetchAll();

$events = array();

foreach ($data_events as $event) {
    $sql_days = "SELECT tagID FROM tage WHERE veranstaltungsId = ?";
    $query_days = $db->query($sql_days, array($event["id"]));
    $data_days = $query_days->fetchAll(PDO::FETCH_COLUMN, 0);

    $ids_days = array(implode(",", $data_days));

    $sql_timewindows_ids = "SELECT zeitfensterID FROM zeitfenster WHERE FIND_IN_SET(tagID, ?)";
    $query_timewindows_ids = $db->query($sql_timewindows_ids, $ids_days);
    $data_timewindows_ids = $query_timewindows_ids->fetchAll(PDO::FETCH_COLUMN, 0);
    $max_participants = 0;
    $participants = 0;
    if($query_timewindows_ids->rowCount() > 0){
        $sql_max_participants = "SELECT CASE WHEN SUM(maxTeilnehmer) IS NULL THEN 0 ELSE SUM(maxTeilnehmer) END as maxTeilnehmer FROM zeitfenster WHERE FIND_IN_SET(tagID, ?)";
        $query_max_participants = $db->query($sql_max_participants, $ids_days);
        $max_participants = (int) $query_max_participants->fetch()["maxTeilnehmer"];

        $sql_participants = "SELECT CASE WHEN SUM(anzahl) IS NULL THEN 0 ELSE SUM(anzahl) END as anzahlTeilnehmer FROM teilnehmer WHERE FIND_IN_SET(zeitfensterID, ?)";
        $query_participants = $db->query($sql_participants, array(implode(",", $data_timewindows_ids)));
        $participants = (int) $query_participants->fetch()["anzahlTeilnehmer"];
    }
    $event["max_participants"] = $max_participants;
    $event["participants"] = $participants;
    array_push($events, $event);
}

$api = new Update();
try {
    [$version, $new] = $api->check_version();
} catch (CommandNotFound $th) {
    [$version, $new] = [false, false];
}

echo $templates->render("admin::home", [
    "events" => $events,
    "new" => $new,
    "version" => $version,
]);
?>