<?php

function isJson(string $string): bool {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function validate_event(array $data){
    $errors = array();

    $description = html_entity_decode($data["description"]);
    if (!(isJson($description))) {
        $errors["description"] = "invalid";
    }

    $email_template = html_entity_decode($data["email_template"]);
    if (!(isJson($email_template))) {
        $errors["email"] = "invalid";
    }

    $title = htmlspecialchars($data["title"]);
    if (strlen($title) > 512) {
        $errors["title"] = "too_long";
    }

    $reg_startdate = strtotime(htmlspecialchars($data["reg_startdate"]));
    $reg_enddate = strtotime(htmlspecialchars($data["reg_enddate"]));
    if ($reg_startdate >= $reg_enddate) {
        $errors["reg_date"] = "invalid";
    }

    $stations = htmlspecialchars($data["stations"]);
    if ($stations < 0) {
        $errors["stations"] = "invalid";
    } elseif (!is_numeric($stations) or $stations === 0) {
        $stations = null;
    }

    $reg_startdate = date("c", $reg_startdate);
    $reg_enddate = date("c", $reg_enddate);

    return array(
        "errors" => $errors,
        "title" => $title,
        "description" => $description,
        "email_template" => $email_template,
        "stations" => $stations,
        "reg_startdate" => $reg_startdate,
        "reg_enddate" => $reg_enddate
    );
}
?>