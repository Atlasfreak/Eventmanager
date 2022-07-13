<?php
namespace Atlasfreak\Eventmanager;

require("classes/update.php");
require("inc/header.php");

if(!is_logged_in()) exit_with_code(403);

$update_api = new Update(CONFIG_DATA["updater"]["git_remote"]);

function check_update(Update $api) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($api->check_version());
    exit;
}

function update(Update $api) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($api->update());
    exit;
}

if (isset($_GET["func"]) && $_GET["func"] === "update") {
    update($update_api);
}

?>