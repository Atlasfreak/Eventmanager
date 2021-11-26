<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_GET["event"], $_POST)) {
    $sql_event = "SELECT id FROM veranstaltungen WHERE id = ? AND anmeldestart <= CURRENT_TIMESTAMP AND anmeldeende >= CURRENT_TIMESTAMP";
    $query_event = $db->query($sql_event, array($_GET["event"]));
    if ($query_event->rowCount() === 0){
        echo "Für diese Veranstaltung kann man sich nicht anmelden.";
        exit;
    }
    $captcha = htmlspecialchars($_POST["captcha"]);

    $data = array("errors" => []);

    session_start();

    if (!(isset($_POST["captcha"]) and $captcha==$_SESSION['digit'])) {
        $data["errors"]["captcha"] = "wrong";
    }

    $post_key = ["selected_day", "selected_timewindow", "lastname", "firstname", "email", "street", "house_nr", "postal_code", "city", "phone"];

    foreach ($post_key as $key) {
        if (!(isset($_POST[$key])) or $_POST[$key] === "") {
            $data["errors"][$key] = "empty";
        }
    }
    if (isset($_POST["selected_day"], $_POST["selected_timewindow"])) {
        $sql_selected_timewindow = "SELECT COUNT(zeitfensterID) FROM zeitfenster WHERE tagID = ? AND zeitfensterID = ?";
        $query_selected_timewindow = $db->query($sql_selected_timewindow, array($_POST["selected_day"], $_POST["selected_timewindow"]));
        if($query_selected_timewindow->rowCount() === 0 or ((int) $query_selected_timewindow->fetch()[0]) !== 1) {
            $data["errors"]["selected_day"] = "wrong_window";
            $data["errors"]["selected_timewindow"] = "wrong_window";
        }
    }

    if (!($data["errors"] === [])) {
        foreach ($post_key as $key) {
            if (isset($_POST[$key])) {
                $data["values"][$key] = htmlspecialchars($_POST[$key]);
            }
        }
        $data = array_merge($data, get_event_data($_GET["event"], $db));
        echo render_regsitration($templates, $data);
        exit;
    }
    // Code after validation
}

?>