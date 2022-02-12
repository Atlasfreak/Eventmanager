<?php
basename($_SERVER['PHP_SELF']) == basename(__FILE__) && die();

include(__DIR__."/../inc/mail.php");

if (empty($_POST)) exit_with_code(400);

$in  = str_repeat('?,', count($_POST["email_addresses"]) - 1) . '?';
$email_sql = "SELECT email,
        id,
        vorname,
        nachname
    FROM teilnehmer
    WHERE id IN (".$in.")";

$email_query = $db->query($email_sql, $_POST["email_addresses"]);
$email_data = $email_query->fetchAll();

$subject = $_POST["email_subject"];
$content = $_POST["email"];

if (!empty($_POST["auto_email"])) {
    function email($email_address, $id, $name, $subject, $content, $db) {
        send_confirmation_mail($db, $id);
    }
} else {
    function email($email_address, $id, $name, $subject, $content, $db) {
        send_mail($email_address, $name, $subject, parse_delta($content));
    }
}

foreach ($email_data as $email_address) {
    email($email_address["email"], $email_address["id"], $email_address["nachname"]." ".$email_address["vorname"], $subject, $content, $db);
}
redirect($_SERVER['REQUEST_URI']);

?>