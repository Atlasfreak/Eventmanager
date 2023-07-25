<?php
namespace Atlasfreak\Eventmanager;

use Atlasfreak\Eventmanager\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;



require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../user_token.php";

class Mailer {
    private Database $db;
    private PHPMailer $mailer;

    public function __construct($db) {
        $this->mailer = $this->initialize_mailer();
        $this->db = $db;
    }

    /**
     * Initializes PHPMailer with the mail account from the config file
     *
     * @param string $subject the email subject
     * @return PHPMailer the PHPMailer instance
     */
    private function initialize_mailer() {
        $mail = new PHPMailer();
        if (CONFIG_DATA["general"]["debug"])
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        if (!CONFIG_DATA["general"]["debug"])
            $mail->SMTPAuth = true;
        $mail->isSMTP();
        $mail->Host = CONFIG_DATA["mail"]["host"];
        $mail->Port = CONFIG_DATA["mail"]["port"];
        $mail->Username = CONFIG_DATA["mail"]["username"];
        $mail->Password = CONFIG_DATA["mail"]["password"];
        $mail->setFrom(CONFIG_DATA["mail"]["email_adress"], CONFIG_DATA["mail"]["displayname"]);
        $mail->CharSet = "utf-8";
        return $mail;
    }

    /**
     * shortcut to send emails
     *
     * Send mail with specified $mailer, or creates one with the initialize_mailer function
     *
     * @param string $to the address to send the mail to
     * @param string $name the name of the recipient
     * @param string $subject the subject
     * @param string $content the content of the email
     * @param PHPMailer|null $mailer the PHPMailer instance to use if null use initialize_mailer
     *
     * @return void
     **/
    public function send_mail(string $to, string $name, string $subject, string $content, array $errors = []) {
        $this->mailer->Subject = $subject;
        $this->mailer->addAddress($to, $name);
        $this->mailer->msgHtml($content);
        if (!$this->mailer->send()) {
            $this->mailer->getSMTPInstance()->reset();
            $errors[] = $to;
        }
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
    }

    /**
     * Sends email, with the same content, to multiple participants
     *
     * @param array<array{email: string, name: string}> $email_address_data
     * @return array<string> all failed emails adresses
     */
    public function send_bulk_mail(array $email_address_data, string $subject, string $content) {
        $this->mailer->SMTPKeepAlive = true;
        $errors = [];
        foreach ($email_address_data as $email) {
            $this->send_mail($email['email'], $email['name'], $subject, $content, $errors);
        }
        return $errors;
    }

    /**
     * Sends confirmation email to multiple participants
     *
     * @param array<int> $participants_ids
     * @return array<string> all failed emails adresses
     */
    public function send_bulk_confirmation_mail(array $participants_ids, $event_id) {
        $this->mailer->SMTPKeepAlive = true;
        $participants_query = $this->get_participants_data($participants_ids, $event_id);
        $event_data = $this->get_event_data($event_id);
        $parsed_template = parse_delta($event_data["email_template"]);
        $subject = "Teilnahme Bestätigung für: " . $event_data["title"];
        $errors = [];
        while ($particpant_data = $participants_query->fetch()) {
            $particpant_data["delete_link"] = $this->get_delete_link($particpant_data);
            $particpant_data["title"] = $event_data["title"];
            $content = $this->create_email_content($particpant_data, $parsed_template);

            $this->send_mail($particpant_data["email"], $particpant_data["firstName"] . " " . $particpant_data["lastName"], $subject, $content, $errors);
        }
        return $errors;
    }

    /**
     * In der E-Mail Template werden folgende Platzhalter durch den jeweiligen Wert ersetzt:
     * ${Veranstaltung} => Titel der Veranstaltung                  $data["title"]
     * ${Tag} => Datum für das man sich registriert hat             $data["day"]
     * ${Zeitfenster} => Uhrzeit für die man sich registriert hat   $data["time"]
     * ${Nachname} => angemeldeter Nachname                         $data["lastName"]
     * ${Vorname} => angemeldeter Vorname                           $data["firstName"]
     * ${Station} => Station an der man sich angemeldet hat         $data["station"]
     * ${Anzahl} => Anzahl an angemeldeten Teilnehmern              $data["quantity"]
     * ${Abmelden} => Link zum selber abmelden                      $data["delete_link"]
     *
     * $data["email_template"] E-Mail Vorlage
     *
     * @param array{
     *  title: string,
     *  day: string,
     *  time: string,
     *  lastName: string,
     *  firstName: string,
     *  station: string,
     *  quantity:string,
     *  delete_link: string,
     *  email_template: string
     * } $data Named array mit allen Daten für die E-Mail (alle Platzhalter)
     *
     * @return string
     */
    private function create_email_content(array $data, string $parsed_template = null) {
        $email_template = $parsed_template == null ? parse_delta($data["email_template"]) : $parsed_template;

        $placeholders = array(
            "\${Veranstaltung}" => $data["title"],
            "\${Tag}" => date("d.m.Y", strtotime($data["day"])),
            "\${Zeitfenster}" => $data["time"],
            "\${Nachname}" => $data["lastName"],
            "\${Vorname}" => $data["firstName"],
            "\${Station}" => $data["station"],
            "\${Anzahl}" => $data["quantity"],
            "\${Abmelden}" => $data["delete_link"],
        );
        $email_template = strtr($email_template, $placeholders);
        return $email_template;
    }

    /**
     * Retrieves the participant data for all specified ids
     *
     * @param array<int> $participants_ids the ids
     * @param int $event_id
     *
     * @return \PDOStatement the database query with all participant data
     */
    private function get_participants_data(array $participants_ids, int $event_id): \PDOStatement {
        $in = str_repeat('?,', count($participants_ids) - 1) . '?';

        $sql = "SELECT tage.tagDatum AS `day`,
            teilnehmer.nachname AS `lastName`,
            teilnehmer.vorname AS `firstName`,
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
            END AS `time`
            FROM tage, teilnehmer, zeitfenster, veranstaltungen
            WHERE teilnehmer.ID IN ($in)
                AND teilnehmer.ZeitfensterID = zeitfenster.ZeitfensterID
                AND tage.tagID = zeitfenster.tagID
                AND veranstaltungen.id = ?";
        $data = $participants_ids;
        $data[] = $event_id;

        return $this->db->query($sql, $data);
    }

    /**
     * @param int $event_id
     * @return array{title: string, email_template: string} the relevant event data
     */
    private function get_event_data(int $event_id): array {
        $sql = "SELECT veranstaltungen.titel AS `title`,
            veranstaltungen.emailVorlage AS `email_template`
            FROM veranstaltungen
            WHERE id = ?";

        return $this->db->query($sql, [$event_id])->fetch();
    }

    /**
     * Returns a link to delete the registration
     *
     * @param array{
     *  id: int,
     *  lastName: string,
     *  firstName: string,
     *  created: string,
     *  edited: string
     * } $data
     * @return string a link to delete the registration
     */
    private function get_delete_link(array $data) {
        return "https://" . $_SERVER['SERVER_NAME'] . ANMELDUNG_URL . "/delete_registration.php?id=" . $data['id'] . "&token=" . make_token_current_time($data);
    }

    /**
     * Retrieves the template and participant data from the database and fills out the placeholders then sends mail to participant.
     *
     * @param int $participant_id the id of the participant
     *
     * @return bool false when an error occurs
     */
    public function send_confirmation_mail(int $participant_id, $event_id) {
        $errors = $this->send_bulk_confirmation_mail([$participant_id], $event_id);
        return empty($errors);
    }
}

?>