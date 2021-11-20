<?php

class Database {
    private const TABLES = array(
        "admin" => array(
            "username" => "VARCHAR(256) NOT NULL UNIQUE",
            "pass" => "VARCHAR(256) NOT NULL",
            "id" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY"
        ),
        "tage" => array(
            "tagID" => "INT(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (`tagID`)",
            "tagDatum" => "DATE NOT NULL",
            "veranstaltungsId" => "INT(11) NOT NULL",
        ),
        "teilnehmer" => array(
            "ID" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "nachname" => "VARCHAR(512) NOT NULL",
            "vorname" => "VARCHAR(512) NOT NULL",
            "strasse" => "VARCHAR(512) NOT NULL",
            "ort" => "VARCHAR(512) NOT NULL",
            "email" => "VARCHAR(512) NOT NULL",
            "telefon" => "VARCHAR(512) NOT NULL",
            "anzahl" => "INT(11) NOT NULL",
            "zeitfensterID" => "INT(11) NOT NULL",
            "eintrag" => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
            // "Anmeldestation" => "VARCHAR(512) NOT NULL"
        ),
        "zeitfenster" => array(
            "zeitfensterID" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "maxTeilnehmer" => "INT(11) NOT NULL DEFAULT 1, CHECK(maxTeilnehmer > 0)",
            "tagID" => "INT(11) NOT NULL",
            "reihenfolge" => "INT(11) NOT NULL",
            "von" => "TIME NOT NULL",
            "bis" => "TIME, CHECK (von < bis)",
        ),
        "veranstaltungen" => array(
            "id" => "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "beschreibung" => "JSON NOT NULL",
            "titel" => "VARCHAR(512) NOT NULL",
            "anmeldestart" => "DATETIME NOT NULL",
            "anmeldeende" => "DATETIME NOT NULL, CHECK(anmeldeende > anmeldestart)",
            "start" => "DATETIME NOT NULL, CHECK(start > anmeldeende)",
            "ende" => "DATETIME NOT NULL, CHECK(ende > start)",
            "emailVorlage" => "JSON NOT NULL",
        )
    );

    public $mysql, $db_name;

    public function __construct($db_username = "root", $db_password = "", $db_name = "anmeldung"){
        $this->mysql = new PDO("mysql:host=localhost;dbname=".$db_name.";charset=utf8",$db_username,$db_password);
        $this->db_name = $db_name;
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
            $query = $this->mysql->prepare($statement);
            $query->execute();
            if ($query->errorInfo()[0] != 0) {
                throw new Exception($query->errorInfo()[2]);
            }
        }
    }

    public function query($statement, $values = array()) {
        $query = $this->mysql->prepare($statement);
        $query->execute($values);
        if($query->errorInfo()[0] == 0) {
            return $query;
        } else {
            // throw new Exception($query->errorInfo()[2]);
            return null;
        }
    }

    public function insert($table, $values = array()) {
        $statement = "INSERT INTO `".$table."` (";
        foreach ($values as $key => $value) {
            $statement .= "`".$key."`";
            if (array_key_last($values) === $key) {
                $statement .= ")";
            } else {
                $statement .= ", ";
            }
        }
        $statement .= " VALUES (";
        foreach ($values as $key => $value) {
            $statement .= ":".$key;
            if (array_key_last($values) === $key) {
                $statement .= ")";
            } else {
                $statement .= ", ";
            }
        }
        return $this->query($statement, $values);
    }
}
$db = new Database();
?>