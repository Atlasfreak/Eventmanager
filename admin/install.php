<?php
include_once("../inc/db.php");
require_once("../config.php");

if (!CONFIG_DATA["general"]["debug"] and strlen(CONFIG_DATA["general"]["secret"]) < 32) {
    $config = get_raw_config();
    $secret = bin2hex(random_bytes(30));
    $config = preg_replace("/(secret *= *\")(.+)(\")/m", "\${1}".$secret."$3", $config);
    if (!is_null($config)) {
        overwrite_config($config);
    }
}
$query = $db->mysql->prepare("SHOW TABLES;");
$query->execute();
if ($query->rowCount() >= $db->count_tables()) die(header('Location: index.php'));

$db->init_db();
echo "<meta http-equiv='refresh' content='5; url=../admin/add_admin'> Datenbank erfolgreich initialisiert."

?>