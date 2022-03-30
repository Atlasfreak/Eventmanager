<?php
basename($_SERVER['PHP_SELF']) == basename(__FILE__) && die();

define("CONFIG_FILENAME", "config.ini");

function get_config() {
    $filename = CONFIG_FILENAME;
    $ini_array = parse_ini_file($filename, true);
    return $ini_array;
}

function get_raw_config() {
    $filename = CONFIG_FILENAME;
    return file_get_contents($filename, true);
}

function overwrite_config($data) {
    $filename = CONFIG_FILENAME;
    return file_put_contents($filename, $data, FILE_USE_INCLUDE_PATH | LOCK_EX);
}

define("CONFIG_DATA", get_config());
define("ANMELDUNG_URL", CONFIG_DATA["general"]["url"]);
?>