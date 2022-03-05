<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require __DIR__."/../PHPMailer/src/Exception.php";
require __DIR__."/../PHPMailer/src/PHPMailer.php";
require __DIR__."/../PHPMailer/src/SMTP.php";

include_once("user_token.php");

function send_mail(string $to, string $name, string $subject, string $content) {
    $mail = new PHPMailer();
    if (CONFIG_DATA["general"]["debug"]) $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = CONFIG_DATA["mail"]["host"];
    $mail->Port = CONFIG_DATA["mail"]["port"];
    if (!CONFIG_DATA["general"]["debug"]) $mail->SMTPAuth = true;
    $mail->Username = CONFIG_DATA["mail"]["username"];
    $mail->Password = CONFIG_DATA["mail"]["password"];
    $mail->setFrom(CONFIG_DATA["mail"]["email_adress"], CONFIG_DATA["mail"]["displayname"]);
    $mail->CharSet = "utf-8";
    $mail->addAddress($to, $name);
    $mail->Subject = $subject;
    $mail->msgHtml($content);
    return $mail->send();
}

/**
 * In der E-Mail Template werden folgende Platzhalter durch den jeweiligen Wert ersetzt:
 * ${Veranstaltung} => Titel der Veranstaltung                  $data["title"]
 * ${Tag} => Datum für das man sich registriert hat             $data["day"]
 * ${Zeitfenster} => Uhrzeit für die man sich registriert hat   $data["time"]
 * ${Nachname} => angemeldeter Nachname                         $data["lastname"]
 * ${Vorname} => angemeldeter Vorname                           $data["firstname"]
 * ${Station} => Station an der man sich angemeldet hat         $data["station"]
 * ${Anzahl} => Anzahl an angemeldeten Teilnehmern              $data["quantity"]
 * ${Abmelden} => Link zum selber abmelden                      $data["delete_link"]
 *
 * $data["email_template"] E-Mail Vorlages
 *
 * @param array $data Named array mit allen Daten für die E-Mail (alle Platzhalter)
 *
 */
function create_email_content(array $data) {
    $email_template = parse_delta($data["email_template"]);

    $placeholders = array(
        "\${Veranstaltung}" => $data["title"],
        "\${Tag}" => date("d.m.Y", strtotime($data["day"])),
        "\${Zeitfenster}" => $data["time"],
        "\${Nachname}" => $data["lastname"],
        "\${Vorname}" => $data["firstname"],
        "\${Station}" => $data["station"],
        "\${Anzahl}" => $data["quantity"],
        "\${Abmelden}" => $data["delete_link"],
    );
    $email_template = strtr($email_template, $placeholders);
    return $email_template;
}

function get_data_for_template(Database $db, int $participant_id) {
    $sql_timewindow = "SELECT
        tagID AS `day_id`,
        CASE
        WHEN bis IS NULL THEN
            TIME_FORMAT(von, '%H:%i')
        ELSE
            CONCAT(TIME_FORMAT(von, '%H:%i'), ' - ', TIME_FORMAT(bis, '%H:%i'))
        END AS `time`
        FROM zeitfenster
        WHERE zeitfensterID = ?";

    $sql_day = "SELECT
        tagDatum AS `day`,
        veranstaltungsId AS `event_id`
        FROM tage
        WHERE tagID = ?";

    $sql_event = "SELECT
        titel AS `title`,
        emailVorlage AS `email_template`
        FROM veranstaltungen
        WHERE id = ?";

    $participant_data = $db->get_participant($participant_id);

    $token = ["delete_link" => "https://".$_SERVER['SERVER_NAME'].ANMELDUNG_URL."/delete_registration.php?id=".$participant_data["id"]."&token=".make_token_current_time($participant_data)];

    $query_timewindow = $db->query($sql_timewindow, array($participant_data["timewindow_id"]));
    $timewindow_data = $query_timewindow->fetch();

    $query_day = $db->query($sql_day, array($timewindow_data["day_id"]));
    $day_data = $query_day->fetch();

    $query_event = $db->query($sql_event, array($day_data["event_id"]));
    $event_data = $query_event->fetch();
    return array_merge($participant_data, $timewindow_data, $day_data, $event_data, $token);
}

function send_confirmation_mail(Database $db, int $participant_id){
    $email_template_data = get_data_for_template($db, $participant_id);
    $email_content = create_email_content($email_template_data);
    return send_mail($email_template_data["email"], $email_template_data["firstname"]." ".$email_template_data["lastname"], "Teilnahmenestätigung für ".$email_template_data["title"], $email_content);
}

?>