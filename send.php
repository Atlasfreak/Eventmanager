<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_GET["event"], $_POST)) {
    $data_events = array("errors" => []);

    $max_participants = $db->get_max_participants($_GET["event"]);
    $current_participants = $db->get_participants($_GET["event"]);

    $registered_participants = 1; // TODO Möglichkeit für mehrere Anmeldungen implementieren.

    if ($current_participants >= $max_participants) {
        array_push($data_events["errors"], array("msg" => "Es gibt für diese Veranstaltung keine freien Anmeldeplätze mehr."));
    }

    $sql_event = "SELECT id FROM veranstaltungen WHERE id = ? AND anmeldestart <= CURRENT_TIMESTAMP AND anmeldeende >= CURRENT_TIMESTAMP";
    $query_event = $db->query($sql_event, array($_GET["event"]));
    if ($query_event->rowCount() === 0){
        $data_events["errors"] = [];
        array_push($data_events["errors"], array("msg" => "Das Anmeldefenster für diese Veranstaltung ist geschlossen."));
    }

    if (!($data_events["errors"] === [])) {
        $data_events = array_merge($data_events, get_events_data($db));
        echo render_overview($templates, $data_events);
        exit;
    }

    $captcha = htmlspecialchars($_POST["captcha"]);

    $data_event = array("errors" => []);

    session_start();

    if (!(isset($_POST["captcha"]) and $captcha==$_SESSION['digit'])) {
        $data_event["errors"]["captcha"] = "wrong";
    }

    $post_key = ["selected_day", "selected_timewindow", "lastname", "firstname", "email", "street", "house_nr", "postal_code", "city", "phone"];

    foreach ($post_key as $key) {
        if (!(isset($_POST[$key])) or $_POST[$key] === "") {
            $data_event["errors"][$key] = "empty";
        }
    }

    if (isset($_POST["selected_day"], $_POST["selected_timewindow"])) {
        $sql_timewindow_count = "SELECT COUNT(zeitfensterID) FROM zeitfenster WHERE tagID = ? AND zeitfensterID = ?";
        $query_timewindow_count = $db->query($sql_timewindow_count, array($_POST["selected_day"], $_POST["selected_timewindow"]));
        if($query_timewindow_count->rowCount() === 0 or ((int) $query_timewindow_count->fetch()[0]) !== 1) {
            $data_event["errors"]["selected_day"] = "wrong_window";
            $data_event["errors"]["selected_timewindow"] = "wrong_window";
        } else {
            $timewindow_max_participants = $db->get_max_participants(null, array($_POST["selected_timewindow"]));
            $timewindow_participants = $db->get_participants(null, array($_POST["selected_timewindow"]));
            if ($timewindow_participants >= $timewindow_max_participants) {
                $data_event["errors"]["selected_timewindow"] = "already_full";
            } elseif ($timewindow_participants + $registered_participants > $max_participants) {
                $data_event["errors"]["selected_timewindow"] = "too_many_registered";
            }
        }
    }
    if (isset($_POST["email"])) {
        if (!(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
            $data_event["errors"]["email"] = "invalid";
        }
    }

    if (!($data_event["errors"] === [])) {
        foreach ($post_key as $key) {
            if (isset($_POST[$key])) {
                $data_event["values"][$key] = htmlspecialchars($_POST[$key]);
            }
        }
        $data_event = array_merge($data_event, get_event_data($_GET["event"], $db));
        echo render_regsitration($templates, $data_event);
        exit;
    }

    // Code after validation
}

?>