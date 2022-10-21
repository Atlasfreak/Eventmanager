<?php
use Atlasfreak\Eventmanager\Database;

require_once(__DIR__."/classes/database.php");
include_once(__DIR__."/../config.php");

$user = CONFIG_DATA["database"]["user"] ?? null;
$password = CONFIG_DATA["database"]["password"] ?? null;
$name = CONFIG_DATA["database"]["name"] ?? null;

$db = new Database($user, $password, $name);

?>