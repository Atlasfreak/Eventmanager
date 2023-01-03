<?php

namespace Atlasfreak\Eventmanager;

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
            "emailVorlage" => "JSON NOT NULL", // Hier wird der quill delta JSON string gespeichert. KEIN HTML!
            "stationen" => "INT(11)",
        ),
        "tage" => array(
            "tagID" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "tagDatum" => "DATE NOT NULL",
            "veranstaltungsId" => "INT(11) NOT NULL, FOREIGN KEY (veranstaltungsId) REFERENCES veranstaltungen(id) ON DELETE CASCADE",
        ),
        "zeitfenster" => array(
            "zeitfensterID" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "maxTeilnehmer" => "INT(11) NOT NULL DEFAULT 1, CHECK(maxTeilnehmer > 0)",
            "tagID" => "INT(11) NOT NULL, FOREIGN KEY (tagID) REFERENCES tage(tagID) ON DELETE CASCADE",
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
            "zeitfensterID" => "INT(11) NOT NULL, FOREIGN KEY (zeitfensterID) REFERENCES zeitfenster(zeitfensterID) ON DELETE CASCADE",
            "eintrag" => "DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
            "anmeldestation" => "INT(11)",
            "bearbeitet" => "DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
        ),
    );


    /**
     * ACHTUNG anfällig für SQL Injections darauf achten, dass keine Nutzereingabe verarbeitet werden!
     */
    protected function create_table(string $table_name) {
        $fields = $this::TABLES[$table_name];
        $statement = "CREATE TABLE IF NOT EXISTS `".$table_name."` (";

        foreach ($fields as $field => $type) {
            $statement = $statement."`".$field."` ".$type;
            if (array_key_last($fields) !== $field) {
                $statement = $statement.",";
            }
        }
        $statement = $statement.")";
        return $this->query($statement);
    }

    protected function add_column(string $table_name, array $missing_fields) {
        $statement = "ALTER TABLE `$table_name` ";

        foreach ($missing_fields as $field) {
            $type = $this::TABLES[$table_name][$field];
            $statement = $statement."ADD `$field` $type";
            if (end($missing_fields) !== $field) {
                $statement = $statement.",";
            }
        }

        return $this->query($statement);
    }

    public $mysql, $db_name;

    public function __construct(?string $db_username = null, ?string $db_password = null, ?string $db_name = null) {
        $db_username = !is_null($db_username) ? $db_username:"root";
        $db_password = !is_null($db_password) ? $db_password:"";
        $db_name = !is_null($db_name) ? $db_name:"anmeldung";
        $this->mysql = new \PDO("mysql:host=localhost;dbname=".$db_name.";charset=utf8",$db_username,$db_password);
        $this->db_name = $db_name;
    }

    public function count_tables(): int {
        return count($this::TABLES);
    }

    public function init_db(): void {
        foreach (array_keys($this::TABLES) as $table) {
            $this->create_table($table);
        }
    }

    public function update_db() {
        $query_tables = $this->query("SHOW TABLES;");
        $current_tables = $query_tables->fetchAll(\PDO::FETCH_COLUMN);
        $missing_tables = array_diff(array_keys($this::TABLES), $current_tables);

        $changed = ["new_tables" => $missing_tables, "new_fields" => []];

        if ($missing_tables) {
            foreach ($missing_tables as $missing_table) {
                $this->create_table($missing_table);
            }
        }

        foreach ($this::TABLES as $table => $fields) {
            $query_fields = $this->query("SHOW COLUMNS FROM $table");
            $current_fields = $query_fields->fetchAll(\PDO::FETCH_COLUMN);
            $missing_fields = array_diff(array_keys($fields), $current_fields);

            if ($missing_fields) {
                $this->add_column($table, $missing_fields);
                array_push($changed["new_fields"], [$table => $missing_fields]);
            }
        }

        return $changed;
    }

    public function query(string $statement, ?array $values = null): \PDOStatement {
        $query = $this->mysql->prepare($statement);
        $query->execute($values);
        if ($query->errorInfo()[0] == 0) {
            return $query;
        } else {
            throw new DatabaseException($query->errorInfo()[2]);
        }
    }

    public function update(string $table, array $condition, array $values) {
        $statement = "UPDATE ".$table." SET ";
        foreach ($values as $column => $value) {
            $statement .= $column." = :".$column;
            if ($column !== array_key_last($values)) {
                $statement .= " , ";
            }
        }
        $statement .= " WHERE ";
        foreach ($condition as $column => $value) {
            $value_name = $column."_val";
            $values = array_merge($values, [$value_name => $value]);

            $statement .= $column." = :".$value_name;
            if ($column !== array_key_last($condition)) {
                $statement .= " AND ";
            }
        }
        return $this->query($statement, $values);
    }

    public function insert(string $table, array $values) {
        $statement = "INSERT INTO `".$table."` (";

        $statement .= "`".implode("`, `", array_keys($values))."`)";
        $statement .= " VALUES (:".implode(", :", array_keys($values)).")";

        return $this->query($statement, $values);
    }

    public function delete(string $table, string $condition, $value) {
        $statement = "DELETE FROM `".$table."` WHERE ".$condition;
        return $this->query($statement, array($value));
    }

    public function get_days(int $event_id) {
        $sql_days = "SELECT tagID, tagDatum FROM tage WHERE veranstaltungsId = ?";
        $query_days = $this->query($sql_days, array($event_id));
        return $query_days;
    }

    public function get_timewindows(?int $event_id = null, ?array $day_ids = null) {
        if ($day_ids === null) {
            if ($event_id === null) {
                throw new \ArgumentCountError("At least one argument must be given and not be null.");
            }
            $query_days = $this->get_days($event_id);
            $day_ids = $query_days->fetchAll(\PDO::FETCH_COLUMN, 0);
        }

        $sql_timewindows = "SELECT zeitfensterID, von, bis, maxTeilnehmer FROM zeitfenster WHERE FIND_IN_SET(tagID, ?)";
        $query_timewindows = $this->query($sql_timewindows, array(implode(",", $day_ids)));
        return $query_timewindows;
    }

    public function get_max_participants(?int $event_id = null, ?array $ids_timewindows = null): int {
        if ($ids_timewindows === null) {
            if ($event_id === null) {
                throw new \ArgumentCountError("At least one argument must be given and not be null.");
            }
            $query_timewindows = $this->get_timewindows($event_id);
            $ids_timewindows = $query_timewindows->fetchAll(\PDO::FETCH_COLUMN, 0);
        }

        $sql_max_participants = "SELECT CASE WHEN SUM(maxTeilnehmer) IS NULL THEN 0 ELSE SUM(maxTeilnehmer) END as maxTeilnehmer FROM zeitfenster WHERE FIND_IN_SET(zeitfensterID, ?)";
        $query_max_participants = $this->query($sql_max_participants, array(implode(",", $ids_timewindows)));
        return (int) $query_max_participants->fetch()["maxTeilnehmer"];
    }

    public function get_participants(?int $event_id = null, ?array $ids_timewindows = null) {
        if ($ids_timewindows === null) {
            if ($event_id === null) {
                throw new \ArgumentCountError("At least one argument must be given and not be null.");
            }
            $query_timewindows = $this->get_timewindows($event_id);
            $ids_timewindows = $query_timewindows->fetchAll(\PDO::FETCH_COLUMN, 0);
        }

        $sql_participants = "SELECT CASE WHEN SUM(anzahl) IS NULL THEN 0 ELSE SUM(anzahl) END as anzahlTeilnehmer FROM teilnehmer WHERE FIND_IN_SET(zeitfensterID, ?)";
        $query_participants = $this->query($sql_participants, array(implode(",", $ids_timewindows)));
        return (int) $query_participants->fetch()["anzahlTeilnehmer"];
    }

    public function get_participant(int $id) {
        $sql_participant = "SELECT
            id,
            nachname AS `lastname`,
            vorname AS `firstname`,
            email,
            anzahl AS `quantity`,
            anmeldestation AS `station`,
            zeitfensterID AS `timewindow_id`,
            eintrag AS `created`,
            bearbeitet AS `edited`
            FROM teilnehmer WHERE id = ?";

        return $this->query($sql_participant, array($id))->fetch();
    }
}
?>