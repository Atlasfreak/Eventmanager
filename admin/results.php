<?php
include("inc/header.php");;

if(!is_logged_in()) redirect("../admin");

include('../inc/db.php');

function timewindow_string($from, $until) {
    $string = strftime("%H:%M", strtotime($from));
    if ($until !== null) {
        $string .= " - ".strftime("%H:%M", strtotime($until));
    }
    return $string;
}

if(!isset($_GET["event"])) {
    exit_with_code(400);
}

$sql_event = "SELECT id, stationen FROM veranstaltungen WHERE id = ?";
$query_event = $db->query($sql_event, array($_GET["event"]));
if ($query_event->rowCount() === 0){
    exit_with_code(400);
}

$data_event = $query_event->fetch();
$stations = $data_event["stationen"];

$sql_days = "SELECT tagID, tagDatum FROM tage WHERE veranstaltungsId = ?";
$query_days = $db->query($sql_days, array($data_event["id"]));
$data_days = $query_days->fetchAll();
$data_timewindows = array();
$data_participants = array();
$results = array();
$dummy_station = (!is_null($stations) and $stations > 0) ? "" : null;

foreach($data_days as $row_day) {
    $tagID = $row_day["tagID"];

    $timewindows_sql = "SELECT von, bis, maxTeilnehmer, zeitfensterID FROM zeitfenster WHERE tagID=? ORDER BY von, bis";
    $timewindows_qry = $db->query($timewindows_sql, array($tagID));
    $data_timewindows[$tagID] = $timewindows_qry->fetchAll();
    $results[$tagID] = 0;

    foreach($data_timewindows[$tagID] as $row_timewindow) {

        $max_participants = $row_timewindow["maxTeilnehmer"];
        $timewindow_id = $row_timewindow["zeitfensterID"];

        $participants_sql = "SELECT nachname, vorname, strasse, ort, email, telefon, id, anmeldestation
        FROM teilnehmer
        WHERE ZeitfensterID = ?
        ORDER BY nachname";
        $participants_qry = $db->query($participants_sql, array($timewindow_id));
        $data_participants[$timewindow_id] = $participants_qry->fetchAll();

        $results[$tagID] += $participants_qry->rowCount();
        $n = $participants_qry->rowCount();
        while ($n < $max_participants) {
            $n++;
            array_push($data_participants[$timewindow_id], array(
                "nachname" => "",
                "vorname" => "",
                "strasse" => "",
                "ort" => "",
                "email" => "",
                "telefon" => "",
                "id" => "",
                "anmeldestation" => $dummy_station
                ));
        }
    }
}

$titles = ["Nachname", "Vorname", "Strasse", "Ort", "E-Mail", "Telefon", "Zeitfenster", ["Editieren", "d-print-none"]];
if (!is_null($stations) and $stations > 0) {
    array_splice($titles, -1, 0, "Station");
}
echo $templates->render("admin::results", [
    "stations" => $stations,
    "data_participants" => $data_participants,
    "data_timewindows" => $data_timewindows,
    "data_days" => $data_days,
    "results" => $results,
    // titles ist ein Array aus Strings mit den Spalten Überschriften, falls ein Element ein Array ist dann mit der Form ["Überschrift", "HTML Klasse(n)"]
    "titles" => $titles,
]);
?>