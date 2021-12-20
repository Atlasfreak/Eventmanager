<?php
include("inc/db.php");
include("inc/header.php");
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_GET["day"]) or !($_GET["day"])) {
    http_response_code(400);
    exit;
}

$sql_timewindows = "SELECT zeitfensterID, von, bis, maxTeilnehmer FROM zeitfenster WHERE tagID = ? ORDER BY von, bis";
$query = $db->query($sql_timewindows, array($_GET["day"]));
$data_timewindows = $query->fetchAll();

$timewindows = array();

foreach ($data_timewindows as $timewindow) {
    $max_participants = $timewindow["maxTeilnehmer"];
    $participants = $db->get_participants(null, array($timewindow["zeitfensterID"]));
    $timewindow["participants"] = $participants;
    if ($participants >= $max_participants) {
        $timewindow["disabled"] = true;
    } else {
        $timewindow["disabled"] = false;
    }
    array_push($timewindows, $timewindow);
}

echo json_encode($timewindows);

?>