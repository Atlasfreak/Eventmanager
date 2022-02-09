<?php
basename($_SERVER['PHP_SELF']) == basename(__FILE__) && die();

include("inc/event_form.php");

if (empty($_POST)) exit_with_code(400);

$data = validate_event($_POST);

if (empty($data["errors"])) {
    $db_data = [
        "titel" => $data["title"],
        "beschreibung" => $data["description"],
        "emailVorlage" => $data["email_template"],
        "anmeldestart" => $data["reg_startdate"],
        "anmeldeende" => $data["reg_enddate"],
        "stationen" => $data["stations"] ?? null,
    ];
    $db->update("veranstaltungen", ["id" => $_GET["event_id"]], $db_data);
    redirect($_SERVER['REQUEST_URI']);
}

?>