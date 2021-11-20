<?php

include("../inc/db.php");
require("../config.php");
$query = $db->mysql->prepare("SHOW TABLES;");
$query->execute();
if ($query->rowCount() > 0) die(header('Location: index.php'));

$db->init_db();
echo "<meta http-equiv='refresh' content='5; url=../admin/'> Datenbank erfolgreich initialisiert."

?>