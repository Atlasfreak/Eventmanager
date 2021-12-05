<?php

class Database {
    //
    // NIEMALS HTML DIREKT IN DER DATENBANK SPEICHERN!!
    //
    // Die Reihenfolge muss bewahrt werden,
    // da sonst bei erstellen nicht alle Tabellen erstellt werden,
    // da sie mit Fremdschlüsseln verknüpft sind.
    //
    private const TABLES = array(
        "admin" => array(
            "username" => "VARCHAR(256) NOT NULL UNIQUE",
            "pass" => "VARCHAR(256) NOT NULL",
            "id" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY"
        ),
        "veranstaltungen" => array(
            "id" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "beschreibung" => "JSON NOT NULL", // Hier wird der quill delta JSON string gespeichert. KEIN HTML!
            "titel" => "VARCHAR(512) NOT NULL",
            "anmeldestart" => "DATETIME NOT NULL",
            "anmeldeende" => "DATETIME NOT NULL, CHECK(anmeldeende > anmeldestart)",
            // "start" => "DATETIME NOT NULL, CHECK(start > anmeldeende)",
            // "ende" => "DATETIME NOT NULL, CHECK(ende > start)",
            "emailVorlage" => "JSON NOT NULL", // Hier wird der quill delta JSON string gespeichert. KEIN HTML!
        ),
        "tage" => array(
            "tagID" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "tagDatum" => "DATE NOT NULL",
            "veranstaltungsId" => "INT(11) NOT NULL, FOREIGN KEY (veranstaltungsId) REFERENCES veranstaltungen(id) ON DELETE CASCADE ON UPDATE CASCADE",
        ),
        "zeitfenster" => array(
            "zeitfensterID" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "maxTeilnehmer" => "INT(11) NOT NULL DEFAULT 1, CHECK(maxTeilnehmer > 0)",
            "tagID" => "INT(11) NOT NULL, FOREIGN KEY (tagID) REFERENCES tage(tagID) ON DELETE CASCADE ON UPDATE CASCADE",
            "reihenfolge" => "INT(11) NOT NULL",
            "von" => "TIME NOT NULL",
            "bis" => "TIME, CHECK (von < bis)",
        ),
        "teilnehmer" => array(
            "id" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "nachname" => "VARCHAR(512) NOT NULL",
            "vorname" => "VARCHAR(512) NOT NULL",
            "strasse" => "VARCHAR(512) NOT NULL",
            "ort" => "VARCHAR(512) NOT NULL",
            "email" => "VARCHAR(512) NOT NULL",
            "telefon" => "VARCHAR(512) NOT NULL",
            "anzahl" => "INT(11) NOT NULL",
            "zeitfensterID" => "INT(11) NOT NULL, FOREIGN KEY (zeitfensterID) REFERENCES zeitfenster(zeitfensterID) ON DELETE CASCADE ON UPDATE CASCADE",
            "eintrag" => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
            "anmeldestation" => "VARCHAR(512) NOT NULL"
        ),
    );

    public $mysql, $db_name;

    public function __construct($db_username = "root", $db_password = "", $db_name = "anmeldung_test"){
        $this->mysql = new PDO("mysql:host=localhost;dbname=".$db_name.";charset=utf8",$db_username,$db_password);
        $this->db_name = $db_name;
    }

    public function count_tables() {
        return count($this::TABLES);
    }

    public function init_db() {
        foreach ($this::TABLES as $table => $fields) {
            $statement = "CREATE TABLE IF NOT EXISTS `".$table."` (";
            foreach ($fields as $field => $type) {
                $statement = $statement."`".$field."` ".$type;
                if (array_key_last($fields) !== $field) {
                    $statement = $statement.",";
                }
            }
            $statement = $statement.")";
            $query = $this->query($statement);
        }
    }

    public function query($statement, $values = null) {
        $query = $this->mysql->prepare($statement);
        $query->execute($values);
        if($query->errorInfo()[0] == 0) {
            return $query;
        } else {
            throw new Exception($query->errorInfo()[2]);
            return null;
        }
    }

    public function insert($table, $values) {
        $statement = "INSERT INTO `".$table."` (";

        $statement .= "`".implode("`, `", array_keys($values))."`)";
        $statement .= " VALUES (:".implode(", :", array_keys($values)).")";

        return $this->query($statement, $values);
    }

    public function get_days($event_id) {
        $sql_days = "SELECT tagID, tagDatum FROM tage WHERE veranstaltungsId = ?";
        $query_days = $this->query($sql_days, array($event_id));
        return $query_days;
    }

    public function get_timewindows($event_id = null, $day_ids = null) {
        if ($day_ids === null) {
            if ($event_id === null) {
                throw new ArgumentCountError("At least one argument must be given and not be null.");
            }
            $query_days = $this->get_days($event_id);
            $day_ids = $query_days->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        $sql_timewindows = "SELECT zeitfensterID, Beschreibung, von, bis, maxTeilnehmer FROM zeitfenster WHERE FIND_IN_SET(tagID, ?)";
        $query_timewindows = $this->query($sql_timewindows, array(implode(",", $day_ids)));
        return $query_timewindows;
    }

    public function get_max_participants($event_id = null, $ids_timewindows = null) {
        if ($ids_timewindows === null) {
            if ($event_id === null) {
                throw new ArgumentCountError("At least one argument must be given and not be null.");
            }
            $query_timewindows = $this->get_timewindows($event_id);
            $ids_timewindows = $query_timewindows->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        $sql_max_participants = "SELECT CASE WHEN SUM(maxTeilnehmer) IS NULL THEN 0 ELSE SUM(maxTeilnehmer) END as maxTeilnehmer FROM zeitfenster WHERE FIND_IN_SET(zeitfensterID, ?)";
        $query_max_participants = $this->query($sql_max_participants, array(implode(",", $ids_timewindows)));
        return (int) $query_max_participants->fetch()["maxTeilnehmer"];
    }

    public function get_participants($event_id = null, $ids_timewindows = null) {
        if ($ids_timewindows === null) {
            if ($event_id === null) {
                throw new ArgumentCountError("At least one argument must be given and not be null.");
            }
            $query_timewindows = $this->get_timewindows($event_id);
            $ids_timewindows = $query_timewindows->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        $sql_participants = "SELECT CASE WHEN SUM(anzahl) IS NULL THEN 0 ELSE SUM(anzahl) END as anzahlTeilnehmer FROM teilnehmer WHERE FIND_IN_SET(zeitfensterID, ?)";
        $query_participants = $this->query($sql_participants, array(implode(",", $ids_timewindows)));
        return (int) $query_participants->fetch()["anzahlTeilnehmer"];
    }
}
$db = new Database();
?>