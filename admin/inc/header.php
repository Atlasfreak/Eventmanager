<?php
    include(__DIR__."/../../inc/header_base.php");
    if(session_status() !== PHP_SESSION_ACTIVE and empty($_SESSION)) session_start($session_options ?? []);

    $templates->addFolder("admin_inc", __DIR__."/../templates/include");

    function is_logged_in() {
        if(!isset($_SESSION["registration_username"], $_SESSION["registration_password"])) {
            return false;
        }
        return true;
    }

    function preg_grep_0($pattern, $array) {
        return array_values(preg_grep($pattern, array_keys($array)));
    }
?>