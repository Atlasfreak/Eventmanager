<?php
basename($_SERVER['PHP_SELF']) == basename(__FILE__) && die();

include(__DIR__."/../inc/mail.php");

if (empty($_POST)) exit_with_code(400);

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

if (empty($data["errors"])) {
    $in  = str_repeat('?,', count($email_addresses) - 1) . '?';
    $email_sql = "SELECT email,
            id,
            vorname,
            nachname
        FROM teilnehmer
        WHERE id IN (".$in.")";

    $email_query = $db->query($email_sql, $email_addresses);
    $email_data = $email_query->fetchAll();

    if (!empty($auto_email)) {
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
}

?>