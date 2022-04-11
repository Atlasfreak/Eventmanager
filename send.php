<?php

if (isset($_GET["event"], $_POST)) {
    $template_data_events = array("errors" => []);

    $max_event_participants = $db->get_max_participants($_GET["event"]);
    $event_participants = $db->get_participants($_GET["event"]);

    $registered_participants = 1; // TODO Möglichkeit für mehrere Anmeldungen implementieren.

    if ($event_participants >= $max_event_participants) {
        array_push($template_data_events["errors"], array("msg" => "Es gibt für diese Veranstaltung keine freien Anmeldeplätze mehr."));
    }

    $db_data = array(
        "nachname" => htmlspecialchars($_POST["lastname"]),
        "vorname" => htmlspecialchars($_POST["firstname"]),
        "strasse" => htmlspecialchars($_POST["street"]." ".$_POST["house_nr"]),
        "ort" => htmlspecialchars($_POST["postal_code"]." ".$_POST["city"]),
        "email" => htmlspecialchars($_POST["email"]),
        "telefon" => htmlspecialchars($_POST["phone"]),
        "anzahl" => htmlspecialchars($registered_participants),
        "zeitfensterID" => htmlspecialchars($_POST["selected_timewindow"]),
        "anmeldestation" => null,
    );

    $query_event_timewindows = $db->get_timewindows($_GET["event"]);
    $ids_event_timewindows = $query_event_timewindows->fetchAll(PDO::FETCH_COLUMN, 0);

    $already_registered_data = array(
        "event_ids" => implode(",", $ids_event_timewindows),
        "nachname" => $db_data["nachname"],
        "vorname" => $db_data["vorname"],
        "ort" => $db_data["ort"],
        "strasse" => $db_data["strasse"],
    );

    $sql_already_registered = "SELECT id FROM teilnehmer WHERE vorname = :vorname AND nachname = :nachname AND strasse = :strasse AND ort = :ort AND FIND_IN_SET(zeitfensterID, :event_ids)";
    $query_already_registered = $db->query($sql_already_registered, $already_registered_data);

    if ($query_already_registered->rowCount() > 0) {
        array_push($template_data_events["errors"], array("msg" => "Sie haben sich bereits für diese Veranstaltung registriert."));
    }

    $data_event = get_event_data($_GET["event"], $db);
    if (isset($data_event["error"]) and $data_event["error"] === "closed"){
        $template_data_events["errors"] = [];
        array_push($template_data_events["errors"], array("msg" => "Das Anmeldefenster für diese Veranstaltung ist geschlossen."));
    }

    if (!($template_data_events["errors"] === [])) {
        $_SESSION["messages"] = add_type_to_msgs($template_data_events["errors"], "danger");
        redirect(".");
    }

    $captcha = htmlspecialchars($_POST["captcha"]);

    $template_data_event = array("errors" => []);

    if (!(isset($_POST["captcha"]) and $captcha == $_SESSION['digit'])) {
        $template_data_event["errors"]["captcha"] = "wrong";
    }

    $post_keys = ["selected_day", "selected_timewindow", "lastname", "firstname", "email", "street", "house_nr", "postal_code", "city", "phone"];

    $template_data_event["errors"] = array_merge($template_data_event["errors"], check_if_empty($_POST, $post_keys));

    if (isset($_POST["selected_day"], $_POST["selected_timewindow"])) {
        $sql_timewindow_count = "SELECT COUNT(zeitfensterID) FROM zeitfenster WHERE tagID = ? AND zeitfensterID = ?";
        $query_timewindow_count = $db->query($sql_timewindow_count, array($_POST["selected_day"], $_POST["selected_timewindow"]));
        if($query_timewindow_count->rowCount() === 0 or ((int) $query_timewindow_count->fetch()[0]) !== 1) {
            $template_data_event["errors"]["selected_day"] = "wrong_window";
            $template_data_event["errors"]["selected_timewindow"] = "wrong_window";
        } else {
            $timewindow_max_participants = $db->get_max_participants(null, array($_POST["selected_timewindow"]));
            $timewindow_participants = $db->get_participants(null, array($_POST["selected_timewindow"]));
            if ($timewindow_participants >= $timewindow_max_participants) {
                $template_data_event["errors"]["selected_timewindow"] = "already_full";
            } elseif ($timewindow_participants + $registered_participants > $timewindow_max_participants) {
                $template_data_event["errors"]["selected_timewindow"] = "too_many_registered";
            }
        }
    }
    if (isset($_POST["email"])) {
        if (!(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
            $template_data_event["errors"]["email"] = "invalid";
        }
    }

    if (!($template_data_event["errors"] === [])) {
        foreach ($post_keys as $key) {
            if (isset($_POST[$key])) {
                $template_data_event["values"][$key] = htmlspecialchars($_POST[$key]);
            }
        }
        $template_data_event = array_merge($template_data_event, $data_event);
        echo render_registration($templates, $template_data_event);
        exit;
    }

    // Code after validation

    if (!empty($data_event["stations"])) {
        try {
            $sql_stations = "SELECT teilnehmer.Anmeldestation
                FROM zeitfenster, teilnehmer, tage, veranstaltungen
                WHERE zeitfenster.ZeitfensterID = ?
                    AND teilnehmer.ZeitfensterID = zeitfenster.ZeitfensterID
                    AND tage.tagID = zeitfenster.tagID
                    AND veranstaltungen.id = tage.veranstaltungsId
                GROUP BY teilnehmer.Anmeldestation
                ORDER BY COUNT(*) ASC";
            $query_stations = $db->query($sql_stations, [$db_data["zeitfensterID"]]);
            $data_stations = $query_stations->fetchAll(PDO::FETCH_COLUMN, 0);
            $missing_stations = array_values(array_diff(range(1, (int) $data_event["stations"]), $data_stations));
            if (count($missing_stations) == 0) {
                $db_data["anmeldestation"] = $data_stations[0];
            } else {
                $db_data["anmeldestation"] = $missing_stations[0];
            }
        } catch(Exception $exception) {
            exit_with_code(500);
        }
    }

    // Insert Data into database
    try {
        $db->insert("teilnehmer", $db_data);
    } catch (Exception $exception) {
        $_SESSION["messages"] = add_type_to_msgs(["Es gab ein Problem mit ihren Angaben. Bitte melden sie sich bei team@whgonline.de falls dieses Problem weiterhin besteht."], "danger");
        redirect(".");
    }

    // Send confirmation E-Mail
    $participant_id = $db->mysql->lastInsertID();
    include("inc/mail.php");
    if (!send_confirmation_mail($db, $participant_id)) {
        $_SESSION["messages"] = add_type_to_msgs(["Es gab ein Problem beim versenden der Bestätigungs E-Mail, ihre Daten wurden bereits gespeichert! Bitte melden sie sich bei anmeldung@whgonline.de."], "danger");
        redirect(".");
    }

    $_SESSION["messages"] = add_type_to_msgs(["Die Anmeldung war erfolgreich. Sie sollten in kürze eine E-Mail erhalten. Schauen sie ggf. im Spamordner nach."], "success");
    redirect(".");
}

?>