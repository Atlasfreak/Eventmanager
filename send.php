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
    if (!(isset($_POST["selected_day"], $_POST["selected_timewindow"], $_POST["lastname"], $_POST["firstname"], $_POST["email"], $_POST["street"], $_POST["city"], $_POST["phone"]))) {
        $data = get_event_data($_GET["event"], $db);

        echo render_regsitration($templates, $data);
        exit;
    }
    $captcha = htmlspecialchars($_POST["captcha"]);
    if (isset($_POST["captcha"]) and $captcha==$_SESSION['digit']) {

    } else {
        header("Location:".ANMELDUNG_URL);
        exit;
    }
}

?>