<?php

include("inc/header.php");
include("../inc/db.php");
require "../quill_delta_parser/Lexer.php";

if(!isset($_SESSION["registration_username"],$_SESSION["registration_password"])) {
    header("Location:../admin");
}

function isJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function render_page($templates, $title_err = false, $description_err = false, $email_template_err = false, $reg_date_err = false, $event_date_err = false, $event_reg_date_err = false, $data = array()) {
    return $templates->render("admin::create_event", array(
        "title_err" => $title_err,
        "description_err" => $description_err,
        "email_template_err" => $email_template_err,
        "reg_date_err" => $reg_date_err,
        "event_date_err" => $event_date_err,
        "event_reg_date_err" => $event_reg_date_err,
        "title_value" => isset($data["title_value"]) ? $data["title_value"]:"",
        "description" => isset($data["description"]) ? $data["description"]:"",
        "email_template" => isset($data["email_template"]) ? $data["email_template"]:"",
        "reg_startdate_val" => isset($data["reg_startdate_val"]) ? $data["reg_startdate_val"]:"",
        "reg_enddate_val" => isset($data["reg_enddate_val"]) ? $data["reg_enddate_val"]:"",
        "event_startdate_val" => isset($data["event_startdate_val"]) ? $data["event_startdate_val"]:"",
        "event_enddate_val" => isset($data["event_enddate_val"]) ? $data["event_enddate_val"]:"",
    ));
}

if (isset($_POST["description"], $_POST["title"], $_POST["email_template"], $_POST["reg_startdate"], $_POST["reg_enddate"], $_POST["event_startdate"], $_POST["event_enddate"])) {

    $title_err = false;
    $description_err = false;
    $email_template_err = false;
    $reg_date_err = false;
    $event_date_err = false;
    $event_reg_date_err = false;

    $description = html_entity_decode($_POST["description"]);
    if (!(isJson($description))) {
        $description_err = true;
        $description = "";
    }

    $email_template = html_entity_decode($_POST["email_template"]);
    if (!(isJson($email_template))) {
        $email_template_err = true;
        $email_template = "";
    }

    $title = htmlspecialchars($_POST["title"]);
    if (strlen($title) > 512) {
        $title_err = true;
    }

    $reg_startdate = strtotime(htmlspecialchars($_POST["reg_startdate"]));
    $reg_enddate = strtotime(htmlspecialchars($_POST["reg_enddate"]));
    if ($reg_startdate >= $reg_enddate) {
        $reg_date_err = true;
    }

    $event_startdate = strtotime(htmlspecialchars($_POST["event_startdate"]));
    $event_enddate = strtotime(htmlspecialchars($_POST["event_enddate"]));
    if ($event_startdate >= $event_enddate) {
        $event_date_err = true;
    }
    if ($event_startdate <= $reg_enddate) {
        $event_reg_date_err = true;
    }

    $reg_startdate = date("Y-m-d H:i", $reg_startdate);
    $reg_enddate = date("Y-m-d H:i", $reg_enddate);

    $event_startdate = date("Y-m-d H:i", $event_startdate);
    $event_enddate = date("Y-m-d H:i", $event_enddate);


    if ($title_err or $description_err or $email_template_err or $reg_date_err or $event_date_err or $event_reg_date_err) {
        $data = array(
            "title_value" => $title,
            "description" => $description,
            "email_template" => $email_template,
            "reg_startdate_val" => $reg_startdate,
            "reg_enddate_val" => $reg_enddate,
            "event_startdate_val" => $event_startdate,
            "event_enddate_val" => $event_enddate,
        );
        echo render_page($templates, $title_err, $description_err, $email_template_err, $reg_date_err, $event_date_err, $event_reg_date_err, $data);
        exit;
    }

    $query = $db->insert("veranstaltungen", array(
        "beschreibung" => $description,
        "emailVorlage" => $email_template,
        "titel" => $title,
        "anmeldestart" => $reg_startdate,
        "anmeldeende" => $reg_enddate,
        "start" => $event_startdate,
        "ende" => $event_enddate,
    ));
    exit(header("Location:../admin/"));
}

echo render_page($templates);
?>