<?php
include_once("inc/header.php");
include_once("inc/user_token.php");
include_once("inc/db.php");

if (empty($_GET["id"]) or empty($_GET["token"])) {
    exit_with_code(400);
}

$participant_id = $_GET["id"];
$participant_data = $db->get_participant($participant_id);

session_start();

if (!check_token($participant_data, $_GET["token"])) {
    $_SESSION["messages"] = add_type_to_msgs(["Der Token ist nicht gültig."], "danger");
    redirect(".");
}

$event_sql = "SELECT titel, anmeldeende
    FROM `veranstaltungen`
    WHERE `veranstaltungen`.id = (
        SELECT tage.veranstaltungsId
        FROM tage
        WHERE tage.tagID = (
            SELECT zeitfenster.tagID
            FROM zeitfenster
            WHERE zeitfenster.ZeitfensterID = (
                SELECT teilnehmer.ZeitfensterID
                FROM teilnehmer
                WHERE teilnehmer.ID = ?
            )
        )
    )";
$event_data = $db->query($event_sql, [$participant_id])->fetch();

if (!empty($_GET["confirm"])) {
    $db->delete("teilnehmer", "id = ?", $participant_id);
    $_SESSION["messages"] = add_type_to_msgs(["Sie haben sich erfolgreich von ".htmlspecialchars($event_data["titel"])." abgemeldet."], "success");
    redirect(".");
}

echo $templates->render("main::delete_confirm", ["title" => "Abmelden", "event" => $event_data["titel"]]);

?>