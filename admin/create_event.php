<?php

include("inc/header.php");
include("../inc/db.php");
include("inc/event_form.php");

if (!is_logged_in()) redirect("../admin");

function render_page(\League\Plates\Engine $templates, array $data = array()): string {
    return $templates->render("admin::create_event", array(
        "title_err" => $data["errors"]["title"] ?? false,
        "description_err" => $data["errors"]["description"] ?? false,
        "email_template_err" => $data["errors"]["email_template"] ?? false,
        "reg_date_err" => $data["errors"]["reg_date"] ?? false,
        "title_value" => $data["title"] ?? "",
        "description" => $data["description"] ?? "",
        "email_template" => $data["email_template"] ?? "",
        "stations_val" => $data["stations"] ?? null,
        "reg_startdate_val" => $data["reg_startdate"] ?? date("Y-m-d H:i"),
        "reg_enddate_val" => $data["reg_enddate"] ?? date("Y-m-d H:i"),
    ));
}

if (isset($_POST["description"], $_POST["title"], $_POST["email_template"], $_POST["reg_startdate"], $_POST["reg_enddate"])) {

    $data = validate_event($_POST);

    if (!empty($data["errors"])) {
        echo render_page($templates, $data);
        exit;
    }

    $query = $db->insert("veranstaltungen", array(
        "beschreibung" => $data["description"],
        "emailVorlage" => $data["email_template"],
        "titel" => $data["title"],
        "anmeldestart" => $data["reg_startdate"],
        "anmeldeende" => $data["reg_enddate"],
        "stationen" => $data["stations"],
    ));
    exit(header("Location:../admin/"));
}

echo render_page($templates);
?>