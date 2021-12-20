<?php
define("ANMELDUNG_URL", "/anmeldung"); // relativer Pfad zur Domain

function get_config() {
    $filename = "config.ini";
    $ini_array = parse_ini_file($filename, true);
    return $ini_array;
}

define("CONFIG_DATA", get_config());
?>