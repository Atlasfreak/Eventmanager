<?php
basename($_SERVER['PHP_SELF']) == basename(__FILE__) && die();

use Atlasfreak\Eventmanager\Mailer;

require(__DIR__ . "/../inc/classes/Mailer.php");

if (empty($_POST))
    exit_with_code(400);

$email_addresses = $_POST["email_addresses"] ?? null;
$subject = $_POST["email_subject"] ?? null;
$content = $_POST["email"] ?? null;
$auto_email = $_POST["auto_email"] ?? null;

$data = array();

if (empty($email_addresses)) {
    $data["errors"]["email_addresses"] = "empty";
}
if (empty($auto_email)) {
    if (empty($subject)) {
        $data["errors"]["email_subject"] = "empty";
    }
    if (empty($content)) {
        $data["errors"]["email"] = "empty";
    }
}

if (!empty($data["errors"])) {
    redirect($_SERVER['REQUEST_URI']);
}

$in = str_repeat('?,', count($email_addresses) - 1) . '?';
$email_sql = "SELECT email AS `email`,
        id,
        CONCAT(nachname, \" \", vorname) AS `name`
    FROM teilnehmer
    WHERE id IN (" . $in . ")";

$email_query = $db->query($email_sql, $email_addresses);

$mailer = new Mailer($db);

if (!empty($auto_email)) {
    $mailer->send_bulk_confirmation_mail($email_query->fetchAll(\PDO::FETCH_COLUMN, 1), $_GET["event_id"]);
} else {
    $mailer->send_bulk_mail($email_query->fetchAll(), $subject, parse_delta($content));
}

redirect($_SERVER['REQUEST_URI']);

?>