<?php

namespace Atlasfreak\Eventmanager;
include_once(__DIR__."../../inc/db.php");


class Update {
    protected const CACHE_FILE_PATH = __DIR__."\\..\\cache";
    protected const CACHE_FILE_NAME = "version";
    protected const CACHE_LIFETIME = 30 * 60; //30 minutes

    public string $origin = "";

    function __construct(string $origin = "") {
        if ($origin)
            $this->origin = " ".escapeshellarg($origin);
    }

    protected function check_cache() {
        if (!file_exists($this::CACHE_FILE_PATH)) {
            mkdir($this::CACHE_FILE_PATH);
        }
        if (is_file($this::CACHE_FILE_PATH."\\".$this::CACHE_FILE_NAME)) {
            if (filemtime($this::CACHE_FILE_PATH) > (time() - $this::CACHE_LIFETIME)) {
                return file_get_contents($this::CACHE_FILE_PATH);
            } else {
                unlink($this::CACHE_FILE_PATH);
            }
        }
        return false;
    }

    protected function set_cache(string $data) {
        file_put_contents($this::CACHE_FILE_PATH, $data);
    }

    /**
     * Checks wether there is a new version available.
     *
     * @return (string|bool)[] The latest version and wether it is new or not.
     */
    public function check_version() {
        if (!$current_version = $this->check_cache()) {
            $current_version = exec("git describe --tags --abbrev=0");
            $commit = exec("git ls-remote --tags".$this->origin);
            preg_match("/\/(\d.\d.\d)/m", $commit, $match);

            $remote_version = $match[1];
            if (version_compare($current_version, $remote_version, "<")) {
                return [$remote_version, true];
            }
        }
        return [$current_version, false];
    }

    public function update_files() {
        [$version, $new] = $this->check_version();
        if ($new) {
            exec("git fetch".$this->origin);
            exec("git pull".$this->origin);
            exec("git checkout ".$version);
            $this->set_cache($version);
            return true;
        }
        return false;
    }

    public function update() {
        $results = [];
        if ($this->update_files()) {
            $results = $db->update_db();
        }
        return $results;
    }
}
?>