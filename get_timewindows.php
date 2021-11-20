<?php
include("inc/db.php");
include("inc/header.php");
header("Content-Type: application/json; charset=UTF-8");

if (!($_GET["day"])) {
    http_response_code(400);
    exit;
}

$sql_timewindows = "SELECT zeitfensterID, von, bis FROM zeitfenster WHERE tagID = ? ORDER BY von, bis";
$query = $db->query($sql_timewindows, array($_GET["day"]));
$data = $query->fetchAll();

echo json_encode($data);

?>