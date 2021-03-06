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
    $sql = "SELECT tage.tagDatum as `day`,
        teilnehmer.nachname AS `lastname`,
        teilnehmer.vorname AS `firstname`,
        teilnehmer.email,
        teilnehmer.anzahl AS `quantity`,
        teilnehmer.anmeldestation AS `station`,
        teilnehmer.eintrag AS `created`,
        teilnehmer.bearbeitet AS `edited`,
        teilnehmer.ID AS `id`,
        CASE
            WHEN zeitfenster.bis IS NULL THEN
                TIME_FORMAT(zeitfenster.von, '%H:%i')
            ELSE
                CONCAT(TIME_FORMAT(zeitfenster.von, '%H:%i'), ' - ', TIME_FORMAT(zeitfenster.bis, '%H:%i'))
        END AS `time`,
        veranstaltungen.titel AS `title`,
        veranstaltungen.emailVorlage AS `email_template`
        FROM tage, teilnehmer, zeitfenster, veranstaltungen
        WHERE teilnehmer.ID = ?
            AND teilnehmer.ZeitfensterID = zeitfenster.ZeitfensterID
            AND tage.tagID = zeitfenster.tagID
            AND veranstaltungen.id = tage.veranstaltungsId";

    $data = $db->query($sql, [$participant_id])->fetch();

    $token = ["delete_link" => "https://".$_SERVER['SERVER_NAME'].ANMELDUNG_URL."/delete_registration.php?id=".$participant_id."&token=".make_token_current_time($data)];

    return array_merge($data, $token);
}

function send_confirmation_mail(Database $db, int $participant_id){
    $email_template_data = get_data_for_template($db, $participant_id);
    $email_content = create_email_content($email_template_data);
    return send_mail($email_template_data["email"], $email_template_data["firstname"]." ".$email_template_data["lastname"], "Teilnahmenestätigung für ".$email_template_data["title"], $email_content);
}

?>