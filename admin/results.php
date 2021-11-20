<?php
include("inc/header.php");;

if(!isset($_SESSION["registration_password"],$_SESSION["registration_username"])) {
    header("Location:../admin");
}
else {

    include('../inc/db.php');

    function timewindow_string($from, $until) {
        $string = strftime("%H:%M", strtotime($from));
        if ($until !== null) {
            $string .= " - ".strftime("%H:%M", strtotime($until));
        }
        return $string;
    }

    if(!isset($_GET["event"])) {
        echo "<meta http-equiv='refresh' content='3; url=".ANMELDUNG_URL."/admin'>";
        echo "Keine Veranstaltung gewählt.";
        exit;
    }
    $sql_event = "SELECT id, titel FROM veranstaltungen WHERE id = ?";
    $query_event = $db->query($sql_event, array($_GET["event"]));
    if ($query_event->rowCount() === 0){
        echo "<meta http-equiv='refresh' content='3; url=".ANMELDUNG_URL."/admin'>";
        echo "Diese Veranstaltung existiert nicht.";
        exit;
    }
    $data_event = $query_event->fetch();

    $sql_days = "SELECT tagID, tagDatum FROM tage WHERE veranstaltungsId = ?";
    $query_days = $db->query($sql_days, array($data_event["id"]));
    $data_days = $query_days->fetchAll();
    // print_r($data_days);
    // exit;

    // $days_sql="SELECT tagDatum, tagID FROM tage ORDER BY tagID ASC";
    // $days_qry = $db->mysql->prepare($days_sql);
    // $days_qry->execute();
    // $data_days = $days_qry->fetchAll();
    $data_time_windows = array();
    $data_participants = array();
    $results = array();

    foreach($data_days as $row_day) {
        $tagID=$row_day["tagID"];

        $time_windows_sql="SELECT von, bis, maxTeilnehmer, zeitfensterID FROM zeitfenster WHERE tagID=? ORDER BY von, bis";
        $time_windows_qry = $db->mysql->prepare($time_windows_sql);
        $time_windows_qry->execute(array($tagID));
        $data_time_windows[$tagID] = $time_windows_qry->fetchAll();
        $results[$tagID] = 0;

        foreach($data_time_windows[$tagID] as $row_time_window) {

            $MaxTeilnehmer=$row_time_window["maxTeilnehmer"];
            $ZeitfensterID=$row_time_window["zeitfensterID"];

            $participants_sql = "
            SELECT nachname, vorname, strasse, ort, email, telefon, id
            FROM teilnehmer
            WHERE ZeitfensterID=?
            ORDER BY nachname";
            $participants_qry = $db->mysql->prepare($participants_sql);
            $participants_qry->execute(array($ZeitfensterID));
            $data_participants[$ZeitfensterID] = $participants_qry->fetchAll();
            $results[$tagID] += $participants_qry->rowCount();
            $n = $participants_qry->rowCount();
            while ($n < $MaxTeilnehmer) {
                $n++;
                array_push($data_participants[$ZeitfensterID], array(
                    "nachname" => "",
                    "vorname" => "",
                    "strasse" => "",
                    "ort" => "",
                    "email" => "",
                    "telefon" => "",
                    "id" => "",
                    ));
            }
        }
    }
    echo $templates->render("admin::results", [
        "prefix" => "",
        "data_participants" => $data_participants,
        "data_time_windows" => $data_time_windows,
        "data_days" => $data_days,
        "results" => $results,
        // ueberschriften ist ein Array aus Strings mit den Spalten Überschriften, falls ein Element ein Array ist dann mit der Form ["Überschrift", "HTML Klasse(n)"]
        "ueberschriften" => ["Nachname","Vorname","Strasse","Ort","E-Mail","Telefon","Zeitfenster", ["Editieren", "d-print-none"]],
    ]);
}
?>